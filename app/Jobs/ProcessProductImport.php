<?php

namespace App\Jobs;

use App\Repositories\Interfaces\ImportBatchRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\VariantRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessProductImport implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 120;

    public function __construct(
        public int $batchId,
        public string $filePath
    ) {}

    public function handle(
        ImportBatchRepositoryInterface $batchRepository,
        ProductRepositoryInterface $productRepository,
        VariantRepositoryInterface $variantRepository
    ): void {
        $batch = $batchRepository->findById($this->batchId);

        if (!$batch) {
            Log::error('Import batch not found', ['batch_id' => $this->batchId]);
            return;
        }

        $batchRepository->updateStatus($batch, 'PROCESSING');

        $fullPath = Storage::path($this->filePath);
        $fh = @fopen($fullPath, 'r');

        if (!$fh) {
            Log::error('Import file not readable', ['batch_id' => $this->batchId, 'path' => $fullPath]);
            $batchRepository->updateStatus($batch, 'FAILED');
            return;
        }

        try {
            $header = fgetcsv($fh);
            if (!$header || count($header) < 2) {
                throw new \RuntimeException('Invalid CSV header');
            }

            $rowIndex = 0;
            while (($row = fgetcsv($fh)) !== false) {
                $rowIndex++;

            
                if (count(array_filter($row, fn($v) => $v !== null && $v !== '')) === 0) {
                    continue;
                }

                
                if (count($row) !== count($header)) {
                    Log::warning('CSV column mismatch', [
                        'batch_id' => $this->batchId,
                        'row' => $rowIndex,
                        'header_count' => count($header),
                        'row_count' => count($row),
                    ]);
                    $batchRepository->incrementFailed($batch->id);
                    continue;
                }

                try {
                    $data = array_combine($header, $row);

                    $name = trim((string) ($data['name'] ?? ''));
                    $sku = trim((string) ($data['sku'] ?? ''));

                    if ($name === '' || $sku === '') {
                        throw new \RuntimeException('Missing required fields: name/sku');
                    }

                    
                    $existingVariant = $variantRepository->findBySku($sku);

                    if ($existingVariant) {
                       
                        $variantRepository->update($existingVariant, [
                            'price' => (string) ($data['price'] ?? $existingVariant->price),
                            'stock_quantity' => (int) ($data['stock_quantity'] ?? $existingVariant->stock_quantity),
                        ]);

                       
                        if ($existingVariant->product) {
                            $productRepository->update($existingVariant->product, [
                                'name' => $name,
                                'description' => $data['description'] ?? null,
                            ]);
                        }
                    } else {
                        
                        $product = $productRepository->create([
                            'name' => $name,
                            'description' => $data['description'] ?? null,
                            'status' => isset($data['status']) ? (bool) $data['status'] : true,
                        ]);

                        $attrs = [];
                        if (!empty($data['attributes'])) {
                            $decoded = json_decode((string) $data['attributes'], true);
                            $attrs = is_array($decoded) ? $decoded : [];
                        }

                        $variantRepository->create([
                            'product_id' => $product->id,
                            'sku' => $sku,
                            'price' => (string) ($data['price'] ?? '0.00'),
                            'stock_quantity' => (int) ($data['stock_quantity'] ?? 0),
                            'attributes' => $attrs,
                            'status' => true,
                        ]);
                    }

                    $batchRepository->incrementProcessed($batch->id);
                } catch (\Throwable $e) {
                    Log::error('Failed to import row', [
                        'batch_id' => $this->batchId,
                        'row' => $rowIndex,
                        'error' => $e->getMessage(),
                    ]);
                    $batchRepository->incrementFailed($batch->id);
                }
            }

            $batchRepository->updateStatus($batch, 'COMPLETED');
            Storage::delete($this->filePath);
        } catch (\Throwable $e) {
            Log::error('Import batch failed', [
                'batch_id' => $this->batchId,
                'error' => $e->getMessage(),
            ]);
            $batchRepository->updateStatus($batch, 'FAILED');
        } finally {
            fclose($fh);
        }
    }
}