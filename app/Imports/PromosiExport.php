<?php

namespace App\Imports;

use App\Models\Aktual;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class PromosiExport implements FromCollection, WithHeadings
{
  protected $data;

    public function __construct($data)
    {
        $this->data = $data; // Collection dari Controller
    }

    public function collection()
    {
        // return collect($this->data)->map(function ($item) {
        //     return [
        //         'Judul Promosi' => $item->judul_promosi,
        //         'Nama Outlet'   => $item->outlet->nama_outlet ?? '-',
        //         'Nama Brand'    => $item->outlet->brand->nama_brand ?? '-',
        //         'Tanggal Mulai' => \Carbon\Carbon::parse($item->mulai_promosi)->format('Y-m-d'),
        //         'Tanggal Akhir' => \Carbon\Carbon::parse($item->akhir_promosi)->format('Y-m-d'),
        //         'Traffic'       => optional($item->promosi_kip->first())->traffic ?? '-',
        //         'Pax'           => optional($item->promosi_kip->first())->pax ?? '-',
        //         'Bill'          => optional($item->promosi_kip->first())->bill ?? '-',
        //         'Budget'        => optional($item->promosi_kip->first())->budget ?? '-',
        //         'Sales'         => optional($item->promosi_kip->first())->sales ?? '-',
        //     ];
        // });
        return collect($this->data)->flatMap(function ($item) {
            return $item->promosi_kip->map(function ($kpi) use ($item) {
                return [
                    'Judul Promosi' => $item->judul_promosi,
                    'Nama Outlet'   => $item->outlet->nama_outlet ?? '-',
                    'Nama Brand'    => $item->outlet->brand->nama_brand ?? '-',
                    'Tanggal Mulai' => \Carbon\Carbon::parse($item->mulai_promosi)->format('Y-m-d'),
                    'Tanggal Akhir' => \Carbon\Carbon::parse($item->akhir_promosi)->format('Y-m-d'),
                    'Traffic'       => $kpi->traffic,
                    'Pax'           => $kpi->pax,
                    'Bill'          => $kpi->bill,
                    'Budget'        => $kpi->budget,
                    'Sales'         => $kpi->sales,
                ];
            });
        });
    }

    public function headings(): array
    {
        return [
            'Judul Promosi',
            'Nama Outlet',
            'Nama Brand',
            'Tanggal Mulai',
            'Tanggal Akhir',
            'Traffic',
            'Pax',
            'Bill',
            'Budget',
            'Sales',
        ];
    }
}
