<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\DolarRef\StoreDolarRefRequest;
use App\Http\Requests\DolarRef\UpdateDolarRefRequest;

use App\Services\DolarRefService;
use Illuminate\Http\JsonResponse;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DolarRefController extends Controller implements HasMiddleware
{
    protected $dolarRefService;

    public static function middleware(): array
    {
        return [
            new Middleware(\App\Http\Middleware\CheckApiKey::class, only: ['store', 'update', 'destroy']),
        ];
    }

    public function __construct(DolarRefService $dolarRefService)
    {
        $this->dolarRefService = $dolarRefService;
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 30);

        $perPage = max(1, min($perPage, 100));

        return response()->json(
            $this->dolarRefService->index($perPage)
        );
    }

    public function getByMonth($yearMonth): JsonResponse
    {
        return response()->json($this->dolarRefService->getByMonth($yearMonth));
    }

    public function getByYear($year): JsonResponse
    {
        return response()->json($this->dolarRefService->getByYear($year));
    }

    public function store(StoreDolarRefRequest $request): JsonResponse
    {
        $data = $request->validated();
        if (array_is_list($data)) {
            $dolarRefs = $this->dolarRefService->storeMany($data);
            return response()->json($dolarRefs, 201);
        }
        $dolarRef = $this->dolarRefService->store($data);
        return response()->json($dolarRef, 201);
    }

    public function show($fecha): JsonResponse
    {
        return response()->json($this->dolarRefService->show($fecha));
    }

    public function update(UpdateDolarRefRequest $request, $fecha): JsonResponse
    {
        $dolarRef = $this->dolarRefService->update($fecha, $request->validated());
        return response()->json($dolarRef);
    }

    public function destroy($fecha): JsonResponse
    {
        $this->dolarRefService->delete($fecha);
        return response()->json(null, 204);
    }
}
