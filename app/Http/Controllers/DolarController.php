<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\Dolar\StoreDolarRequest;
use App\Http\Requests\Dolar\UpdateDolarRequest;

use App\Services\DolarService;
use Illuminate\Http\JsonResponse;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DolarController extends Controller implements HasMiddleware
{
    protected $dolarService;

    public static function middleware(): array
    {
        return [
            new Middleware(\App\Http\Middleware\CheckApiKey::class, only: ['store', 'update', 'destroy']),
        ];
    }

    public function __construct(DolarService $dolarService)
    {
        $this->dolarService = $dolarService;
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 30);

        $perPage = max(1, min($perPage, 100));

        return response()->json(
            $this->dolarService->index($perPage)
        );
    }

    public function getByMonth($yearMonth): JsonResponse
    {
        return response()->json($this->dolarService->getByMonth($yearMonth));
    }

    public function getByYear($year): JsonResponse
    {
        return response()->json($this->dolarService->getByYear($year));
    }

    public function store(StoreDolarRequest $request): JsonResponse
    {
        $data = $request->validated();
        if (array_is_list($data)) {
            $dolars = $this->dolarService->storeMany($data);
            return response()->json($dolars, 201);
        }
        $dolar = $this->dolarService->store($data);
        return response()->json($dolar, 201);
    }

    public function show($fecha): JsonResponse
    {
        return response()->json($this->dolarService->show($fecha));
    }

    public function update(UpdateDolarRequest $request, $fecha): JsonResponse
    {
        $dolar = $this->dolarService->update($fecha, $request->validated());
        return response()->json($dolar);
    }

    public function destroy($fecha): JsonResponse
    {
        $this->dolarService->delete($fecha);
        return response()->json(null, 204);
    }
}
