<?php

namespace App\Imports;

use App\Models\Srdr;
use App\Models\Srr;
// use App\Models\Outlet;
// use App\Models\Promosi;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class AktualImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    protected $promosi_id;
    protected $mode_import;

    public function __construct($promosi_id, $mode_import)
    {
        // Menyimpan promosi_id dari konstruktor.
        // Tidak perlu mencari outlet di sini karena promosi_id sudah cukup.
        $this->promosi_id = $promosi_id;
        $this->mode_import = $mode_import;
    }

    public function model(array $row)
    {
        // Perbaikan: Pastikan semua kolom yang diperlukan ada
        // untuk menghindari error jika file Excel tidak lengkap.
        if (empty($row['sales_number']) || empty($row['menu_code'])) {
            return null; // Lewati baris jika data utama tidak ada
        }

        // Transformasi tanggal untuk sales_date
        $salesDate = $this->transformDate($row['sales_date'] ?? null);

        // Data untuk tabel Srdr
        $srdrData = [
            'promosi_id' => $this->promosi_id, // Perbaikan utama: Menggunakan promosi_id
            'sales_number' => $row['sales_number'] ?? null,
            'sales_date' => $salesDate,
            'sales_dine_in' => $this->transformDate($row['sales_dine_in'] ?? null),
            'sales_dine_out' => $this->transformDate($row['sales_dine_out'] ?? null),
            'branch' => $row['branch'] ?? null,
            'brand' => $row['brand'] ?? null,
            'city' => $row['city'] ?? null,
            'visit_purpose' => $row['visit_purpose'] ?? null,
            'payment_method' => $row['payment_method'] ?? null,
            'menu_category' => $row['menu_category'] ?? null,
            'menu_category_detail' => $row['menu_category_detail'] ?? null,
            'menu' => $row['menu'] ?? null,
            'menu_code' => $row['menu_code'] ?? null,
            'order_mode' => $row['order_mode'] ?? null,
            'qty' => $row['qty'] ?? 0,
            'price' => $row['price'] ?? 0,
            'subtotal' => $row['subtotal'] ?? 0,
            'discount' => $row['discount'] ?? 0,
            'total' => $row['total'] ?? 0,
            'nett_sales' => $row['nett_sales'] ?? 0,
            'bill_discount' => $row['bill_discount'] ?? 0,
            'total_after_bill_discount' => $row['total_after_bill_discount'] ?? 0,
            'waiters' => $row['waiters'] ?? null,
        ];

        // Memasukkan atau memperbarui data Srdr
        Srdr::updateOrCreate(
            [
                'sales_number' => $row['sales_number'],
                'menu_code' => $row['menu_code'],
                'sales_date' => $salesDate,
            ],
            $srdrData
        );

        // Bagian untuk Srr
        if (!empty($row['srr_sales_number'])) {
            $srrSalesDate = $this->transformDate($row['srr_sales_date'] ?? null);
            $srrData = [
                'sales_number' => $row['srr_sales_number'] ?? null,
                'pax' => $row['pax'] ?? 0,
                'grand_total' => $row['srr_grand_total'] ?? 0,
                'sales_date' => $srrSalesDate,
            ];

            Srr::updateOrCreate(
                [
                    'sales_number' => $row['srr_sales_number'],
                    'sales_date' => $srrSalesDate, // Perbaikan: Gunakan sales_date sebagai kunci
                ],
                $srrData
            );
        }

        return null;
    }

    private function transformDate($value)
    {
        if (empty($value)) return null;

        try {
            return is_numeric($value)
                ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d H:i:s')
                : Carbon::parse($value)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            // Sebaiknya Anda mencatat error di sini untuk debugging,
            // daripada hanya mengembalikan null.
            // \Log::warning("Gagal mengonversi tanggal: " . $value . " - " . $e->getMessage());
            return null;
        }
    }

    public function chunkSize(): int { return 5000; }
    public function batchSize(): int { return 100; }
}