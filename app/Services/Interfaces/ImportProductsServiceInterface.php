<?php

namespace App\Services\Interfaces;

use App\Models\ImportBatch;

interface ImportProductsServiceInterface
{
    public function execute(array $data): ImportBatch;

    public function getBatchStatus(int $batchId, int $userId): ?ImportBatch;
}