<?php

namespace App\Http\Controllers\Api\Import;

use App\Http\Controllers\Controller;
use App\Http\Requests\Import\ImportProductsRequest;
use App\Services\Interfaces\ImportServiceInterface;
use Illuminate\Http\JsonResponse;


class ImportController extends Controller
{
    public function __construct(
        private readonly ImportServiceInterface $importService
    ) {
    }

    public function import(
        ImportProductsRequest $request
    ): JsonResponse {
        try {
            $batch = $this->importService->execute([
                'user_id' => (int) auth()->id(),
                'file' => $request->file('file'),
            ]);

            return response()->json([
                'data' => $batch,
                'message' => 'Import basariyla baslatildi',
            ], 202);
        } catch (\Throwable $e) {
            return $this->handleException($e, 'ImportController@import');
        }
    }

    public function status(int $batchId): JsonResponse
    {
        try {
            $batch = $this->importService->getBatchForUser($batchId, (int) auth()->id());

            if ($batch === null) {
                return response()->json(['error' => ['message' => 'Batch bulunamadi']], 404);
            }

            if ($batch === false) {
                return response()->json(['error' => ['message' => 'Unauthorized']], 403);
            }

            return response()->json(['data' => $batch], 200);
        } catch (\Throwable $e) {
            return $this->handleException($e, 'ImportController@status');
        }
    }
}
