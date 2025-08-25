<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class SrdrImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    /**
     * Buffer untuk data SRDR sebelum di-insert/update ke database.
     * @var array
     */
    private $srdrBuffer = [];

    /**
     * Batasan jumlah baris dalam buffer sebelum data di-flush ke database.
     * @var int
     */
    private $chunkLimit = 1000;

    /**
     * Menetapkan baris header.
     * @return int
     */
    public function headingRow(): int
    {
        return 12; // Data dimulai dari baris ke-12
    }

    /**
     * Mengolah setiap chunk data dari file Excel.
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Hilangkan spasi di semua nilai string dan ubah string kosong menjadi null
            $cleanRow = array_map(function ($value) {
                if (is_string($value)) {
                    $value = trim($value);
                    return $value === '' ? null : $value;
                }
                return $value;
            }, $row->toArray());

            // Lewati baris jika semua kolomnya kosong
            if (empty(array_filter($cleanRow))) {
                continue;
            }

            // Memastikan kolom sales_number tidak kosong sebelum buffering
            if (!empty($cleanRow['sales_number'])) {
                $this->srdrBuffer[] = [
                    'sales_number' => $cleanRow['sales_number'],
                    'sales_date' => $this->transformDate($cleanRow['sales_date']),
                    'sales_date_in' => $this->transformDate($cleanRow['sales_date_in']),
                    'sales_date_out' => $this->transformDate($cleanRow['sales_date_out']),
                    'branch' => $cleanRow['branch'],
                    'brand' => $cleanRow['brand'],
                    'city' => $cleanRow['city'],
                    'visit_purpose' => $cleanRow['visit_purpose'],
                    'payment_method' => $cleanRow['payment_method'],
                    'menu_category' => $cleanRow['menu_category'],
                    'menu_category_detail' => $cleanRow['menu_category_detail'],
                    'menu' => $cleanRow['menu'],
                    'menu_code' => $cleanRow['menu_code'],
                    'order_mode' => $cleanRow['order_mode'],
                    'qty' => $cleanRow['qty'],
                    'price' => $cleanRow['price'],
                    'subtotal' => $cleanRow['subtotal'],
                    'discount' => $cleanRow['discount'],
                    'total' => $cleanRow['total'],
                    'nett_sales' => $cleanRow['nett_sales'],
                    'bill_discount' => $cleanRow['bill_discount'],
                    'total_after_bill_discount' => $cleanRow['total_after_bill_discount'],
                    'waiters' => $cleanRow['waiter'],
                    'tax' => $cleanRow['tax'],
                    'service_charge' => $cleanRow['service_charge'],
                    'mark_id' => $cleanRow['mark_id'], // Mengambil mark_id dari Excel
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Flush buffer jika sudah mencapai batas
            if (count($this->srdrBuffer) >= $this->chunkLimit) {
                $this->flushSrdr();
            }
        }
    }

    /**
     * Destruktor kelas. Memastikan semua data yang tersisa di buffer
     * di-flush ke database saat impor selesai.
     */
    public function __destruct()
    {
        $this->flushSrdr();
    }

    /**
     * Memproses data yang ada di buffer dan menyimpannya ke database.
     */
    private function flushSrdr()
    {
        if (empty($this->srdrBuffer)) {
            return;
        }

        DB::transaction(function () {
            // Ambil semua kunci unik dari buffer untuk pengecekan data yang sudah ada
            $keys = collect($this->srdrBuffer)->map(function ($data) {
                return [
                    'sales_number' => $data['sales_number'],
                    'menu_code' => $data['menu_code'],
                    'order_mode' => $data['order_mode'],
                    'price' => $data['price'],
                    'qty' => $data['qty']
                ];
            })->unique()->toArray(); // Pastikan kunci unik

            // Ambil semua record yang sudah ada dalam satu query
            $existingRecords = DB::table('srdr')
                ->where(function ($q) use ($keys) {
                    foreach ($keys as $key) {
                        $q->orWhere(function ($sub) use ($key) {
                            $sub->where('sales_number', $key['sales_number'])
                                ->where('menu_code', $key['menu_code'])
                                ->where('order_mode', $key['order_mode'])
                                ->where('price', $key['price'])
                                ->where('qty', $key['qty']);
                        });
                    }
                })
                ->get()
                ->keyBy(function ($item) {
                    return implode('|', [
                        $item->sales_number,
                        $item->menu_code,
                        $item->order_mode,
                        $item->price,
                        $item->qty
                    ]);
                });

            // Lakukan update atau insert untuk setiap baris di buffer
            foreach ($this->srdrBuffer as $data) {
                $key = implode('|', [
                    $data['sales_number'],
                    $data['menu_code'],
                    $data['order_mode'],
                    $data['price'],
                    $data['qty']
                ]);

                if (isset($existingRecords[$key])) {
                    // Jika data sudah ada, lakukan update
                    DB::table('srdr')->where('id', $existingRecords[$key]->id)->update($data);
                } else {
                    // Jika data belum ada, lakukan insert
                    DB::table('srdr')->insert($data);
                }
            }
        });

        // Kosongkan buffer setelah selesai
        $this->srdrBuffer = [];
    }

    /**
     * Mengubah nilai tanggal dari Excel ke format yang valid.
     * @param mixed $value
     * @return string|null
     */
    private function transformDate($value)
    {
        if (!$value) {
            return null;
        }
        try {
            if (is_numeric($value)) {
                return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d H:i:s');
            }
            return Carbon::parse($value)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Menetapkan ukuran chunk untuk pembacaan file.
     * @return int
     */
    public function chunkSize(): int
    {
        return 1000;
    }
    // cara ke 3 kurang sesuai
    // private $srdrBuffer = [];
    // private $srrBuffer = [];
    // private $chunkLimit = 2000;

    // public function collection(Collection $rows)
    // {
    //     $now = now()->format('Y-m-d H:i:s');

    //     foreach ($rows as $row) {
    //         // Processing for srdr table
    //         if (!empty($row['sales_number'])) {
    //             $rowHash = md5(
    //                 ($row['sales_number'] ?? '') .
    //                 ($row['menu_code'] ?? '') .
    //                 ($row['order_mode'] ?? '') .
    //                 ($row['price'] ?? '') .
    //                 ($row['qty'] ?? '')
    //             );

    //             $this->srdrBuffer[] = [
    //                 'sales_number' => $row['sales_number'],
    //                 'sales_date' => $this->transformDate($row['sales_date']),
    //                 'sales_dine_in' => $this->transformDate($row['sales_dine_in']),
    //                 'sales_dine_out' => $this->transformDate($row['sales_dine_out']),
    //                 'branch' => $row['branch'],
    //                 'brand' => $row['brand'],
    //                 'city' => $row['city'],
    //                 'visit_purpose' => $row['visit_purpose'],
    //                 'payment_method' => $row['payment_method'],
    //                 'menu_category' => $row['menu_category'],
    //                 'menu_category_detail' => $row['menu_category_detail'],
    //                 'menu' => $row['menu'],
    //                 'menu_code' => $row['menu_code'],
    //                 'order_mode' => $row['order_mode'],
    //                 'qty' => $row['qty'],
    //                 'price' => $row['price'],
    //                 'subtotal' => $row['subtotal'],
    //                 'discount' => $row['discount'],
    //                 'total' => $row['total'],
    //                 'nett_sales' => $row['nett_sales'],
    //                 'bill_discount' => $row['bill_discount'],
    //                 'total_after_bill_discount' => $row['total_after_bill_discount'],
    //                 'waiters' => $row['waiters'],
    //                 'created_at' => $now,
    //                 'updated_at' => $now,
    //                 'row_hash' => $rowHash,
    //             ];
    //         }

    //         // Processing for srr table
    //         if (!empty($row['srr_sales_number'])) {
    //             $rowHash = md5(
    //                 ($row['srr_sales_number'] ?? '') .
    //                 ($row['sales_date'] ?? '') .
    //                 ($row['grand_total'] ?? '') .
    //                 ($row['pax'] ?? '')
    //             );

    //             $this->srrBuffer[] = [
    //                 'sales_number' => $row['srr_sales_number'],
    //                 'pax' => $row['pax'],
    //                 'grand_total' => $row['srr_grand_total'],
    //                 'sales_date' => $this->transformDate($row['srr_sales_date']),
    //                 'created_at' => $now,
    //                 'updated_at' => $now,
    //                 'row_hash' => $rowHash,
    //             ];
    //         }

    //         if (count($this->srdrBuffer) >= $this->chunkLimit) {
    //             $this->flushSrdr();
    //         }

    //         if (count($this->srrBuffer) >= $this->chunkLimit) {
    //             $this->flushSrr();
    //         }
    //     }
    // }

    // public function __destruct()
    // {
    //     $this->flushSrdr();
    //     $this->flushSrr();
    // }

    // private function flushSrdr()
    // {
    //     if (empty($this->srdrBuffer)) return;

    //     // Tentukan kolom-kolom yang tidak perlu di-update (dalam kasus ini, tidak ada)
    //     // Dengan mengosongkan array update, upsert akan melakukan INSERT IGNORE
    //     $updateColumns = []; 

    //     // DB::table('srdr')->upsert($this->srdrBuffer, ['row_hash'], $updateColumns);
    //     DB::table('srdr')->upsert($this->srdrBuffer, ['row_hash'], ['updated_at']);
    //     $this->srdrBuffer = [];
    // }

    // private function flushSrr()
    // {
    //     if (empty($this->srrBuffer)) return;
        
    //     $updateColumns = [];

    //     // DB::table('srr')->upsert($this->srrBuffer, ['row_hash'], $updateColumns);
    //     DB::table('srr')->upsert($this->srrBuffer, ['row_hash'], ['updated_at']);
    //     $this->srrBuffer = [];
    // }

    // private function transformDate($value)
    // {
    //     if (!$value) return null;
    //     try {
    //         return is_numeric($value)
    //             ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d H:i:s')
    //             : Carbon::parse($value)->format('Y-m-d H:i:s');
    //     } catch (\Exception $e) {
    //         return null;
    //     }
    // }

    // public function chunkSize(): int
    // {
    //     return 2000;
    // }
        // cara ke 2 
    //     $now = now()->format('Y-m-d H:i:s');

    //     foreach ($rows as $row) {
    //         if (!empty($row['sales_number'])) {
    //             $rowHash = md5(
    //                 ($row['sales_number'] ?? '') .
    //                 ($row['menu_code'] ?? '') .
    //                 ($row['order_mode'] ?? '') .
    //                 ($row['price'] ?? '') .
    //                 ($row['qty'] ?? '')
    //             );

    //             $this->srdrBuffer[] = [
    //                 'sales_number' => $row['sales_number'],
    //                 'sales_date' => $this->transformDate($row['sales_date']),
    //                 'sales_dine_in' => $this->transformDate($row['sales_dine_in']),
    //                 'sales_dine_out' => $this->transformDate($row['sales_dine_out']),
    //                 'branch' => $row['branch'],
    //                 'brand' => $row['brand'],
    //                 'city' => $row['city'],
    //                 'visit_purpose' => $row['visit_purpose'],
    //                 'payment_method' => $row['payment_method'],
    //                 'menu_category' => $row['menu_category'],
    //                 'menu_category_detail' => $row['menu_category_detail'],
    //                 'menu' => $row['menu'],
    //                 'menu_code' => $row['menu_code'],
    //                 'order_mode' => $row['order_mode'],
    //                 'qty' => $row['qty'],
    //                 'price' => $row['price'],
    //                 'subtotal' => $row['subtotal'],
    //                 'discount' => $row['discount'],
    //                 'total' => $row['total'],
    //                 'nett_sales' => $row['nett_sales'],
    //                 'bill_discount' => $row['bill_discount'],
    //                 'total_after_bill_discount' => $row['total_after_bill_discount'],
    //                 'waiters' => $row['waiters'],
    //                 'created_at' => $now,
    //                 'updated_at' => $now,
    //                 'row_hash' => $rowHash,
    //             ];
    //         }

    //         if (!empty($row['srr_sales_number'])) {
    //             $rowHash = md5(
    //                 ($row['srr_sales_number'] ?? '') .
    //                 ($row['sales_date'] ?? '') .
    //                 ($row['grand_total'] ?? '') .
    //                 ($row['pax'] ?? '')
    //             );

    //             $this->srrBuffer[] = [
    //                 'sales_number' => $row['srr_sales_number'],
    //                 'pax' => $row['pax'],
    //                 'grand_total' => $row['srr_grand_total'],
    //                 'sales_date' => $this->transformDate($row['srr_sales_date']),
    //                 'created_at' => $now,
    //                 'updated_at' => $now,
    //                 'row_hash' => $rowHash,
    //             ];
    //         }

    //         if (count($this->srdrBuffer) >= $this->chunkLimit) {
    //             $this->flushSrdr();
    //         }

    //         if (count($this->srrBuffer) >= $this->chunkLimit) {
    //             $this->flushSrr();
    //         }
    //     }
    // }

    // public function __destruct()
    // {
    //     $this->flushSrdr();
    //     $this->flushSrr();
    // }

    // private function flushSrdr()
    // {
    //     if (empty($this->srdrBuffer)) return;

    //     DB::table('srdr')->upsert($this->srdrBuffer, ['row_hash']);
    //     $this->srdrBuffer = [];
    // }

    // private function flushSrr()
    // {
    //     if (empty($this->srrBuffer)) return;

    //     DB::table('srr')->upsert($this->srrBuffer, ['row_hash']);
    //     $this->srrBuffer = [];
    // }

    // private function transformDate($value)
    // {
    //     if (!$value) return null;
    //     try {
    //         return is_numeric($value)
    //             ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d H:i:s')
    //             : Carbon::parse($value)->format('Y-m-d H:i:s');
    //     } catch (\Exception $e) {
    //         return null;
    //     }
    // }

    // public function chunkSize(): int
    // {
    //     return 2000;
    // }
    
    // cara pertama 
    // private $srdrBuffer = [];
    // private $srrBuffer = [];
    // private $chunkLimit = 2000;

    // public function collection(Collection $rows)
    // {
    //     $now = now()->format('Y-m-d H:i:s');

    //     foreach ($rows as $row) {
    //         // Validasi agar hanya baris relevan yang dimasukkan
    //         if (!empty($row['sales_number'])) {
    //             $this->srdrBuffer[] = [
    //                 $row['sales_number'],
    //                 $this->transformDate($row['sales_date']),
    //                 $this->transformDate($row['sales_dine_in']),
    //                 $this->transformDate($row['sales_dine_out']),
    //                 $row['branch'],
    //                 $row['brand'],
    //                 $row['city'],
    //                 $row['visit_purpose'],
    //                 $row['payment_method'],
    //                 $row['menu_category'],
    //                 $row['menu_category_detail'],
    //                 $row['menu'],
    //                 $row['menu_code'],
    //                 $row['order_mode'],
    //                 $row['qty'],
    //                 $row['price'],
    //                 $row['subtotal'],
    //                 $row['discount'],
    //                 $row['total'],
    //                 $row['nett_sales'],
    //                 $row['bill_discount'],
    //                 $row['total_after_bill_discount'],
    //                 $row['waiters'],
    //                 $now,
    //                 $now,
    //             ];
    //         }

    //         if (!empty($row['srr_sales_number'])) {
    //             $this->srrBuffer[] = [
    //                 $row['srr_sales_number'],
    //                 $row['pax'],
    //                 $row['srr_grand_total'],
    //                 $this->transformDate($row['srr_sales_date']),
    //                 $now,
    //                 $now,
    //             ];
    //         }

    //         if (count($this->srdrBuffer) >= $this->chunkLimit) {
    //             $this->flushSrdr();
    //         }

    //         if (count($this->srrBuffer) >= $this->chunkLimit) {
    //             $this->flushSrr();
    //         }
    //     }
    // }

    // public function __destruct()
    // {
    //     $this->flushSrdr();
    //     $this->flushSrr();
    // }

    // private function flushSrdr()
    // {
    //     if (empty($this->srdrBuffer)) return;

    //     $columns = 25;
    //     $placeholders = '(' . rtrim(str_repeat('?,', $columns), ',') . ')';
    //     $batchCount = count($this->srdrBuffer);
    //     $allPlaceholders = implode(',', array_fill(0, $batchCount, $placeholders));
    //     $flatValues = [];

    //     foreach ($this->srdrBuffer as $row) {
    //         if (count($row) < $columns) {
    //             $row = array_pad($row, $columns, null);
    //         }
    //         $flatValues = array_merge($flatValues, $row);
    //     }

    //     DB::insert("
    //         INSERT INTO srdr (
    //             sales_number, sales_date, sales_dine_in, sales_dine_out, branch, brand, city,
    //             visit_purpose, payment_method, menu_category, menu_category_detail, menu, menu_code,
    //             order_mode, qty, price, subtotal, discount, total, nett_sales,
    //             bill_discount, total_after_bill_discount, waiters, created_at, updated_at
    //         ) VALUES $allPlaceholders
    //     ", $flatValues);

    //     $this->srdrBuffer = [];
    // }

    // private function flushSrr()
    // {
    //     if (empty($this->srrBuffer)) return;

    //     $columns = 6;
    //     $placeholders = '(' . rtrim(str_repeat('?,', $columns), ',') . ')';
    //     $batchCount = count($this->srrBuffer);
    //     $allPlaceholders = implode(',', array_fill(0, $batchCount, $placeholders));
    //     $flatValues = [];

    //     foreach ($this->srrBuffer as $row) {
    //         if (count($row) < $columns) {
    //             $row = array_pad($row, $columns, null);
    //         }
    //         $flatValues = array_merge($flatValues, $row);
    //     }

    //     DB::insert("
    //         INSERT INTO srr (
    //             sales_number, pax, grand_total, sales_date, created_at, updated_at
    //         ) VALUES $allPlaceholders
    //     ", $flatValues);

    //     $this->srrBuffer = [];
    // }

    // private function transformDate($value)
    // {
    //     if (!$value) return null;
    //     try {
    //         return is_numeric($value)
    //             ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d H:i:s')
    //             : Carbon::parse($value)->format('Y-m-d H:i:s');
    //     } catch (\Exception $e) {
    //         return null;
    //     }
    // }

    // public function chunkSize(): int
    // {
    //     return 2000;
    // }
}
