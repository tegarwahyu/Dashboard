<?php

namespace App\Imports;

use App\Models\Srdr; // <-- Gunakan model Srdr
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts; // <-- Trait untuk insert massal
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class SrdrImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    public function headingRow(): int
    {
        return 12;
    }

    /**
     * Mengubah setiap baris dari Excel menjadi sebuah instance Model.
     * Laravel Excel akan mengumpulkannya secara otomatis.
     */
    public function model(array $row)
    {
        $cleanRow = $this->cleanRow($row);

        // Validasi: Skip baris jika data kunci tidak ada
        if (empty($cleanRow) || empty($cleanRow['sales_number'])) {
            return null; // Mengembalikan null akan membuat baris ini diabaikan
        }

        // Cukup kembalikan instance model baru.
        // Laravel Excel akan menanganinya dari sini.
        return new Srdr([
            'sales_number'              => $cleanRow['sales_number'],
            'sales_date'                => $this->transformDate($cleanRow['sales_date']),
            'sales_date_in'             => $this->transformDate($cleanRow['sales_date_in']),
            'sales_date_out'            => $this->transformDate($cleanRow['sales_date_out']),
            'branch'                    => $cleanRow['branch'],
            'brand'                     => $cleanRow['brand'],
            'city'                      => $cleanRow['city'],
            'visit_purpose'             => $cleanRow['visit_purpose'],
            'payment_method'            => $cleanRow['payment_method'],
            'menu_category'             => $cleanRow['menu_category'],
            'menu_category_detail'      => $cleanRow['menu_category_detail'],
            'menu'                      => $cleanRow['menu'],
            'menu_code'                 => $cleanRow['menu_code'],
            'order_mode'                => $cleanRow['order_mode'],
            'qty'                       => $cleanRow['qty'],
            'price'                     => $cleanRow['price'],
            'subtotal'                  => $cleanRow['subtotal'],
            'discount'                  => $cleanRow['discount'],
            'total'                     => $cleanRow['total'],
            'nett_sales'                => $cleanRow['nett_sales'],
            'bill_discount'             => $cleanRow['bill_discount'],
            'total_after_bill_discount' => $cleanRow['total_after_bill_discount'],
            'waiters'                   => $cleanRow['waiter'], // Perhatikan potensi typo
            'tax'                       => $cleanRow['tax'],
            'service_charge'            => $cleanRow['service_charge'],
        ]);
    }

    /**
     * Ukuran batch untuk insert ke database.
     */
    public function batchSize(): int
    {
        return 1000; // Insert setiap 1000 baris
    }

    /**
     * Ukuran chunk untuk membaca file Excel.
     * Sebaiknya sama atau kelipatan dari batchSize.
     */
    public function chunkSize(): int
    {
        return 1000;
    }

    // Helper function untuk membersihkan baris
    private function cleanRow(array $row): ?array
    {
        $cleaned = array_map(function ($value) {
            return is_string($value) ? (trim($value) === '' ? null : trim($value)) : $value;
        }, $row);
        
        return array_filter($cleaned) ? $cleaned : null;
    }
    
    // Helper function untuk mengubah tanggal
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