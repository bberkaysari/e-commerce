<?php

namespace App\Services\Import;

use App\Exceptions\DomainException;
use App\Jobs\ProcessProductImport;
use App\Repositories\Import\ImportBatchRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class ImportProductsService
{
    public function __construct(
        private readonly ImportBatchRepositoryInterface $batchRepository
    ) {}

    /**
     * payload:
     * - user_id (int)
     * - file (UploadedFile)
     */
    public function execute(array $data)
    {
        $userId = (int) $data['user_id'];
        /** @var UploadedFile $file */
        $file = $data['file'];

        try {
            $filePath = $file->store('imports');

            // Büyük dosyada satır sayma maliyetli. Şimdilik 0/unknown tut.
            // İstersen stream ile saydırırız.
            $batch = $this->batchRepository->create([
                'user_id' => $userId,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'total_rows' => 0,
                'processed_rows' => 0,
                'failed_rows' => 0,
                'status' => 'PENDING',
            ]);

            ProcessProductImport::dispatch($batch->id, $filePath);

            return $batch;
        } catch (\Throwable $e) {
            Log::error('Import start failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            throw new DomainException('Unexpected error while starting import', 500);
        }
    }
}