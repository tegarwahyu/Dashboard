<?php

namespace App\Imports;

use App\Models\Aktual;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class AktualImport implements OnEachRow, WithHeadingRow
{
    protected $promosi_id;
    protected $outlet_id;

    public function __construct($promosi_id, $outlet_id)
    {
        $this->promosi_id = $promosi_id;
        $this->outlet_id = $outlet_id;
    }

    public function onRow(Row $row)
    {
        $row = $row->toArray();

        if (empty($row['tanggal'])) {
            return;
        }

        $tanggal_excel = $row['tanggal'];
        $tanggal = null;

        // Konversi tanggal dari Excel format (serial number atau string)
        try {
            if (is_numeric($tanggal_excel)) {
                $carbon = Carbon::instance(Date::excelToDateTimeObject($tanggal_excel));
                $tanggal = $carbon->toDateString(); // format Y-m-d
            } else {
                $tanggal = Carbon::createFromFormat('d/m/Y', trim($tanggal_excel))->format('Y-m-d');
            }
        } catch (\Exception $e) {
            // Jika gagal parse, skip baris
            return;
        }

        $budget = isset($row['budget']) ? trim($row['budget']) : '';

        // Query cari berdasarkan promosi_id, outlet_id, dan tanggal
        $query = Aktual::where('promosi_id', $this->promosi_id)
            ->where('outlet_id', $this->outlet_id)
            ->whereDate('promo_date', $tanggal);
        // dd($query->exists());
        // die();
        if ($budget !== '') {
            // Kalau ada budget dan data ditemukan, update
            if ($query->exists()) {
                $query->update([
                    'budget' => $budget
                ]);
            }
        } else {
            // Kalau budget kosong dan belum ada data, buat baru
            if (!$query->exists()) {
                Aktual::create([
                    'promosi_id' => $this->promosi_id,
                    'outlet_id' => $this->outlet_id,
                    'promo_date' => $tanggal,
                    'traffic' => $row['traffic'] ?? null,
                    'pax' => $row['pax'] ?? null,
                    'bill' => $row['bill'] ?? null,
                    'sales' => $row['sales'] ?? null,
                ]);
            }
        }
    }

    public function headingRow(): int
    {
        return 1;
    }
}
