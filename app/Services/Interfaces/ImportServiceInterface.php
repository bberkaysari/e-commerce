<?php

namespace App\Services\Interfaces;

interface ImportServiceInterface
{
    /**
     * @param array{
     *     user_id: int,
     *     file: \Illuminate\Http\UploadedFile
     * } $data
     */
    public function execute(array $data);

    public function getBatchForUser(int $batchId, int $userId);
}