<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\Ufv\StoreUfvRequest;
use App\Http\Requests\Ufv\UpdateUfvRequest;

use App\Services\UfvService;
use Illuminate\Http\JsonResponse;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UfvController extends Controller implements HasMiddleware
{
    protected $ufvService;

    public static function middleware(): array
    {
        return [
            new Middleware(\App\Http\Middleware\CheckApiKey::class, only: ['store', 'update', 'destroy']),
        ];
    }

    public function __construct(UfvService $ufvService)
    {
        $this->ufvService = $ufvService;
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 30);

        $perPage = max(1, min($perPage, 100));

        return response()->json(
            $this->ufvService->index($perPage)
        );
    }

    public function getByMonth($yearMonth): JsonResponse
    {
        return response()->json($this->ufvService->getByMonth($yearMonth));
    }

    public function getByYear($year): JsonResponse
    {
        return response()->json($this->ufvService->getByYear($year));
    }

    public function store(StoreUfvRequest $request): JsonResponse
    {
        $data = $request->validated();
        if (array_is_list($data)) {
            $ufvs = $this->ufvService->storeMany($data);
            return response()->json($ufvs, 201);
        }
        $ufv = $this->ufvService->store($data);
        return response()->json($ufv, 201);
    }

    public function show($fecha): JsonResponse
    {
        return response()->json($this->ufvService->show($fecha));
    }

    public function update(UpdateUfvRequest $request, $fecha): JsonResponse
    {
        $ufv = $this->ufvService->update($fecha, $request->validated());
        return response()->json($ufv);
    }

    public function destroy($fecha): JsonResponse
    {
        $this->ufvService->delete($fecha);
        return response()->json(null, 204);
    }
}
