<?php

namespace App\Repositories\Import;

use App\Models\ImportBatch;

class EloquentImportBatchRepository implements ImportBatchRepositoryInterface
{
    public function create(array $data): ImportBatch
    {
        return ImportBatch::query()->create($data);
    }

    public function findById(int $id): ?ImportBatch
    {
        return ImportBatch::query()->find($id);
    }

    public function incrementProcessed(int $batchId, int $by = 1): void
    {
        ImportBatch::query()->where('id', $batchId)->increment('processed_rows', $by);
    }

    public function incrementFailed(int $batchId, int $by = 1): void
    {
        ImportBatch::query()->where('id', $batchId)->increment('failed_rows', $by);
    }

    public function updateStatus(ImportBatch $batch, string $status): ImportBatch
    {
        $batch->status = $status;
        $batch->save();

        return $batch->fresh();
    }
}