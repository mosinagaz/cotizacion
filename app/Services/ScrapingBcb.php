<?php

namespace App\Services;

use App\Models\Dolar;
use App\Models\DolarRef;
use App\Models\Ufv;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ScrapingBcb
{
    private const URL = 'https://www.bcb.gob.bo/';

    private ?string $html = null;

    private ?DOMXPath $xpath = null;

    private ?int $lastHttpStatus = null;

    private ?string $lastHttpBody = null;

    /**
     * Obtiene los indicadores del BCB y los guarda en las tablas correspondientes.
     *
     * @return array{ufv: Ufv, dolar: Dolar, dolar_referencial: DolarRef}
     */
    public function guardarDatos(): array
    {
        $ufv = $this->fetchUfv();
        $dolar = $this->fetchDolar();
        //$dolarReferencial = $this->fetchDolarReferencial();

        return [
            'ufv' => Ufv::updateOrCreate(
                ['fecha' => $ufv['fecha']],
                ['valor' => $ufv['valor']]
            ),
            'dolar' => Dolar::updateOrCreate(
                ['fecha' => $dolar['fecha']],
                [
                    'precio_compra' => $dolar['precio_compra'],
                    'precio_venta' => $dolar['precio_venta'],
                ]
            )/*,
            'dolar_referencial' => DolarRef::updateOrCreate(
                ['fecha' => $dolarReferencial['fecha']],
                [
                    'precio_compra' => $dolarReferencial['precio_compra'],
                    'precio_venta' => $dolarReferencial['precio_venta'],
                ]
            ),*/
        ];
    }

    /**
     * @return array{url: string, http_status: int|null, body: string|null}
     */
    public function getLastResponseContext(): array
    {
        return [
            'url' => self::URL,
            'http_status' => $this->lastHttpStatus,
            'body' => $this->lastHttpBody !== null ? $this->truncateBody($this->lastHttpBody) : null,
        ];
    }

    /**
     * @return array{fecha: string, valor: float}
     */
    public function fetchUfv(): array
    {
        $card = $this->findCard('Unidad de fomento a la vivienda');

        $valorNode = $this->xpath()->query(
            ".//div[contains(@class,'bcb-single')]//span[contains(@class,'bcb-val-money')]",
            $card
        )->item(0);

        if ($valorNode === null) {
            throw new RuntimeException('No se pudo obtener el valor de la UFV desde el BCB.');
        }

        return [
            'fecha' => $this->parseCardDate($card),
            'valor' => $this->parseDecimal($valorNode->textContent),
        ];
    }

    /**
     * @return array{fecha: string, precio_compra: float, precio_venta: float}
     */
    public function fetchDolar(): array
    {
        $card = $this->findCard('Tipo de cambio oficial');

        $valorNode = $this->xpath()->query(
            ".//span[contains(@class,'bcb-tco-num')]",
            $card
        )->item(0);

        if ($valorNode !== null) {
            $valor = $this->parseDecimal($valorNode->textContent);

            return [
                'fecha' => $this->parseCardDate($card),
                'precio_compra' => $valor,
                'precio_venta' => $valor,
            ];
        }

        $precios = $this->parseCompraVenta($card);

        return [
            'fecha' => $this->parseCardDate($card),
            'precio_compra' => $precios['compra'],
            'precio_venta' => $precios['venta'],
        ];
    }

    /**
     * @return array{fecha: string, precio_compra: float, precio_venta: float}
     */
    public function fetchDolarReferencial(): array
    {
        $card = $this->findCard('Valor referencial del dólar estadounidense');
        $precios = $this->parseCompraVenta($card);

        return [
            'fecha' => $this->parseCardDate($card),
            'precio_compra' => $precios['compra'],
            'precio_venta' => $precios['venta'],
        ];
    }

    private function xpath(): DOMXPath
    {
        if ($this->xpath !== null) {
            return $this->xpath;
        }

        libxml_use_internal_errors(true);

        $dom = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8">'.$this->fetchHtml(), LIBXML_NOWARNING | LIBXML_NOERROR);

        $this->xpath = new DOMXPath($dom);

        return $this->xpath;
    }

    private function fetchHtml(): string
    {
        if ($this->html !== null) {
            return $this->html;
        }

        $response = Http::timeout(30)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (compatible; CotizacionesBot/1.0)',
                'Accept' => 'text/html,application/xhtml+xml',
            ])
            ->get(self::URL);

        $this->lastHttpStatus = $response->status();
        $this->lastHttpBody = $response->body();

        if (! $response->successful()) {
            throw new RuntimeException(
                'No se pudo acceder a la página del BCB. Código HTTP: '.$response->status()
            );
        }

        $this->html = $this->lastHttpBody;

        return $this->html;
    }

    private function truncateBody(string $body, int $max = 3000): string
    {
        if (strlen($body) <= $max) {
            return $body;
        }

        return substr($body, 0, $max).'... [truncado]';
    }

    private function findCard(string $nameContains): DOMElement
    {
        $cards = $this->xpath()->query("//article[contains(@class,'bcb-kpi2-card')]");

        foreach ($cards as $card) {
            if (! $card instanceof DOMElement) {
                continue;
            }

            $nameNode = $this->xpath()->query(".//p[contains(@class,'bcb-kpi2-name')]", $card)->item(0);
            $name = trim($nameNode?->textContent ?? '');

            if ($name !== '' && str_contains($name, $nameContains)) {
                return $card;
            }
        }

        throw new RuntimeException("No se encontró el indicador '{$nameContains}' en la página del BCB.");
    }

    /**
     * @return array{compra: float, venta: float}
     */
    private function parseCompraVenta(DOMElement $card): array
    {
        $compra = null;
        $venta = null;

        $rows = $this->xpath()->query(".//div[contains(@class,'bcb-row')]", $card);

        foreach ($rows as $row) {
            if (! $row instanceof DOMElement) {
                continue;
            }

            $label = trim($this->xpath()->query(".//div[contains(@class,'bcb-lbl')]", $row)->item(0)?->textContent ?? '');
            $value = trim($this->xpath()->query(".//div[contains(@class,'bcb-val')]", $row)->item(0)?->textContent ?? '');

            if ($label === '' || $value === '') {
                continue;
            }

            if (strcasecmp($label, 'Compra') === 0) {
                $compra = $this->parseDecimal($value);
            }

            if (strcasecmp($label, 'Venta') === 0) {
                $venta = $this->parseDecimal($value);
            }
        }

        if ($compra === null || $venta === null) {
            throw new RuntimeException('No se pudieron obtener los precios de compra y venta desde el BCB.');
        }

        return [
            'compra' => $compra,
            'venta' => $venta,
        ];
    }

    private function parseCardDate(DOMElement $card): string
    {
        $timeNode = $this->xpath()->query('.//time[@datetime]', $card)->item(0);

        if ($timeNode instanceof DOMElement) {
            $datetime = trim($timeNode->getAttribute('datetime'));

            if ($datetime !== '') {
                return $datetime;
            }
        }

        throw new RuntimeException('No se pudo obtener la fecha del indicador en la página del BCB.');
    }

    private function parseDecimal(string $raw): float
    {
        $value = trim(preg_replace('/[^\d,.-]/', '', $raw) ?? '');

        if ($value === '') {
            throw new RuntimeException("No se pudo interpretar el valor numérico '{$raw}'.");
        }

        if (str_contains($value, ',') && str_contains($value, '.')) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } elseif (str_contains($value, ',')) {
            $value = str_replace(',', '.', $value);
        }

        return (float) $value;
    }
}
