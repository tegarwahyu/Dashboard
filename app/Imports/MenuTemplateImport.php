<?php

namespace App\Imports;

use App\Models\Master_menu_template;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MenuTemplateImport implements OnEachRow, WithHeadingRow
{
    // protected $promosi_id;
    // protected $outlet_id;

    // public function __construct($promosi_id, $outlet_id)
    // {
    //     $this->promosi_id = $promosi_id;
    //     $this->outlet_id = $outlet_id;
    // }

    public function onRow(Row $row)
    {
        $row = $row->toArray();

        // Validasi: jika menu_code kosong, abaikan
        if (empty($row['menu_code'])) return;

        Master_menu_template::updateOrCreate(
            ['menu_code' => $row['menu_code']],
            [
                'menu_template_name' => $row['menu_template_name'] ?? null,
                'menu_category'      => $row['menu_category'] ?? null,
                'menu_category_detail' => $row['menu_category_detail'] ?? null,
                'menu_name'          => $row['menu_name'] ?? null,
                'menu_short_name'    => $row['menu_short_name'] ?? null,
                'price'              => $row['price'] ?? 0,
                'status'             => $row['status'] ?? 'active',
            ]
        );
    }

    public function headingRow(): int
    {
        return 1;
    }
}
