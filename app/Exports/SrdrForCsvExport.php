<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class SrdrForCsvExport implements FromCollection, WithHeadings, WithMapping
{
    protected $rows;

    public function __construct(Collection $rows)
    {
        $this->rows = $rows;
    }

    public function collection()
    {
        return $this->rows;
    }

    private function getColumnMapping(): array
    {
        // Peta kolom tidak berubah
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

    public function headings(): array
    {
        return array_keys($this->getColumnMapping());
    }

    public function map($row): array
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
                    // Menggunakan fungsi baru yang mengembalikan 0 jika kosong
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
    
    /**
     * FUNGSI BARU: Mengembalikan 0 untuk nilai kosong/non-numerik.
     */
    private function formatNumberOrDefault($value)
    {
        // Jika nilainya valid secara numerik, kembalikan apa adanya.
        if (is_numeric($value)) {
            return $value;
        }
        
        // Jika tidak, kembalikan 0 sebagai nilai default.
        return 0;
    }

    private function transformDate($value): ?string
    {
        // Fungsi ini tidak berubah
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