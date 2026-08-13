<?php

namespace App\Console\Commands;

use App\Services\ScrapingBcb;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GuardarDatosBcbCommand extends Command
{
    protected $signature = 'bcb:guardar-datos';

    protected $description = 'Obtiene UFV, dólar y dólar referencial del BCB y los guarda en la base de datos';

    public function handle(ScrapingBcb $scrapingBcb): int
    {
        $this->info('Obteniendo datos del BCB...');

        Log::info('Inicio de sincronización de datos del BCB');

        try {
            $datos = $scrapingBcb->guardarDatos();
        } catch (\Throwable $exception) {
            Log::error('Error al guardar datos del BCB', [
                'message' => $exception->getMessage(),
                'exception' => $exception::class,
                'response' => $scrapingBcb->getLastResponseContext(),
            ]);

            $this->error('Error al guardar datos del BCB: '.$exception->getMessage());

            return self::FAILURE;
        }

        $registro = [
            'ufv' => [
                'fecha' => $datos['ufv']->fecha,
                'valor' => $datos['ufv']->valor,
            ],
            'dolar' => [
                'fecha' => $datos['dolar']->fecha,
                'precio_compra' => $datos['dolar']->precio_compra,
                'precio_venta' => $datos['dolar']->precio_venta,
            ]
        ];

        Log::info('Datos del BCB guardados correctamente', $registro);

        $this->table(
            ['Indicador', 'Fecha', 'Compra / Valor', 'Venta'],
            [
                [
                    'UFV',
                    $registro['ufv']['fecha'],
                    $registro['ufv']['valor'],
                    '-',
                ],
                [
                    'Dólar',
                    $registro['dolar']['fecha'],
                    $registro['dolar']['precio_compra'],
                    $registro['dolar']['precio_venta'],
                ]
            ]
        );

        $this->info('Datos guardados correctamente.');

        return self::SUCCESS;
    }
}
