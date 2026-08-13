<?php

namespace App\Services;

use App\Models\Dolar;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class DolarService
{
    public function index(int $perPage = 25): LengthAwarePaginator
    {
        return Dolar::select('fecha', 'precio_compra', 'precio_venta')
            ->orderBy('fecha', 'desc')
            ->paginate($perPage);
    }

    public function store(array $data): Dolar
    {
        return Dolar::create($data);
    }

    public function storeMany(array $data): Collection
    {
        $now = now();
        foreach ($data as &$item) {
            $item['created_at'] = $now;
            $item['updated_at'] = $now;
        }
        Dolar::insert($data);
        return Dolar::whereIn('fecha', array_column($data, 'fecha'))->get();
    }

    public function getByMonth(string $yearMonth): Collection
    {
        [$year, $month] = explode('-', $yearMonth);
        return Dolar::whereYear('fecha', $year)
            ->whereMonth('fecha', $month)
            ->get();
    }

    public function getByYear(string $year): Collection
    {
        return Dolar::whereYear('fecha', $year)->get();
    }

    public function show($fecha): Dolar
    {
        return Dolar::where('fecha', $fecha)->first() ?? new Dolar([
            'fecha' => $fecha,
            'precio_compra' => 0,
            'precio_venta' => 0
        ]);
    }

    public function update($fecha, array $data): Dolar
    {
        $dolar = Dolar::where('fecha', $fecha)->firstOrFail();
        $dolar->update($data);
        return $dolar;
    }

    public function delete($fecha): ?bool
    {
        $dolar = Dolar::where('fecha', $fecha)->firstOrFail();
        return $dolar->delete();
    }
}
