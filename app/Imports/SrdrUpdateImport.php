<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SrdrUpdateImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $now = now()->format('Y-m-d H:i:s');

        foreach ($rows as $row) {
            DB::table('srdr')
                ->where('sales_number', $row[1])
                ->where('menu_code', $row[13])
                ->where('sales_date', $row[2])
                ->update([
                    // 'promosi_id' => $row[0],
                    'sales_dine_in' => $this->transformDate($row[3]),
                    'sales_dine_out' => $this->transformDate($row[4]),
                    // 'sales_dine_in' => $row[3],
                    // 'sales_dine_out' => $row[4],
                    'branch' => $row[5],
                    'brand' => $row[6],
                    'city' => $row[7],
                    'visit_purpose' => $row[8],
                    'payment_method' => $row[9],
                    'menu_category' => $row[10],
                    'menu_category_detail' => $row[11],
                    'menu' => $row[12],
                    'order_mode' => $row[14],
                    'qty' => $row[15],
                    'price' => $row[16],
                    'subtotal' => $row[17],
                    'discount' => $row[18],
                    'total' => $row[19],
                    'nett_sales' => $row[20],
                    'bill_discount' => $row[21],
                    'total_after_bill_discount' => $row[22],
                    'pax' => $row[23],
                    'waiters' => $row[24],
                ]);

            DB::table('srr')
                ->where('sales_number', $row[25])
                ->where('sales_date', $row[28])
                ->update([
                    'sales_number' => $row[25],
                    'pax' => $row[26],
                    'grand_total' => $row[27],
                    'sales_date' => $row[28],
                ]);
        }
    }

    private function transformDate($value)
    {
        if (!$value) return null;
        try {
            return is_numeric($value)
                ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d H:i:s')
                : Carbon::parse($value)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }
}