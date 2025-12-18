<?php

namespace App\Repositories\Interfaces;

use App\Models\ImportBatch;

interface ImportBatchRepositoryInterface
{
    public function create(array $data): ImportBatch;

    public function findById(int $id): ?ImportBatch;

    public function incrementProcessed(int $batchId, int $by = 1): void;

    public function incrementFailed(int $batchId, int $by = 1): void;

    public function updateStatus(ImportBatch $batch, string $status): ImportBatch;
}
