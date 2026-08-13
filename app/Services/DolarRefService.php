<?php

namespace App\Services;

use App\Models\DolarRef;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class DolarRefService
{
    public function index(int $perPage = 25): LengthAwarePaginator
    {
        return DolarRef::select('fecha', 'precio_compra', 'precio_venta')
            ->orderBy('fecha', 'desc')
            ->paginate($perPage);
    }

    public function store(array $data): DolarRef
    {
        return DolarRef::create($data);
    }

    public function storeMany(array $data): Collection
    {
        $now = now();
        foreach ($data as &$item) {
            $item['created_at'] = $now;
            $item['updated_at'] = $now;
        }
        DolarRef::insert($data);
        return DolarRef::whereIn('fecha', array_column($data, 'fecha'))->get();
    }

    public function getByMonth(string $yearMonth): Collection
    {
        [$year, $month] = explode('-', $yearMonth);
        return DolarRef::whereYear('fecha', $year)
            ->whereMonth('fecha', $month)
            ->get();
    }

    public function getByYear(string $year): Collection
    {
        return DolarRef::whereYear('fecha', $year)->get();
    }

    public function show($fecha): DolarRef
    {
        return DolarRef::where('fecha', $fecha)->first() ?? new DolarRef([
            'fecha' => $fecha,
            'precio_compra' => 0,
            'precio_venta' => 0
        ]);
    }

    public function update($fecha, array $data): DolarRef
    {
        $dolarRef = DolarRef::where('fecha', $fecha)->firstOrFail();
        $dolarRef->update($data);
        return $dolarRef;
    }

    public function delete($fecha): ?bool
    {
        $dolarRef = DolarRef::where('fecha', $fecha)->firstOrFail();
        return $dolarRef->delete();
    }
}
