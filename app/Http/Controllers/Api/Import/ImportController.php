<?php

namespace App\Http\Controllers\Api\Import;

use App\Http\Controllers\Controller;
use App\Http\Requests\Import\ImportProductsRequest;
use App\Repositories\Import\ImportBatchRepositoryInterface;
use App\Services\Import\ImportProductsService;
use Illuminate\Http\JsonResponse;

class ImportController extends Controller
{
    public function import(
        ImportProductsRequest $request,
        ImportProductsService $service
    ): JsonResponse {
        $batch = $service->execute([
            'user_id' => (int) auth()->id(),
            'file' => $request->file('file'),
        ]);

        return response()->json([
            'data' => $batch,
            'message' => 'Import started successfully',
        ], 202);
    }

    public function status(int $batchId, ImportBatchRepositoryInterface $batchRepository): JsonResponse
    {
        $batch = $batchRepository->findById($batchId);

        if (!$batch) {
            return response()->json(['error' => ['message' => 'Batch not found']], 404);
        }

        // opsiyonel: sadece kendi batch’ini görebilsin
        if ((int) $batch->user_id !== (int) auth()->id()) {
            return response()->json(['error' => ['message' => 'Unauthorized']], 403);
        }

        return response()->json(['data' => $batch], 200);
    }
}