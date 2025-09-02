<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Row;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class SrdrStreamConverter implements OnEachRow, WithStartRow, WithChunkReading, WithCalculatedFormulas
{
    private $fileHandle;
    private int $processedRowCount = 0;
    private array $uniqueKeys = [];

    public function __construct($fileHandle)
    {
        $this->fileHandle = $fileHandle;
    }

    /**
     * Method ini akan dipanggil untuk setiap baris di file Excel.
     * Ini adalah versi final yang sudah digabungkan.
     */
    public function onRow(Row $row)
    {
        $rowData = $row->toArray();

        // 1. Validasi: Hanya proses baris yang memiliki sales_number
        $salesNumber = trim($rowData[0] ?? '');
        if (empty($salesNumber)) {
            return; // Lewati baris ini jika sales_number kosong
        }

        // 2. Kumpulkan kunci unik (untuk proses Ganti Data)
        $this->collectUniqueKeys($rowData);

        // 3. Petakan dan bersihkan data untuk ditulis ke CSV
        $mappedData = $this->map($rowData);

        // 4. Langsung tulis baris yang sudah diproses ke file CSV
        fputcsv($this->fileHandle, $mappedData);

        // 5. Tambah penghitung baris
        $this->processedRowCount++;
    }

    public function startRow(): int
    {
        return 13;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function getProcessedRowCount(): int
    {
        return $this->processedRowCount;
    }

    /**
     * Mengumpulkan kombinasi unik dari branch, brand, dan sales_date.
     */
    private function collectUniqueKeys(array $row)
    {
        $branch = trim($row[9] ?? null);
        $brand = trim($row[10] ?? null);
        $salesDate = $this->transformDate($row[6] ?? null);

        if ($branch && $brand && $salesDate) {
            $dateOnly = substr($salesDate, 0, 10);
            $key = $branch . '|' . $brand . '|' . $dateOnly;
            
            if (!isset($this->uniqueKeys[$key])) {
                $this->uniqueKeys[$key] = [
                    'branch' => $branch,
                    'brand' => $brand,
                    'sales_date' => $dateOnly,
                ];
            }
        }
    }

    /**
     * Mengembalikan array berisi kunci-kunci unik yang sudah dikumpulkan.
     */
    public function getUniqueKeys(): array
    {
        return array_values($this->uniqueKeys);
    }
    
    /**
     * Peta kolom sebagai satu-satunya sumber kebenaran.
     */
    private function getColumnMapping(): array
    {
        return [
            'sales_number'              => ['index' => 0,  'type' => 'string'],
            'sales_date'                => ['index' => 6,  'type' => 'date'],
            'sales_date_in'             => ['index' => 7,  'type' => 'date'],
            'sales_date_out'            => ['index' => 8,  'type' => 'date'],
            'branch'                    => ['index' => 9,  'type' => 'string'],
            'brand'                     => ['index' => 10, 'type' => 'string'],
            'city'                      => ['index' => 11, 'type' => 'string'],
            'visit_purpose'             => ['index' => 13, 'type' => 'string'],
            'payment_method'            => ['index' => 24, 'type' => 'string'],
            'menu_category'             => ['index' => 25, 'type' => 'string'],
            'menu_category_detail'      => ['index' => 26, 'type' => 'string'],
            'menu'                      => ['index' => 27, 'type' => 'string'],
            'menu_code'                 => ['index' => 29, 'type' => 'string'],
            'order_mode'                => ['index' => 31, 'type' => 'string'],
            'qty'                       => ['index' => 32, 'type' => 'number'],
            'price'                     => ['index' => 33, 'type' => 'number'],
            'subtotal'                  => ['index' => 34, 'type' => 'number'],
            'discount'                  => ['index' => 35, 'type' => 'number'],
            'total'                     => ['index' => 39, 'type' => 'number'],
            'nett_sales'                => ['index' => 40, 'type' => 'number'],
            'bill_discount'             => ['index' => 41, 'type' => 'number'],
            'total_after_bill_discount' => ['index' => 42, 'type' => 'number'],
            'waiters'                   => ['index' => 43, 'type' => 'string'],
            'tax'                       => ['index' => 37, 'type' => 'number'],
            'service_charge'            => ['index' => 36, 'type' => 'number'],
            'created_at'                => ['index' => -1, 'type' => 'timestamp'],
            'updated_at'                => ['index' => -1, 'type' => 'timestamp'],
        ];
    }

    public function getHeadings(): array
    {
        return array_keys($this->getColumnMapping());
    }

    private function map(array $row): array
    {
        $mappedRow = [];
        $now = now()->toDateTimeString();

        foreach ($this->getColumnMapping() as $dbColumn => $details) {
            $value = ($details['index'] >= 0) ? ($row[$details['index']] ?? null) : null;
            switch ($details['type']) {
                case 'date':
                    $mappedRow[] = $this->transformDate($value);
                    break;
                case 'number':
                    $mappedRow[] = $this->formatNumberOrDefault($value);
                    break;
                case 'timestamp':
                    $mappedRow[] = $now;
                    break;
                case 'string':
                default:
                    $mappedRow[] = $value;
                    break;
            }
        }
        return $mappedRow;
    }
    
    private function formatNumberOrDefault($value)
    {
        if (is_numeric($value)) {
            return $value;
        }
        return 0;
    }

    private function transformDate($value): ?string
    {
        if (!$value) return null;
        try {
            if (is_numeric($value)) {
                return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d H:i:s');
            }
            $ts = strtotime($value);
            return $ts ? date('Y-m-d H:i:s', $ts) : null;
        } catch (\Exception $e) {
            return null;
        }
    }
}