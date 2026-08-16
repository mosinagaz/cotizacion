<?php

namespace App\Http\Controllers;

use App\Models\Dolar;
use App\Models\SiteCounter;
use App\Models\Ufv;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $ufv = Ufv::query()->orderByDesc('fecha')->first();
        $dolar = Dolar::query()->orderByDesc('fecha')->first();

        $ufvs = Ufv::query()->orderByDesc('fecha')->limit(10)->get()->keyBy('fecha');
        $dolares = Dolar::query()->orderByDesc('fecha')->limit(10)->get()->keyBy('fecha');

        $historico = $ufvs->keys()
            ->merge($dolares->keys())
            ->unique()
            ->sortDesc()
            ->take(10)
            ->map(fn (string $fecha) => [
                'fecha' => $fecha,
                'ufv' => $ufvs->get($fecha)?->valor,
                'compra' => $dolares->get($fecha)?->precio_compra,
                'venta' => $dolares->get($fecha)?->precio_venta,
            ]);

        if (! session()->has('counted_visit')) {
            $visitas = SiteCounter::incrementVisits();
            session()->put('counted_visit', true);
        } else {
            $visitas = SiteCounter::visits();
        }

        return view('api', [
            'ufv' => $ufv,
            'dolar' => $dolar,
            'historico' => $historico,
            'baseUrl' => url('/api'),
            'visitas' => $visitas,
        ]);
    }
}
