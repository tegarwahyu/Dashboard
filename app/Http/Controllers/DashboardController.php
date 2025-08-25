<?php

namespace App\Http\Controllers;

use App\models\Brand;
use App\Models\Outlet;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\Sub_branch;
use App\Models\Promosi;
use App\Models\Srdr;
use App\Models\Target_outlet;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{

    
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        return view('dashboard');
    }

    public function indexAccounting(Request $request)
    {
        return view('dasboard_acounting_operational');
    }

    public function getMenuCategoryDetail(Request $request){
        $brand = $request->query('brand');
        // $cat   = $request->query('menu_category');
        $branchNames = DB::table('branch as br')
            ->join('brand as b', 'br.brand_id', '=', 'b.id')
            ->whereRaw('LOWER(TRIM(b.nama_brand)) = LOWER(TRIM(?))', [$brand])
            ->selectRaw('TRIM(br.nama_branch) as nama_branch')
            ->orderBy('br.nama_branch')
            ->pluck('nama_branch');   // Collection of strings

        // 2) Pakai whereIn utk memfilter srdr.branch
        $details = DB::table('srdr')
            ->when($brand, function ($q) use ($branchNames) {
                if ($branchNames->isNotEmpty()) {
                    $q->whereIn(DB::raw('TRIM(branch)'), $branchNames->all());
                } else {
                    // tidak ada branch utk brand ini → kosongkan hasil
                    $q->whereRaw('1=0');
                }
            })
            ->selectRaw('TRIM(menu_category_detail) AS menu_category_detail')
            ->whereNotNull('menu_category_detail')->where('menu_category_detail', '<>', '')
            ->distinct()->orderBy('menu_category_detail')
            ->pluck('menu_category_detail');


        return response()->json($details);
    }

    public function getMenuCategory(Request $request){

        $brand = $request->query('brand');
        // 1) Ambil daftar nama_branch untuk brand tsb → array string
        $branchNames = DB::table('branch as br')
            ->join('brand as b', 'br.brand_id', '=', 'b.id')
            ->whereRaw('LOWER(TRIM(b.nama_brand)) = LOWER(TRIM(?))', [$brand])
            ->selectRaw('TRIM(br.nama_branch) as nama_branch')
            ->orderBy('br.nama_branch')
            ->pluck('nama_branch');   // Collection of strings

        // 2) Pakai whereIn utk memfilter srdr.branch
        $categories = DB::table('srdr')
            ->when($brand, function ($q) use ($branchNames) {
                if ($branchNames->isNotEmpty()) {
                    $q->whereIn(DB::raw('TRIM(branch)'), $branchNames->all());
                } else {
                    // tidak ada branch utk brand ini → kosongkan hasil
                    $q->whereRaw('1=0');
                }
            })
            ->selectRaw('TRIM(menu_category) AS menu_category')
            ->whereNotNull('menu_category')->where('menu_category', '<>', '')
            ->distinct()->orderBy('menu_category')
            ->pluck('menu_category');

        return response()->json($categories);
    }

    public function getSummarySalesReport(Request $request) {
        // dd('jalan ke kontroler getSummarySalesReport');
        // die();
        $outlet = $request->input('outlet'); 
        $tahun  = $request->input('tahun');

        // --- KUERI UTAMA ANDA (TETAP SAMA) ---
        // Ini adalah data penjualan bulanan dari tabel 'srdr'
        $data = DB::table('srdr')
            ->select(
                DB::raw("MONTH(sales_date) as bulan_num"),
                DB::raw("MONTHNAME(sales_date) as bulan"),
                DB::raw("SUM(nett_sales) as nett_sales"),
                DB::raw("SUM(tax) as tax"),
                DB::raw("SUM(service_charge) as service_charge"),
                DB::raw("SUM(total) as omset"),
                DB::raw("(SUM(service_charge) * 0.40 * 0.80) as OperasionalTS"),
                DB::raw("(SUM(service_charge) * 0.40 * 0.20) as OfficeTS"),
                DB::raw("(SUM(service_charge) * 0.40 * 0.80) as OperasionalBS"),
                DB::raw("(SUM(service_charge) * 0.40 * 0.20) as OfficeBS"),
                DB::raw("(SUM(service_charge) * 0.20) as LnB"),
                DB::raw("COUNT(DISTINCT sales_number) as bill"),
                DB::raw("(SUM(nett_sales) / NULLIF(COUNT(DISTINCT sales_number),0)) as avg_bill"),
                DB::raw("SUM(CASE WHEN menu_category = 'FOOD' THEN qty ELSE 0 END) as qty_food"),
                DB::raw("SUM(CASE WHEN menu_category = 'BEVERAGE' THEN qty ELSE 0 END) as qty_beverage"),
                DB::raw("SUM(CASE WHEN menu_category IN ('FOOD','BEVERAGE') THEN qty ELSE 0 END) as total_fnb")
            )
            ->where('branch', $outlet)
            ->whereYear('sales_date', $tahun)
            ->groupBy(DB::raw("MONTH(sales_date)"), DB::raw("MONTHNAME(sales_date)"))
            ->orderBy(DB::raw("MONTH(sales_date)"))
            ->get();
        // --- AKHIR DARI KUERI UTAMA ---

        // --- LOGIKA PENGGABUNGAN DATA ---

        // Langkah 1: Dapatkan ID sub_branch berdasarkan nama outlet
        $subBranch = DB::table('sub_branch')
            ->select('id')
            ->where('nama_sub_branch', $outlet)
            ->first();

        // Jika outlet tidak ditemukan, kembalikan data kosong
        if (!$subBranch) {
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
        }
        
       // Langkah 2a: Subkueri untuk data TARGET bulanan (sudah diperbaiki)
        $targets = DB::table('target_sales_outlet')
            ->select(
                DB::raw("MONTH(month) as bulan_num"), // MENGAMBIL BULAN DALAM BENTUK ANGKA
                DB::raw("SUM(senin + selasa + rabu + kamis + jumat + sabtu + minggu) as total_target_per_bulan")
            )
            ->where('sub_branch_id', $subBranch->id)
            ->whereYear('created_at', $tahun)
            ->groupBy(DB::raw("MONTH(month)"))
            ->get();

        // Menggunakan "bulan_num" sebagai kunci
        $targetsByMonth = $targets->keyBy('bulan_num');
        
        // Langkah 2b: Subkueri untuk data GAJI bulanan (sudah diperbaiki)
        $salaries = DB::table('outlet_salary_monthly')
            ->select(
                DB::raw("MONTH(month) as bulan_num"), // MENGAMBIL BULAN DALAM BENTUK ANGKA
                DB::raw("SUM(total_salary) as gaji_per_bulan")
            )
            ->where('sub_branch_id', $subBranch->id)
            ->whereYear('created_at', $tahun)
            ->groupBy(DB::raw("MONTH(month)"))
            ->get();

        // Menggunakan "bulan_num" sebagai kunci
        $salariesByMonth = $salaries->keyBy('bulan_num');
        
        // Langkah 3: Gabungkan data (sekarang sudah cocok)
        $data = $data->map(function ($item) use ($targetsByMonth, $salariesByMonth) {
            $monthNumber = (int) $item->bulan_num; // Pastikan tipe datanya integer
            
            // Sekarang lookup akan berhasil karena kunci cocok (angka)
            $targetData = $targetsByMonth->get($monthNumber);
            $salaryData = $salariesByMonth->get($monthNumber);
            
            $item->total_target_per_bulan = $targetData ? $targetData->total_target_per_bulan : 0;
            $item->gaji_per_bulan = $salaryData ? $salaryData->gaji_per_bulan : 0;
            
            return $item;
        });

        // dd($data);
        // die();

        // --- AKHIR DARI LOGIKA PENGGABUNGAN ---

        // Format agar cocok utk DataTables / AJAX
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $data->count(),
            'recordsFiltered' => $data->count(),
            'data' => $data
        ]);
    }

    public function getAnalisaItem(Request $request){
        $outlet = $request->input('outlet');
        $bulan  = $request->input('bulan');
        $tahun  = $request->input('tahun');

        if (!$outlet || !$bulan || !$tahun) {
            return response()->json(['error' => 'Parameter outlet, bulan, dan tahun wajib diisi.'], 400);
        }

        $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::create($tahun, $bulan, 1)->endOfMonth();
        
        // parameter kategori
        $BEVERAGE = [ 'CLAYPOT', 'DESSERT','FREE BEVERAGE','HEALTHY DRINK','OTHER DRINK','REFRESHING TEA', 'SMOOTHIES','LOYALTY' ];
        $FOOD = [ 'FREE FOOD', 'LPYALTY FOOD','PROMO','SIDE DISH FOOD','ROTI','NASI GORENG', 'NASI','MEE', 'KUEYTEOW', 'DIMSUM & SNACK', 'BUBUR' ];
        $OTHER = [ 'MEMBER & VOUCHER', 'PAKET DELIVERY','PACKAGING','MERCHANDISE','MENU PAKET' ];

        $rawData = DB::table('srdr')
            ->join('master_menu_template_data', 'srdr.menu_code', '=', 'master_menu_template_data.menu_code')
            ->leftJoin('analisa_item', 'srdr.menu_code', '=', 'analisa_item.menu_code') // relasi cost
            ->select(
                'srdr.menu_category_detail as kategori',
                'srdr.menu as nama_menu',
                'srdr.menu_code as kode',
                DB::raw('SUM(srdr.qty) as qty'),
                DB::raw('SUM(srdr.subtotal) as total_sales'),
                // 'master_menu_template_data.price as price_list',
                DB::raw('IF(SUM(srdr.qty) = 0, 0, SUM(srdr.subtotal) / SUM(srdr.qty)) as price_list'),
                DB::raw('IFNULL(analisa_item.cost, 0) as cost'),
                DB::raw('SUM(srdr.qty * IFNULL(analisa_item.cost, 0)) as total_cost'),
                DB::raw('IF(IFNULL(analisa_item.cost, 0) = 0, 0, SUM(srdr.qty * IFNULL(analisa_item.cost, 0) * 100) / NULLIF(SUM(srdr.subtotal), 0)) as percent_cost')
            )
            ->where('srdr.branch', $outlet)
            ->whereBetween('srdr.sales_date', [$startDate, $endDate])
            ->groupBy(
                'srdr.menu_category_detail',
                'srdr.menu',
                'srdr.menu_code',
                'master_menu_template_data.price',
                'analisa_item.cost'
            )
            ->orderBy('srdr.menu_category_detail')
            ->get();

        // Total berdasarkan kategori
        $total_beverage = $rawData->whereIn('kategori', $BEVERAGE)->sum('qty');
        $total_food = $rawData->whereIn('kategori', $FOOD)->sum('qty');
        $total_other = $rawData->whereIn('kategori', $OTHER)->sum('qty');

        // Total sales
        $total_sales_beverage = $rawData->whereIn('kategori', $BEVERAGE)->sum('total_sales');
        $total_sales_food = $rawData->whereIn('kategori', $FOOD)->sum('total_sales');
        $total_sales_other = $rawData->whereIn('kategori', $OTHER)->sum('total_sales');
        
        // Total cost berdasarkan kategori dengan validasi numerik
        $total_cost_beverage = $rawData->whereIn('kategori', $BEVERAGE)->sum(function ($item) {
            return is_numeric($item->total_cost) ? (float) $item->total_cost : 0;
        });
        $total_cost_food = $rawData->whereIn('kategori', $FOOD)->sum(function ($item) {
            return is_numeric($item->total_cost) ? (float) $item->total_cost : 0;
        });
        $total_cost_other = $rawData->whereIn('kategori', $OTHER)->sum(function ($item) {
            return is_numeric($item->total_cost) ? (float) $item->total_cost : 0;
        });
        
        // --- Persentase Cost ---
        // $total_percent_cost_beverage = $total_sales_beverage > 0 ? ($total_cost_beverage / $total_sales_beverage) * 100 : 0;
        // $total_percent_cost_food     = $total_sales_food > 0 ? ($total_cost_food / $total_sales_food) * 100 : 0;
        // $total_percent_cost_other    = $total_sales_other > 0 ? ($total_cost_other / $total_sales_other) * 100 : 0;

        return response()->json([
            'data' => $rawData,
            'total_beverage' => $total_beverage,
            'total_food' => $total_food,
            'total_other' => $total_other,
            'total_sales_beverage' => $total_sales_beverage,
            'total_sales_food' => $total_sales_food,
            'total_sales_other' => $total_sales_other,
            // Cost
            'total_cost_beverage' => $total_cost_beverage,
            'total_cost_food'     => $total_cost_food,
            'total_cost_other'    => $total_cost_other,
            // Persentase Cost
            // 'total_percent_cost_beverage' => round($total_percent_cost_beverage, 2),
            // 'total_percent_cost_food'     => round($total_percent_cost_food, 2),
            // 'total_percent_cost_other'    => round($total_percent_cost_other, 2),
        ]);

        // cara ke 4
        // // parameter other, beverage, food
        // $BEVERAGE = [ 'CLAYPOT', 'DESSERT','FREE BEVERAGE','HEALTHY DRINK','OTHER DRINK','REFRESHING TEA', 'SMOOTHIES','LOYALTY' ];
        // $FOOD = [ 'FREE FOOD', 'LPYALTY FOOD','PROMO','SIDE DISH FOOD','ROTI','NASI GORENG', 'NASI','MEE', 'KUEYTEOW', 'DIMSUM & SNACK', 'BUBUR' ];
        // $OTHER = [ 'MEMBER & VOUCHER', 'PAKET DELIVERY','PACKAGING','MERCHANDISE','MENU PAKET' ];
        
        // $rawData = DB::table('srdr')
        //     ->join('master_menu_template_data', 'srdr.menu_code', '=', 'master_menu_template_data.menu_code')
        //     ->select(
        //         'srdr.menu_category_detail as kategori',
        //         'srdr.menu as nama_menu',
        //         'srdr.menu_code as kode',
        //         DB::raw('SUM(srdr.qty) as qty'),
        //         DB::raw('SUM(srdr.subtotal) as total_sales'),
        //         'master_menu_template_data.price as price_list'
        //     )
        //     ->where('srdr.branch', $outlet)
        //     ->whereBetween('srdr.sales_date', [$startDate, $endDate])
        //     ->groupBy(
        //         'srdr.menu_category_detail',
        //         'srdr.menu',
        //         'srdr.menu_code',
        //         'master_menu_template_data.price'
        //     )
        //     ->orderBy('srdr.menu_category_detail')
        //     ->get();

        // // Tambahkan total berdasarkan kategori
        // $total_beverage = $rawData->whereIn('kategori', $BEVERAGE)->sum('qty');
        // $total_food = $rawData->whereIn('kategori', $FOOD)->sum('qty');
        // $total_other = $rawData->whereIn('kategori', $OTHER)->sum('qty');

        // // Total sales
        // $total_sales_beverage = $rawData->whereIn('kategori', $BEVERAGE)->sum('total_sales');
        // $total_sales_food = $rawData->whereIn('kategori', $FOOD)->sum('total_sales');
        // $total_sales_other = $rawData->whereIn('kategori', $OTHER)->sum('total_sales');

        // // Tambahkan ke response
        // return response()->json([
        //     'data' => $rawData,
        //     'total_beverage' => $total_beverage,
        //     'total_food' => $total_food,
        //     'total_other' => $total_other,
        //     'total_sales_beverage' => $total_sales_beverage,
        //     'total_sales_food' => $total_sales_food,
        //     'total_sales_other' => $total_sales_other
        // ]);
        // metode ke 3
        // $rawData = DB::table('srdr')
        //     ->join('master_menu_template_data', 'srdr.menu_code', '=', 'master_menu_template_data.menu_code')
        //     ->select(
        //         'srdr.menu_category_detail as kategori',
        //         'srdr.menu as nama_menu',
        //         'srdr.menu_code as kode',
        //         DB::raw('SUM(srdr.qty) as qty'),
        //         DB::raw('SUM(srdr.subtotal) as total_sales'),
        //         'master_menu_template_data.price as price_list'
        //     )
        //     ->where('srdr.branch', $outlet)
        //     ->whereBetween('srdr.sales_date', [$startDate, $endDate])
        //     ->groupBy(
        //         'srdr.menu_category_detail',
        //         'srdr.menu',
        //         'srdr.menu_code',
        //         'master_menu_template_data.price'
        //     )
        //     ->orderBy('srdr.menu_category_detail')
        //     ->get();

        // return response()->json(['data' => $rawData]);
        // metode ke 2 
        // $rawData = DB::table('srdr')
        //     ->select(
        //         'menu_category_detail as kategori',
        //         'menu as nama_menu',
        //         'menu_code as kode',
        //         DB::raw('SUM(qty) as qty'),
        //         DB::raw('SUM(subtotal) as total_sales')
        //     )
        //     ->where('branch', $outlet)
        //     ->whereBetween('sales_date', [$startDate, $endDate])
        //     ->groupBy('menu_category_detail', 'menu', 'menu_code')
        //     ->orderBy('menu_category_detail')
        //     ->get();

        // return response()->json(['data' => $rawData]);
        // metode pertama 
        // $rawData = DB::table('srdr')
        //     ->select(
        //         'menu_category_detail as kategori',
        //         'menu as nama_menu',
        //         'menu_code as kode',
        //         DB::raw('SUM(qty) as qty')
        //     )
        //     ->where('branch', $outlet)
        //     ->whereBetween('sales_date', [$startDate, $endDate])
        //     ->groupBy('menu_category_detail', 'menu', 'menu_code')
        //     ->orderBy('menu_category_detail')
        //     ->get();

        // return response()->json(['data' => $rawData]);

    }

    public function getAccountingParameter(){
        // $outletIds = Outlet::select('id','nama_outlet')->get();

        // return response()->json([
        //     'data_outlet' => $outletIds
        // ]);
        // $outletIds = Branch::select('id','nama_branch')->get();
        $outletIds = Sub_branch::select('id','nama_sub_branch')->get();
        return response()->json([
            'data_outlet' => $outletIds
        ]);
    }

    public function getAccountingParameterBrand(){
        $brandIds = Brand::select('id','nama_brand')->get();
        // $brandIds = Branch::select('id','nama_branch')->get();
        return response()->json([
            'data_brand' => $brandIds
        ]);
    }

    public function getBrandList(){
        $brands = Brand::whereHas('outlets')->pluck('nama_brand');
        return response()->json($brands);
    }

    public function getPromotionsByBrand($nama_brand){
        $promotions = Promosi::whereHas('outlet.brand', function ($query) use ($nama_brand) {
            $query->where('nama_brand', $nama_brand);
        })->where('is_enabled',1)->pluck('judul_promosi')->unique();

        return response()->json($promotions);
    }

    public function getGeneralData(){
         // Misalnya kamu ambil dari tabel srdr atau data lain
        $getPromosi = Promosi::select('judul_promosi')->where('is_enabled',1)->get();
        $outletIds = Outlet::select('id','nama_outlet')->get();
        $brandIds  = Brand::select('id','nama_brand')->get();

        return response()->json([
            'total_promosi' => $getPromosi->count(),
            'total_outlet' => $outletIds->count(),
            'total_brand' => $brandIds->count(),
        ]);
    }

    public function getMenuCode()
    {
        $data = DB::table('master_menu_template_data')
            ->select(
                'id',
                'menu_template_name',
                'menu_category',
                'menu_category_detail',
                'menu_name',
                'menu_short_name',
                'menu_code',
                'price',
                'status'
            )
            ->get();

        return response()->json($data);
    }

    public function getBranchDashboard(){
        // Mengambil data dari tabel branch
        $branches = Branch::select('id', 'nama_branch')->get();

        // Mengembalikan data dalam format JSON
        return response()->json($branches);
    }

    public function getMenuReport(Request $request){
        // dd($request);
        // die();
        $data = $request->validate([
            'branch_id' => ['required'],                  // bisa ID atau nama
            'month'     => ['required','integer','min:1','max:12'],
            'year'      => ['required','integer','min:2000','max:2100'],
            'type'      => ['required','in:qty,value'],
        ]);

        $branchParam = $data['branch_id'];
        $month       = (int) $data['month'];
        $year        = (int) $data['year'];
        $type        = $data['type'];

        // Jika numeric → lookup nama branch; jika string → pakai langsung
        if (is_numeric($branchParam)) {
            $branchName = DB::table('branch')
                ->where('id', (int) $branchParam)
                ->value(DB::raw('TRIM(nama_branch)'));
        } else {
            $branchName = trim((string) $branchParam);
        }

        if (!$branchName) {
            return response()->json(['rows' => []]);
        }

        // Ambil agregat qty per menu_code (nanti dipetakan ke 'value' sesuai type)
        $agg = DB::table('srdr as s')
            ->selectRaw('
                TRIM(s.menu)                 AS menu_name,
                TRIM(s.menu_code)            AS menu_code,
                TRIM(s.menu_category)        AS menu_category_name,
                TRIM(s.menu_category_detail) AS menu_category_detail_name,
                SUM(s.qty)                   AS qty_sum
            ')
            ->whereRaw('LOWER(TRIM(s.branch)) = LOWER(TRIM(?))', [$branchName])
            ->whereMonth('s.sales_date', $month)   // jika sales_date string, lihat catatan di bawah
            ->whereYear('s.sales_date',  $year)
            ->groupBy('s.menu', 's.menu_code', 's.menu_category', 's.menu_category_detail')
            ->orderByDesc(DB::raw('SUM(s.qty)'))
            ->get();

        // Map: jika type=qty → value=qty_sum; kalau type=value → value=0
        $rows = $agg->map(function ($r) use ($type) {
            return (object) [
                'menu_name'                  => $r->menu_name,
                'menu_code'                  => $r->menu_code,
                'menu_category_name'         => $r->menu_category_name,
                'menu_category_detail_name'  => $r->menu_category_detail_name,
                'value'                      => $type === 'qty' ? (int) $r->qty_sum : 0,
            ];
        });

        // Rank dihitung di frontend sesuai JS kamu
        return response()->json(['rows' => $rows]);
    }

    public function getProgramData2(Request $request){
        // dd($request);
        // die();
        $brand   = (string) $request->input('brand', '');
        $catRaw  = (string) $request->input('MenuCategory', '');
        $detRaw  = (string) $request->input('MenuCategoryDetail', '');

        $cat     = trim($catRaw);
        $detail  = trim($detRaw);

        // Normalisasi nilai "aneh" dari frontend
        foreach (['undefined','null','-'] as $bad) {
            if (strtolower($cat) === $bad)    $cat = '';
            if (strtolower($detail) === $bad) $detail = '';
        }

        // menu_codes: pastikan array string bersih
        $menu_codes = json_decode($request->input('menu_codes', '[]'), true) ?: [];
        $menu_codes = array_values(array_filter(array_map(fn($v) => trim((string)$v), $menu_codes)));

        $rawData = DB::table('srdr')
            ->join('branch as br', 'srdr.branch', '=', 'br.nama_branch')
            ->join('brand  as b',  'br.brand_id', '=', 'b.id')
            ->select(
                'srdr.branch as nama_outlet',
                'srdr.sales_date',
                'srdr.menu_code',
                DB::raw('MIN(TRIM(srdr.menu)) as menu_name'),
                DB::raw('SUM(srdr.qty) as jumlah_menu'),
                DB::raw('0 as qty_target')
            )
            // === FILTERS (lebih toleran spasi/kapital) ===
            ->when($brand   !== '', fn($q) => $q->whereRaw('LOWER(TRIM(b.nama_brand)) = LOWER(TRIM(?))', [$brand]))
            ->when($cat     !== '', fn($q) => $q->whereRaw('LOWER(TRIM(srdr.menu_category)) = LOWER(TRIM(?))', [$cat]))
            ->when($detail  !== '', fn($q) => $q->whereRaw('LOWER(TRIM(srdr.menu_category_detail)) = LOWER(TRIM(?))', [$detail]))
            ->when(!empty($menu_codes), fn($q) => $q->whereIn(DB::raw('TRIM(srdr.menu_code)'), $menu_codes))
            // ==============================================
            ->groupBy('srdr.branch', 'srdr.sales_date', 'srdr.menu_code')
            ->orderBy('srdr.branch')
            ->orderBy('srdr.sales_date')
            ->get();
        // dd($rawData);
        // die();
        // Group per outlet -> per tanggal (tetap sama)
        // Group per outlet -> per tanggal (ikutkan menu_name)
        $grouped = collect($rawData)->groupBy('nama_outlet')->map(function ($items, $outlet) {
            $detail_harian = $items->groupBy('sales_date')->map(function ($rows, $tanggal) {
                return [
                    'sales_date' => $tanggal,
                    'menus' => $rows->map(fn($row) => [
                        'menu_code'   => $row->menu_code,
                        'menu_name'   => $row->menu_name,      // ⬅️ simpan nama menu
                        'jumlah_menu' => (int) $row->jumlah_menu,
                    ])->values()
                ];
            })->values();

            return [
                'nama_outlet'   => $outlet,
                'ach_day'       => 0,
                'ach_avg'       => 0,
                'detail_harian' => $detail_harian
            ];
        })->values();

        return DataTables::of($grouped)
            ->addIndexColumn()
            ->addColumn('ach_day', fn($row) => $row['ach_day'].'%')
            ->addColumn('ach_avg', fn($row) => $row['ach_avg'].'%')
            ->addColumn('detail', function ($row) {
                $buttons = '';

                // code -> name map untuk label tombol
                $codeNameMap = collect($row['detail_harian'])
                    ->flatMap(fn($hari) => collect($hari['menus'])
                        ->mapWithKeys(fn($m) => [ $m['menu_code'] => $m['menu_name'] ]))
                    ->toArray();

                foreach ($codeNameMap as $menuCode => $menuName) {
                    // kumpulkan detail harian khusus kode ini
                    $filtered = collect($row['detail_harian'])->map(function ($hari) use ($menuCode) {
                        $menus = collect($hari['menus'])->where('menu_code', $menuCode)->values();
                        if ($menus->isNotEmpty()) {
                            return [
                                'sales_date'   => $hari['sales_date'],
                                'nama_menu' => $menus->first()['menu_name'],
                                'menu_code'    => $menuCode,
                                'jumlah_menu'  => $menus->first()['jumlah_menu']
                            ];
                        }
                        return null;
                    })->filter()->values();

                    $label = trim($menuCode . ' - ' . ($menuName ?? ''));
                    $buttons .= '<button class="btn btn-sm btn-outline-primary view-actual me-1"
                                    data-outlet="'.e($row['nama_outlet']).'"
                                    data-menu="'.e($label).'"
                                    data-detail=\''.e(json_encode($filtered)).'\'>
                                    <i class="fas fa-calendar-day"></i> '.e($menuCode).'
                                </button>';
                }
                return $buttons;
            })
            ->rawColumns(['detail'])
            ->make(true);

    }

    public function getProgramData(Request $request){
        // dd($request);
        // die();
        $brand = $request->input('brand');
        $program = $request->input('program');

        $promoData = DB::table('tb_promosi_kpi as pk')
            ->join('tb_promosi as p', 'pk.promo_id', '=', 'p.id')
            ->join('tb_outlet as o', 'p.outlet_id', '=', 'o.id')
            ->join('brand as b', 'o.brand_id', '=', 'b.id')
            ->select(
                'o.nama_outlet',
                'pk.menu_kode',
                'pk.menu_nama',
                'pk.qty_target',
                'p.judul_promosi',
                'b.nama_brand',
                'p.mulai_promosi',
                'p.akhir_promosi'
            )
            ->where('p.judul_promosi', $program)
            ->where('b.nama_brand', $brand)
            ->get();

        // Tambahkan actuals_per_day
        foreach ($promoData as $row) {
            $actuals = DB::table('srdr')
                ->where('menu_code', $row->menu_kode)
                ->where('branch', $row->nama_outlet)
                ->whereBetween('sales_date', [$row->mulai_promosi, $row->akhir_promosi])
                ->select('sales_date', DB::raw('SUM(qty) as actual_paket'))
                ->groupBy('sales_date')
                ->orderBy('sales_date')
                ->get();

            $row->actuals_per_day = $actuals;
        }

        foreach ($promoData as $row) {
            $actuals = collect($row->actuals_per_day)->pluck('actual_paket')->map(fn($val) => (float) $val);

            // ACH DAY: nilai terakhir yang > 0 dibagi target
            $lastActual = $actuals->filter(fn($val) => $val > 0)->last();
            $row->ach_day = $row->qty_target > 0 ? round($lastActual / $row->qty_target * 100, 2) : 0;

            // ACH AVG: rata-rata dari nilai > 0 dibagi target
            $avgActual = $actuals->filter(fn($val) => $val > 0)->avg();
            $row->ach_avg = $row->qty_target > 0 ? round($avgActual / $row->qty_target * 100, 2) : 0;
        }
        return DataTables::of(collect($promoData))
            ->addIndexColumn()
            ->addColumn('ach_day', fn($row) => $row->ach_day . '%')
            ->addColumn('ach_avg', fn($row) => $row->ach_avg . '%')
            ->addColumn('detail', function ($row) {
                return '<button class="btn btn-sm btn-outline-primary view-actual" data-outlet="' . $row->nama_outlet . '" data-menu="' . $row->menu_kode . '" data-detail=\'' . json_encode($row->actuals_per_day) . '\'>
                            <i class="fas fa-calendar-day"></i>
                        </button>';
            })
            ->rawColumns(['detail'])
            ->make(true);

    }

    public function getPromoActual2(Request $request){
        // Langkah 1: Validasi dan Relasikan Brand dan Branch
        // Cari brand berdasarkan nama
        $brand = Brand::where('nama_brand', $request->brand)->first();
        
        // Jika brand tidak ditemukan, hentikan proses
        if (!$brand) {
            return response()->json(['message' => 'Brand tidak ditemukan.'], 404);
        }

        // Cari branch berdasarkan nama outlet dan brand_id.
        // Ini adalah langkah relasi terpisah brand -> branch.
        $branch = Branch::where('nama_branch', $request->outlet)
                        ->where('brand_id', $brand->id)
                        ->first();
                        
        // Jika branch tidak ditemukan, hentikan proses
        if (!$branch) {
            return response()->json(['message' => 'Outlet tidak ditemukan untuk brand ini.'], 404);
        }

        // Langkah 2: Kueri Tabel srdr
        // Setelah validasi brand dan branch berhasil, kita gunakan nama-nama yang sudah valid
        // untuk mengkueri tabel srdr. Ini memastikan data yang diambil sudah terfilter
        // dengan benar tanpa melakukan join langsung dari brand ke srdr.
        $dailySales = Srdr::select(
                DB::raw('DATE(sales_date) as date'),
                'srdr.branch',
                'srdr.menu',
                DB::raw('SUM(srdr.qty) as total_qty'),
                DB::raw('SUM(srdr.subtotal) as total_subtotal'),
                DB::raw('SUM(srdr.nett_sales) as total_nett_sales')
            )
            // ->where('srdr.brand', $brand->nama_brand)
            ->where('srdr.branch', $branch->nama_branch)
            ->groupBy(DB::raw('DATE(sales_date)'), 'srdr.branch', 'srdr.menu')
            ->orderBy('date', 'asc')
            ->orderBy('srdr.branch', 'asc')
            ->get();

        if ($dailySales->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada data penjualan untuk outlet yang diminta.'
            ], 404);
        }

        return response()->json($dailySales);

    }

    public function getPromoActual(Request $request){
        $outlet = $request->input('outlet');
        $brand = $request->input('brand');
        $program = $request->input('program');

        $data = DB::table('tb_promosi_kpi as pk')
            ->join('tb_promosi as p', 'pk.promo_id', '=', 'p.id')
            ->join('tb_outlet as o', 'p.outlet_id', '=', 'o.id')
            ->join('brand as b', 'o.brand_id', '=', 'b.id')
            ->join('srdr', function ($join) {
                $join->on('srdr.menu_code', '=', 'pk.menu_kode')
                    ->on('srdr.branch', '=', 'o.nama_outlet');
            })
            ->select(
                'srdr.sales_date',
                'o.nama_outlet',
                'pk.menu_kode',
                'pk.menu_nama',
                'pk.qty_target',
                DB::raw('SUM(srdr.qty) as actual_paket')
            )
            ->where('p.judul_promosi', $program)
            ->where('b.nama_brand', $brand)
            ->where('o.nama_outlet', $outlet)
            ->whereBetween('srdr.sales_date', [DB::raw('p.mulai_promosi'), DB::raw('p.akhir_promosi')])
            ->groupBy('srdr.sales_date', 'o.nama_outlet', 'pk.menu_kode', 'pk.menu_nama', 'pk.qty_target')
            ->orderBy('srdr.sales_date')
            ->get();

        return response()->json($data);
    }

    public function getRangking(Request $request){
        dd($request);
        die();
    }

    public function getCompare(Request $request){
        // dd($outlet);
        // die();
        $brandName = $request->outlet; // nama brand, misalnya "Kopi ABC"
        $month = $request->date;
        $year = $request->tahun;

        // 1. Temukan brand berdasarkan nama
        $brand = Brand::where('nama_brand', $brandName)->first();
        if (!$brand) {
            return response()->json(['error' => 'Brand tidak ditemukan'], 404);
        }

        // 2. Ambil semua branch dari brand tersebut
        $branchIds = Branch::where('brand_id', $brand->id)->pluck('id');

        // 3. Ambil semua sub_branch dari branch-brand tersebut
        $subBranches = Sub_branch::whereIn('branch_id', $branchIds)->get();

        $result = [];

        foreach ($subBranches as $sub) {
            $outletId   = $sub->id;
            $outletName = $sub->nama_sub_branch;

            // Ambil daftar sales_number dari SRDR (boleh ada / tidak dipakai ke SRR)
            $salesNumbers = DB::table('srdr')
                ->where('branch', $outletName)
                ->pluck('sales_number')
                ->unique();

            // === Semua metrik dari SRR -> 0 ===
            $salesSummary = (object)[
                'sales' => 0,
                'guest' => 0,
                'bill'  => 0,
            ];

            // Nett sales dari SRDR (langsung per bulan & outlet)
            $nettSales = (int) DB::table('srdr')
                ->where('branch', $outletName)
                ->whereYear('sales_date', $year)
                ->whereMonth('sales_date', $month)
                ->sum('nett_sales');

            // Target outlet (sum harian pada bulan tsb)
            $target = (int) DB::table('target_sales_outlet')
                ->where('sub_branch_id', $outletId)
                ->whereYear('month', $year)
                ->whereMonth('month', $month)
                ->selectRaw('SUM(senin+selasa+rabu+kamis+jumat+sabtu+minggu) as total_target')
                ->value('total_target');

            // Rata-rata (akan 0 karena bill/guest = 0)
            $avgBill  = ($salesSummary->bill  > 0) ? ceil($nettSales / $salesSummary->bill)  : 0;
            $avgGuest = ($salesSummary->guest > 0) ? ceil($nettSales / $salesSummary->guest) : 0;

            // Total gaji outlet
            $totalSalary = (int) DB::table('outlet_salary_monthly')
                ->where('sub_branch_id', $outletId)
                ->whereYear('month', $year)
                ->whereMonth('month', $month)
                ->sum('total_salary');

            // Qty menu per kategori (SRDR)
            $menuCategoryQty = DB::table('srdr')
                ->where('branch', $outletName)
                ->whereYear('sales_date', $year)
                ->whereMonth('sales_date', $month)
                ->select(
                    DB::raw("SUM(CASE WHEN LOWER(menu_category) = 'food' THEN qty ELSE 0 END) as food"),
                    DB::raw("SUM(CASE WHEN LOWER(menu_category) = 'beverage' THEN qty ELSE 0 END) as beverage"),
                    DB::raw("SUM(CASE WHEN LOWER(menu_category) = 'dessert' THEN qty ELSE 0 END) as dessert")
                )
                ->first();

            $result[] = [
                'outlet'       => $outletName,
                'sales'        => 0,                        // SRR OFF
                'nett_sales'   => $nettSales,               // dari SRDR
                'target'       => $target,
                'bill'         => 0,                        // SRR OFF
                'guest'        => 0,                        // SRR OFF
                'avg_bill'     => $avgBill,                 // 0
                'avg_guest'    => $avgGuest,                // 0
                'total_salary' => $totalSalary,
                'qty_fnb'      => (int) ($menuCategoryQty->food ?? 0) + (int) ($menuCategoryQty->beverage ?? 0),
            ];
        }

        // return DataTables
        return DataTables::of(collect($result))
            ->addColumn('target', function ($row) {
                return $row['target'];
            })
            ->addColumn('avg_bill', function ($row) {
                return $row['bill'] > 0 ? ceil($row['nett_sales'] / $row['bill']) : 0;
            })
            ->addColumn('avg_guest', function ($row) {
                return $row['guest'] > 0 ? ceil($row['nett_sales'] / $row['guest']) : 0;
            })
            ->skipTotalRecords()
            ->addIndexColumn()
            ->make(true);
    }

    public function getGeneralSummary(Request $request){
         // Misalnya kamu ambil dari tabel srdr atau data lain
        // $getPromosi = Promosi::select('judul_promosi')->where('is_enabled',1)->get();
        // $outletIds = Outlet::select('id','nama_outlet')->get();
        // $brandIds  = Brand::select('id','nama_brand')->get();

        // return response()->json([
        //     'total_promosi' => $getPromosi->count(),
        //     'total_outlet' => $outletIds->count(),
        //     'total_brand' => $brandIds->count(),
        // ]);

        // dd($request);
        // die();
        $outlet = $request->input('outlet');
        $tahun  = $request->input('tahun');

        if (!$outlet) {
            return response()->json([
                'data'    => [],
                'message' => 'Pilih outlet terlebih dahulu!'
            ], 422);
        }

        // Sub-branch berdasarkan nama_sub_branch
        $dataOutlet = Sub_branch::where('nama_sub_branch', $outlet)->first();

        // ===== Target bulanan (tetap dari target_sales_outlet) =====
        $targetBulanan = DB::table('target_sales_outlet')
            ->where('sub_branch_id', $dataOutlet->id)
            ->whereYear('month', $tahun)
            ->select(
                DB::raw('MONTH(month) as bulan'),
                DB::raw('SUM(CAST(senin AS UNSIGNED) + CAST(selasa AS UNSIGNED) + CAST(rabu AS UNSIGNED) + CAST(kamis AS UNSIGNED) + CAST(jumat AS UNSIGNED) + CAST(sabtu AS UNSIGNED) + CAST(minggu AS UNSIGNED)) as target')
            )
            ->groupBy(DB::raw('MONTH(month)'))
            ->pluck('target', 'bulan');

        // ===== Base 12 bulan agar setiap bulan muncul =====
        $months = range(1, 12);
        $monthsSql = implode(' UNION ALL ', array_map(fn($m) => "SELECT $m AS bulan", $months));

        // Semua metrik yang dulu dari SRR -> dibuat 0
        $monthlyFinal = DB::table(DB::raw("($monthsSql) as base"))
            ->select(
                'bulan',
                DB::raw('0 as sales'),   // SRR OFF
                DB::raw('0 as guest'),   // SRR OFF
                DB::raw('0 as bill')     // SRR OFF
            );

        // ===== Nett sales per bulan dari SRDR (tetap dipakai) =====
        $nettSalesMonthly = DB::table('srdr')
            ->where('branch', $outlet)                 // outlet = nama_sub_branch (string)
            ->whereYear('sales_date', $tahun)
            ->select(
                DB::raw('MONTH(sales_date) as bulan'),
                DB::raw('SUM(nett_sales) as nett_sales')
            )
            ->groupBy(DB::raw('MONTH(sales_date)'));

        // ===== Gabungkan ke final output =====
        $queryBulanan = DB::table(DB::raw("({$monthlyFinal->toSql()}) as m"))
            ->mergeBindings($monthlyFinal)
            ->leftJoinSub($nettSalesMonthly, 'nm', function ($join) {
                $join->on('m.bulan', '=', 'nm.bulan');
            })
            ->select(
                'm.bulan',
                'm.sales',                 // 0
                DB::raw('COALESCE(nm.nett_sales, 0) as nett_sales'),
                'm.guest',                 // 0
                'm.bill'                   // 0
            )
            ->orderBy('m.bulan');

        // ===== Output DataTables =====
        return DataTables::of($queryBulanan)
            ->addColumn('target', function ($row) use ($targetBulanan) {
                return (int)($targetBulanan[$row->bulan] ?? 0);
            })
            ->addColumn('avg_bill', function ($row) {
                // nett_sales / bill -> 0 karena bill = 0
                return 0;
            })
            ->addColumn('avg_guest', function ($row) {
                // nett_sales / guest -> 0 karena guest = 0
                return 0;
            })
            ->skipTotalRecords()
            ->addIndexColumn()
            ->make(true);


    }

    public function getAccountingData(Request $request){
        // dd($request);
        // die();
        $outlet = $request->input('outlet');
        $bulan  = $request->input('bulan');
        $tahun  = $request->input('tahun');

        if (!$outlet) {
            return response()->json([
                'data'    => [],
                'message' => 'Pilih outlet terlebih dahulu!'
            ], 422);
        }

        // $dataOutlet = Branch::where('nama_branch', $outlet)->first();
        $dataOutlet = Sub_branch::where('nama_sub_branch', $outlet)->first();
        // dd($dataOutlet->id);
        
        $targetWeekly = DB::table('target_sales_outlet')
                    ->where('sub_branch_id', $dataOutlet->id)
                    ->whereMonth('month', $bulan)
                    ->whereYear('month', $tahun)
                    ->get();

        $targetByDate = [];
        $hariMap = [
            1 => 'senin', 2 => 'selasa', 3 => 'rabu', 4 => 'kamis',
            5 => 'jumat', 6 => 'sabtu', 7 => 'minggu'
        ];

        // JAGAAN: kalau hasil query kosong, targetByDate tetap kosong -> semua 0
        if ($targetWeekly->isEmpty()) {
            $targetByDate = [];
        } else {
            foreach ($targetWeekly as $t) {
                // hard guard: abaikan baris yang bukan bulan/tahun request
                $monthOfRow = Carbon::parse($t->month);
                if ((int)$monthOfRow->month !== (int)$bulan || (int)$monthOfRow->year !== (int)$tahun) {
                    continue;
                }

                // lewati jika semua target harian 0
                $totalMingguan = array_sum(array_map(fn($h) => (int)($t->{$h} ?? 0), $hariMap));
                if ($totalMingguan <= 0) continue;

                // hitung awal minggu di DALAM bulan yang sama
                $startOfWeek = $monthOfRow
                    ->copy()
                    ->startOfMonth()
                    ->addWeeks(($t->week_number ?? 1) - 1)
                    ->startOfWeek(Carbon::MONDAY);

                for ($i = 0; $i < 7; $i++) {
                    $d = $startOfWeek->copy()->addDays($i);

                    // simpan hanya tanggal yang bulannya = bulan request (strict)
                    if ((int)$d->month !== (int)$bulan || (int)$d->year !== (int)$tahun) continue;

                    $idx = $d->dayOfWeekIso; // 1..7 (Senin..Minggu)
                    $nilai = (int)($t->{$hariMap[$idx]} ?? 0);
                    if ($nilai > 0) {
                        $tgl = $d->toDateString();
                        // akumulasi jika ada lebih dari satu baris menyentuh tanggal yang sama
                        $targetByDate[$tgl] = ($targetByDate[$tgl] ?? 0) + $nilai;
                    }
                }
            }
        }

        // sales_number milik outlet (pakai SRDR saja)
        $filteredSalesNumbers = DB::table('srdr')
            ->select('sales_number')
            ->where('branch', $outlet)
            ->distinct();
        // $filteredSalesNumbers = DB::table('srdr')
        //     ->select('sales_number')
        //     ->where('branch', $outlet)
        //     ->distinct();

        $salesData = DB::table('srdr')
                        ->select(
                            'sales_number',
                            DB::raw('0 as grand_total'),
                            DB::raw('DATE(sales_date) as tanggal')
                        )
                        ->where('branch', $outlet)
                        ->whereMonth('sales_date', $bulan)
                        ->whereYear('sales_date', $tahun)
                        ->distinct();

        // $salesData = DB::table('srr')
        //     ->select('sales_number', 'grand_total', DB::raw('DATE(sales_date) as tanggal'))
        //     ->whereIn('sales_number', $filteredSalesNumbers)
        //     ->whereMonth('sales_date', $bulan)
        //     ->whereYear('sales_date', $tahun)
        //     ->distinct();

        // Subquery: Nett sales per sales_number
        $nettSalesData = DB::table('srdr')
                            ->select('sales_number', DB::raw('SUM(nett_sales) as total_nett_sales'))
                            ->groupBy('sales_number');
        // $nettSalesData = DB::table('srdr')
        //     ->select('sales_number', DB::raw('SUM(nett_sales) as total_nett_sales'))
        //     ->groupBy('sales_number');
        
        $billPerDate = DB::table('srdr')
                        ->where('branch', $outlet)
                        ->whereMonth('sales_date', $bulan)
                        ->whereYear('sales_date', $tahun)
                        ->select(
                            DB::raw('DATE(sales_date) as tanggal'),
                            DB::raw('0 as bill')
                        )
                        ->groupBy(DB::raw('DATE(sales_date)'));
        // $billPerDate = DB::table('srr')
        //     ->join('srdr', 'srr.sales_number', '=', 'srdr.sales_number')
        //     ->where('srdr.branch', $outlet)
        //     ->whereMonth('srr.sales_date', $bulan)
        //     ->whereYear('srr.sales_date', $tahun)
        //     ->select(
        //         DB::raw('DATE(srr.sales_date) as tanggal'),
        //         DB::raw('COUNT(DISTINCT srr.sales_number) as bill')
        //     )
        //     ->groupBy(DB::raw('DATE(srr.sales_date)'));
        
        // Step 1 PAX: Ambil daftar sales_number unik dari srdr, dengan tanggalnya dari srr
        $salesNumberPerTanggal = DB::table('srdr')
            ->join('srr', 'srdr.sales_number', '=', 'srr.sales_number')
            ->where('srdr.branch', $outlet)
            ->whereMonth('srr.sales_date', $bulan)
            ->whereYear('srr.sales_date', $tahun)
            ->select(
                DB::raw('DISTINCT srdr.sales_number'),
                DB::raw('DATE(srr.sales_date) as tanggal'),
                'srr.pax'
            );

        // Step 2 PAX: Hitung pax berdasarkan srr, hanya untuk sales_number yang valid
        $paxPerDate = DB::table('srdr')
                        ->where('branch', $outlet)
                        ->whereMonth('sales_date', $bulan)
                        ->whereYear('sales_date', $tahun)
                        ->select(
                            DB::raw('DATE(sales_date) as tanggal'),
                            DB::raw('0 as pax')
                        )
                        ->groupBy(DB::raw('DATE(sales_date)'));
        // $paxPerDate = DB::table(DB::raw("({$salesNumberPerTanggal->toSql()}) as pax_src"))
        //     ->mergeBindings($salesNumberPerTanggal)
        //     ->select(
        //         'tanggal',
        //         DB::raw('SUM(pax) as pax')
        //     )
        //     ->groupBy('tanggal');
        
        $foodPerDate = DB::table('srdr')
                        ->where('branch', $outlet)
                        ->whereMonth('sales_date', $bulan)
                        ->whereYear('sales_date', $tahun)
                        ->select(
                            DB::raw('DATE(sales_date) as tanggal'),
                            DB::raw("SUM(CASE WHEN LOWER(menu_category) = 'food' THEN qty ELSE 0 END) as food"),
                            DB::raw("SUM(CASE WHEN LOWER(menu_category) = 'beverage' THEN qty ELSE 0 END) as beverage")
                        )
                        ->groupBy(DB::raw('DATE(sales_date)'));
        // $foodPerDate = DB::table('srdr')
        //     ->where('branch', $outlet)
        //     ->whereMonth('sales_date', $bulan)
        //     ->whereYear('sales_date', $tahun)
        //     ->select(
        //         DB::raw('DATE(sales_date) as tanggal'),
        //         DB::raw("SUM(CASE WHEN LOWER(menu_category) = 'food' THEN qty ELSE 0 END) as food"),
        //         DB::raw("SUM(CASE WHEN LOWER(menu_category) = 'beverage' THEN qty ELSE 0 END) as beverage"),
        //         DB::raw("SUM(CASE WHEN LOWER(menu_category) = 'dessert' THEN qty ELSE 0 END) as dessert")
        //     )
        //     ->groupBy(DB::raw('DATE(sales_date)'));

        // $nettSalesData = DB::table('srdr')
        //     ->select('sales_number', DB::raw('SUM(nett_sales) as total_nett_sales'))
        //     ->groupBy('sales_number');
        // Gabungkan semua

        $query = DB::table(DB::raw("({$salesData->toSql()}) as sales_data"))
                    ->mergeBindings($salesData)
                    ->leftJoinSub($billPerDate, 'bill_data', function ($join) {
                        $join->on('sales_data.tanggal', '=', 'bill_data.tanggal');
                    })
                    ->leftJoinSub($paxPerDate, 'pax_data', function ($join) {
                        $join->on('sales_data.tanggal', '=', 'pax_data.tanggal');
                    })
                    ->leftJoinSub($foodPerDate, 'food_data', function ($join) {
                        $join->on('sales_data.tanggal', '=', 'food_data.tanggal');
                    })
                    ->leftJoinSub($nettSalesData, 'nett_data', function ($join) {
                        $join->on('sales_data.sales_number', '=', 'nett_data.sales_number');
                    })
                    ->select(
                        'sales_data.tanggal',
                        // SALES dipaksa 0 karena SRR tidak dipakai
                        DB::raw('0 as sales'),
                        DB::raw('SUM(COALESCE(nett_data.total_nett_sales, 0)) as nett_sales'),
                        // Bill & PAX dari subquery nol → tetap 0 per tanggal
                        DB::raw('MAX(COALESCE(bill_data.bill, 0)) as bill'),
                        DB::raw('MAX(COALESCE(pax_data.pax, 0)) as pax'),
                        DB::raw('MAX(COALESCE(food_data.food, 0)) as food'),
                        DB::raw('MAX(COALESCE(food_data.beverage, 0)) as beverage')
                    )
                    ->groupBy('sales_data.tanggal')
                    ->orderBy('sales_data.tanggal');
        // $query = DB::table(DB::raw("({$salesData->toSql()}) as sales_data"))
        //     ->mergeBindings($salesData)
        //     ->leftJoinSub($billPerDate, 'bill_data', function ($join) {
        //         $join->on('sales_data.tanggal', '=', 'bill_data.tanggal');
        //     })
        //     ->leftJoinSub($paxPerDate, 'pax_data', function ($join) {
        //         $join->on('sales_data.tanggal', '=', 'pax_data.tanggal');
        //     })
        //     ->leftJoinSub($foodPerDate, 'food_data', function ($join) {
        //         $join->on('sales_data.tanggal', '=', 'food_data.tanggal');
        //     })
        //     ->leftJoinSub($nettSalesData, 'nett_data', function ($join) {
        //         $join->on('sales_data.sales_number', '=', 'nett_data.sales_number');
        //     })
        //     ->select(
        //         'sales_data.tanggal',
        //         DB::raw('SUM(sales_data.grand_total) as sales'),
        //         DB::raw('SUM(COALESCE(nett_data.total_nett_sales, 0)) as nett_sales'),
        //         DB::raw('MAX(COALESCE(bill_data.bill, 0)) as bill'),
        //         DB::raw('MAX(COALESCE(pax_data.pax, 0)) as pax'),
        //         DB::raw('MAX(COALESCE(food_data.food, 0)) as food'),
        //         DB::raw('MAX(COALESCE(food_data.beverage, 0)) as beverage'),
        //         // DB::raw('MAX(COALESCE(food_data.dessert, 0)) as dessert')
        //     )
        //     ->groupBy('sales_data.tanggal')
        //     ->orderBy('sales_data.tanggal');

        return DataTables::of($query)
            ->addColumn('target_sales', function ($row) use ($targetByDate) {
                return $targetByDate[$row->tanggal] ?? 0;
            })
            ->addColumn('avg_bill', function ($row) {
                return 0; // karena sumbernya (bill) berbasis SRR → sementara 0
            })
            ->addColumn('avg_guest', function ($row) {
                return 0; // karena sumbernya (pax) berbasis SRR → sementara 0
            })
            ->skipTotalRecords()
            ->addIndexColumn()
            ->make(true);

    }

    // public function getDataDashboard(Request $request){
    //     $getPromosi = Promosi::select('judul_promosi')->where('is_enabled',1)->get();
    //     $outletIds = Outlet::select('id','nama_outlet')->get();
    //     $brandIds  = Brand::select('id','nama_brand')->get();

    //     return response()->json([
    //         'total_promosi' => $getPromosi->count(),
    //         'total_outlet' => $outletIds->count(),
    //         'total_brand' => $brandIds->count(),
    //     ]);
    // }

    public function getDataDashboard(Request $request){
        $today = Carbon::now();
        $hasDateFilter = $request->start_date || $request->end_date;

        if ($hasDateFilter) {
            $dateStart = $request->start_date ?? $today->toDateString();
            $dateEnd   = $request->end_date ?? $today->toDateString();
            $datePeriode = "CONCAT(DATE_FORMAT('$dateStart', '%d/%m'), ' s/d ', DATE_FORMAT('$dateEnd', '%d/%m/%Y'))";
        } else {
            $datePeriode = "CONCAT(DATE_FORMAT(tb_promosi.mulai_promosi, '%d/%m'), ' s/d ', DATE_FORMAT(CURDATE(), '%d/%m/%Y'))";
        }

        $allData = DB::table('srdr')
            ->join('tb_promosi', 'tb_promosi.id', '=', 'srdr.promosi_id')
            ->join('tb_outlet', 'tb_outlet.id', '=', 'tb_promosi.outlet_id')
            ->leftJoin('tb_promosi_kpi', function($join) {
                $join->on('srdr.promosi_id', '=', 'tb_promosi_kpi.promo_id')
                    ->on('srdr.menu_code', '=', 'tb_promosi_kpi.menu_kode');
            })
            ->where('tb_promosi.is_enabled', 1)
            ->when(!$hasDateFilter, function ($q) use ($today) {
                $q->whereDate('tb_promosi.mulai_promosi', '<=', $today)
                ->whereDate('tb_promosi.akhir_promosi', '>=', $today);
            })
            ->when($request->start_date, function ($q) use ($request) {
                $q->whereDate('srdr.sales_dine_in', '>=', $request->start_date);
            })
            ->when($request->end_date, function ($q) use ($request) {
                $q->whereDate('srdr.sales_dine_in', '<=', $request->end_date);
            })
            ->when($request->brand_id, function ($q) use ($request) {
                $q->where('tb_outlet.brand_id', $request->brand_id);
            })
            ->when($request->outlet_id, function ($q) use ($request) {
                $q->where('tb_promosi.outlet_id', $request->outlet_id);
            })
            ->select(
                'srdr.promosi_id',
                'srdr.brand',
                'tb_promosi.judul_promosi',
                'tb_outlet.nama_outlet',
                DB::raw("DATE_FORMAT(tb_promosi.mulai_promosi, '%d/%m') as mulai"),
                DB::raw("DATE_FORMAT(tb_promosi.akhir_promosi, '%d/%m/%Y') as akhir"),
                DB::raw("$datePeriode as date_periode"),

                // ✅ Total QTY (menu sesuai KPI)
                DB::raw("SUM(CASE WHEN tb_promosi_kpi.menu_kode IS NOT NULL THEN srdr.qty ELSE 0 END) as total_qty"),

                // ✅ Total Sales Promo (total after bill discount sesuai KPI)
                DB::raw("SUM(CASE WHEN tb_promosi_kpi.menu_kode IS NOT NULL THEN srdr.total_after_bill_discount ELSE 0 END) as total_sales_promo"),

                // ✅ Total Sales All (semua transaksi promosi)
                DB::raw("SUM(srdr.total_after_bill_discount) as total_sales_all"),

                // ✅ Persentase
                DB::raw("CASE WHEN SUM(srdr.total_after_bill_discount) > 0 
                            THEN ROUND(SUM(CASE WHEN tb_promosi_kpi.menu_kode IS NOT NULL THEN srdr.total_after_bill_discount ELSE 0 END) 
                            / SUM(srdr.total_after_bill_discount) * 100, 2) 
                            ELSE 0 END as total_sales_all_percent")
            )
            ->groupBy(
                'srdr.promosi_id',
                'srdr.brand',
                'tb_promosi.judul_promosi',
                'tb_outlet.nama_outlet',
                'tb_promosi.mulai_promosi',
                'tb_promosi.akhir_promosi'
            )
            ->get();
        // $today = Carbon::now();
        // $hasDateFilter = $request->start_date || $request->end_date;

        // if ($hasDateFilter) {
        //     $dateStart = $request->start_date ?? $today->toDateString();
        //     $dateEnd   = $request->end_date ?? $today->toDateString();
        //     $datePeriode = "CONCAT(DATE_FORMAT('$dateStart', '%d/%m'), ' s/d ', DATE_FORMAT('$dateEnd', '%d/%m/%Y'))";
        // } else {
        //     $datePeriode = "CONCAT(DATE_FORMAT(tb_promosi.mulai_promosi, '%d/%m'), ' s/d ', DATE_FORMAT(CURDATE(), '%d/%m/%Y'))";
        // }

        // $allData = DB::table('srdr')
        //     ->join('tb_promosi', 'tb_promosi.id', '=', 'srdr.promosi_id')
        //     ->join('tb_outlet', 'tb_outlet.id', '=', 'tb_promosi.outlet_id')
        //     ->leftJoin('tb_promosi_kpi', function($join) {
        //         $join->on('srdr.promosi_id', '=', 'tb_promosi_kpi.promo_id')
        //             ->on('srdr.menu_code', '=', 'tb_promosi_kpi.menu_kode');
        //     })
        //     ->where('tb_promosi.is_enabled', 1)
        //     ->when(!$hasDateFilter, function ($q) use ($today) {
        //         $q->whereDate('tb_promosi.mulai_promosi', '<=', $today)
        //         ->whereDate('tb_promosi.akhir_promosi', '>=', $today);
        //     })
        //     ->when($request->start_date, function ($q) use ($request) {
        //         $q->whereDate('srdr.sales_dine_in', '>=', $request->start_date);
        //     })
        //     ->when($request->end_date, function ($q) use ($request) {
        //         $q->whereDate('srdr.sales_dine_in', '<=', $request->end_date);
        //     })
        //     ->when($request->brand_id, function ($q) use ($request) {
        //         $q->where('tb_outlet.brand_id', $request->brand_id);
        //     })
        //     ->when($request->outlet_id, function ($q) use ($request) {
        //         $q->where('tb_promosi.outlet_id', $request->outlet_id);
        //     })
        //     ->select(
        //         'srdr.promosi_id',
        //         'srdr.brand',
        //         'tb_promosi.judul_promosi',
        //         'tb_outlet.nama_outlet',
        //         DB::raw("DATE_FORMAT(tb_promosi.mulai_promosi, '%d/%m') as mulai"),
        //         DB::raw("DATE_FORMAT(tb_promosi.akhir_promosi, '%d/%m/%Y') as akhir"),
        //         DB::raw($datePeriode . ' as date_periode'),
        //         DB::raw("COUNT(srdr.id) as total_qty"),
        //         DB::raw("SUM(CASE WHEN tb_promosi_kpi.menu_kode IS NOT NULL THEN srdr.total ELSE 0 END) as total_sales_promo"),
        //         DB::raw("SUM(srdr.total) as total_sales_all"),
        //         DB::raw("CASE WHEN SUM(srdr.total) > 0 
        //                     THEN ROUND(SUM(CASE WHEN tb_promosi_kpi.menu_kode IS NOT NULL THEN srdr.total ELSE 0 END) 
        //                     / SUM(srdr.total) * 100, 2) 
        //                     ELSE 0 END as total_sales_all_percent")
        //     )
        //     ->groupBy(
        //         'srdr.promosi_id',
        //         'srdr.brand',
        //         'tb_promosi.judul_promosi',
        //         'tb_outlet.nama_outlet',
        //         'tb_promosi.mulai_promosi',
        //         'tb_promosi.akhir_promosi'
        //     )
        //     ->get();
        // part 2 
        // $today = Carbon::now();
        // $sub = DB::table('srdr')
        //     ->select(DB::raw('MAX(id) as id'))
        //     ->groupBy('promosi_id');
        // // Format tanggal untuk output
        // // Ambil nilai tanggal input (kalau kosong, isi dengan default sekarang)
        // $hasDateFilter = $request->start_date || $request->end_date;

        // // tentukan periode untuk ditampilkan
        // if ($hasDateFilter) {
        //     $dateStart = $request->start_date ?? $today->toDateString();
        //     $dateEnd   = $request->end_date ?? $today->toDateString();
        //     $datePeriode = "CONCAT(DATE_FORMAT('$dateStart', '%d/%m'), ' s/d ', DATE_FORMAT('$dateEnd', '%d/%m/%Y'))";
        // } else {
        //     $datePeriode = "CONCAT(DATE_FORMAT(tb_promosi.mulai_promosi, '%d/%m'), ' s/d ', DATE_FORMAT(CURDATE(), '%d/%m/%Y'))";
        // }

        // $allData = DB::table('srdr')
        //     ->join('tb_promosi', 'tb_promosi.id', '=', 'srdr.promosi_id')
        //     ->join('tb_outlet', 'tb_outlet.id', '=', 'tb_promosi.outlet_id')
        //     ->where('tb_promosi.is_enabled', 1)
        //     ->when(!$hasDateFilter, function ($q) use ($today) {
        //         $q->whereDate('tb_promosi.mulai_promosi', '<=', $today)
        //         ->whereDate('tb_promosi.akhir_promosi', '>=', $today);
        //     })
        //     ->when($request->start_date, function ($q) use ($request) {
        //         $q->whereDate('srdr.sales_dine_in', '>=', $request->start_date);
        //     })
        //     ->when($request->end_date, function ($q) use ($request) {
        //         $q->whereDate('srdr.sales_dine_in', '<=', $request->end_date);
        //     })
        //     ->when($request->brand_id, function ($q) use ($request) {
        //         $q->where('tb_outlet.brand_id', $request->brand_id);
        //     })
        //     ->when($request->outlet_id, function ($q) use ($request) {
        //         $q->where('tb_promosi.outlet_id', $request->outlet_id);
        //     })
        //     ->select(
        //         'srdr.promosi_id',
        //         'srdr.brand',
        //         'tb_promosi.judul_promosi',
        //         'tb_outlet.nama_outlet',
        //         DB::raw("DATE_FORMAT(tb_promosi.mulai_promosi, '%d/%m') as mulai"),
        //         DB::raw("DATE_FORMAT(tb_promosi.akhir_promosi, '%d/%m/%Y') as akhir"),
        //         DB::raw($datePeriode . ' as date_periode'),
        //         DB::raw("COUNT(srdr.id) as total_qty")
        //     )
        //     ->groupBy(
        //         'srdr.promosi_id',
        //         'srdr.brand',
        //         'tb_promosi.judul_promosi',
        //         'tb_outlet.nama_outlet',
        //         'tb_promosi.mulai_promosi',
        //         'tb_promosi.akhir_promosi'
        //     )
        //     ->get();

            // part 1 
        // $allData = DB::table('srdr')
        // ->join('tb_promosi', 'tb_promosi.id', '=', 'srdr.promosi_id')
        // ->join('tb_outlet', 'tb_outlet.id', '=', 'tb_promosi.outlet_id')
        // ->where('tb_promosi.is_enabled', 1)
        // // === Jika tidak ada filter tanggal (default periode aktif) ===
        // ->when(!$request->start_date && !$request->end_date, function ($q) use ($today) {
        //     $q->whereDate('tb_promosi.mulai_promosi', '<=', $today)
        //     ->whereDate('tb_promosi.akhir_promosi', '>=', $today);
        // })
        // // === Jika ada filter tanggal, pakai range srdr ===
        // ->when($request->start_date, function ($q) use ($request) {
        //     $q->whereDate('srdr.sales_dine_in', '>=', $request->start_date); // kolom tanggal srdr
        // })
        // ->when($request->end_date, function ($q) use ($request) {
        //     $q->whereDate('srdr.sales_dine_in', '<=', $request->end_date);   // kolom tanggal srdr
        // })
        // ->when($request->brand_id, function ($q) use ($request) {
        //     $q->where('tb_outlet.brand_id', $request->brand_id);
        // })
        // ->when($request->outlet_id, function ($q) use ($request) {
        //     $q->where('tb_promosi.outlet_id', $request->outlet_id);
        // })
        // ->select(
        //     'srdr.promosi_id',
        //     'tb_promosi.judul_promosi',
        //     'tb_outlet.nama_outlet',
        //     DB::raw("DATE_FORMAT(tb_promosi.mulai_promosi, '%d/%m') as mulai"),
        //     DB::raw("DATE_FORMAT(tb_promosi.akhir_promosi, '%d/%m/%Y') as akhir"),
        //     DB::raw("CONCAT(DATE_FORMAT(tb_promosi.mulai_promosi, '%d/%m'), ' s/d ', DATE_FORMAT(CURDATE(), '%d/%m/%Y')) as date_periode"),
        //     DB::raw("COUNT(srdr.id) as total_qty") // dihitung sesuai filter tanggal
        // )
        // ->groupBy('srdr.promosi_id','tb_promosi.judul_promosi','tb_outlet.nama_outlet','tb_promosi.mulai_promosi','tb_promosi.akhir_promosi')
        // ->get();
        // $allData = DB::table('srdr')
        //     ->joinSub($sub, 'filtered', function ($join) {
        //         $join->on('srdr.id', '=', 'filtered.id');
        //     })
        //     ->join('tb_promosi', 'tb_promosi.id', '=', 'srdr.promosi_id')
        //     ->join('tb_outlet', 'tb_outlet.id', '=', 'tb_promosi.outlet_id')
        //     ->where('tb_promosi.is_enabled', 1)
        //     // === Default filter periode aktif (kalau tidak ada filter tanggal) ===
        //     ->when(!$request->start_date && !$request->end_date, function ($q) use ($today) {
        //         $q->whereDate('tb_promosi.mulai_promosi', '<=', $today)
        //         ->whereDate('tb_promosi.akhir_promosi', '>=', $today);
        //     })
        //     // === Filter berdasarkan tanggal jika ada input ===
        //     ->when($request->start_date, function ($q) use ($request) {
        //         $q->whereDate('tb_promosi.mulai_promosi', '>=', $request->start_date);
        //     })
        //     ->when($request->end_date, function ($q) use ($request) {
        //         $q->whereDate('tb_promosi.akhir_promosi', '<=', $request->end_date);
        //     })
        //     ->when($request->brand_id, function ($q) use ($request) {
        //         $q->where('tb_outlet.brand_id', $request->brand_id);
        //     })
        //     // === Filter outlet ===
        //     ->when($request->outlet_id, function ($q) use ($request) {
        //         $q->where('tb_promosi.outlet_id', $request->outlet_id);
        //     })
        //     ->select(
        //         'srdr.*',
        //         'tb_promosi.judul_promosi',
        //         'tb_outlet.nama_outlet',
        //         DB::raw("DATE_FORMAT(tb_promosi.mulai_promosi, '%d/%m') as mulai"),
        //         DB::raw("DATE_FORMAT(tb_promosi.akhir_promosi, '%d/%m/%Y') as akhir"),
        //         DB::raw("CONCAT(DATE_FORMAT(tb_promosi.mulai_promosi, '%d/%m'), ' s/d ', DATE_FORMAT(CURDATE(), '%d/%m/%Y')) as date_periode"),
        //         DB::raw("(SELECT COUNT(*) FROM srdr s WHERE s.promosi_id = srdr.promosi_id) as total_qty")
        //     )
        //     ->get();

        return Datatables::of($allData)
            ->addIndexColumn()
            ->addColumn('aksi', function ($data) {
                return '
                    <button type="button" onclick="#" class="btn btn-xs btn-success btn-flat"><i class="fas fa-chart-line"></i></button>
                    <button type="button" onclick="#" class="btn btn-xs btn-warning btn-flat" title="Grafik Perbandingan"><i class="fas fa-chart-bar"></i></button>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function getBrands() {
        return Brand::select('id','nama_brand')->get();
    }

    public function getOutlets(Request $request) {
        return Outlet::where('brand_id', $request->brand)->get(['id','nama_outlet']);
    }

    // public function getOutletByBrand($brandId)
    // {
    //     $outlet = Outlet::where('brand_id', $brandId)->get();
    //     return response()->json($outlet);
    // }

    // public function getDataAktual(Request $request)
    // {
    //     $judulPromosi = $request->judul_promosi;
    //     $outlet1 = $request->outlet1;
    //     $outlet2 = $request->outlet2;

    //     // Cari ID promosi berdasarkan judul_promosi
    //     // $promosi = Promosi::where('judul_promosi', $judulPromosi)->get();
    //     $promosiIds = Promosi::where('judul_promosi', $judulPromosi)->pluck('id');

    //     if (!$promosiIds) {
    //         return response()->json([]);
    //     }

    //     // Ambil awal dan akhir bulan ini
    //     $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
    //     $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

    //     // Ambil total_sales per outlet dari tb_aktual
    //     // $data = DB::table('tb_aktual')
    //     //     ->join('tb_outlet', 'tb_outlet.id', '=', 'tb_aktual.outlet_id')
    //     //     ->selectRaw('tb_aktual.outlet_id, MAX(tb_outlet.nama_outlet) as nama_outlet, SUM(sales) as total_sales')
    //     //     ->whereIn('tb_aktual.promosi_id', $promosiIds)
    //     //     ->whereIn('tb_aktual.outlet_id', [$outlet1, $outlet2])
    //     //     ->whereBetween('tb_aktual.promo_date', [$startOfMonth, $endOfMonth])
    //     //     ->groupBy('tb_aktual.outlet_id')
    //     //     ->get();
    //     $data = DB::table('tb_aktual')
    //         ->join('tb_outlet', 'tb_outlet.id', '=', 'tb_aktual.outlet_id')
    //         ->selectRaw('
    //             tb_aktual.outlet_id,
    //             MAX(tb_outlet.kode_outlet) as kode_outlet,
    //             SUM(sales) as total_sales
    //         ')
    //         ->whereIn('tb_aktual.promosi_id', $promosiIds)
    //         ->whereIn('tb_aktual.outlet_id', [$outlet1, $outlet2])
    //         ->whereBetween('tb_aktual.promo_date', [$startOfMonth, $endOfMonth])
    //         ->groupBy('tb_aktual.outlet_id')
    //         ->get();

    //     // Ambil pendapatan_program per outlet dari tb_promosi
    //     $pendapatan = DB::table('tb_promosi')
    //         ->selectRaw('outlet_id, SUM(pendapatan_program) as total_pendapatan')
    //         ->whereIn('id', $promosiIds)
    //         ->whereIn('outlet_id', [$outlet1, $outlet2])
    //         ->groupBy('outlet_id')
    //         ->pluck('total_pendapatan', 'outlet_id');

    //     // Gabungkan ke dalam dua array: salesChart dan conversionChart
    //     $salesChart = [];
    //     $conversionChart = [];

    //     foreach ($data as $item) {
    //         $outletCode = $item->kode_outlet;
    //         $sales = $item->total_sales;
    //         $pendapatanProgram = $pendapatan[$item->outlet_id] ?? 0;

    //         $conversionRate = $sales > 0 ? ($pendapatanProgram / $sales) * 100 : 0;

    //         $salesChart[] = [
    //             'outlet' => $outletCode,
    //             'sales' => $sales,
    //         ];

    //         $conversionChart[] = [
    //             'outlet' => $outletCode,
    //             'conversion_rate' => round($conversionRate, 2),
    //         ];
    //     }

    //     // Return data untuk dua grafik
    //     return response()->json([
    //         'sales_chart' => $salesChart,
    //         'conversion_chart' => $conversionChart,
    //     ]);
    // }

    // public function getSalesPerOutlet(Request $request)
    // {
    //     $judulPromosi = $request->judul_promosi;
    //     $brandId = $request->brand_id;

    //     // 1. Ambil semua outlet dengan brand terkait
    //     $outlets = Outlet::where('brand_id', $brandId)->get();

    //     // 2. Ambil semua promosi dengan judul dan outlet_id yang termasuk dalam brand
    //     $promosiIds = Promosi::where('judul_promosi', $judulPromosi)
    //         ->whereIn('outlet_id', $outlets->pluck('id'))
    //         ->pluck('id');

    //     // 3. Hitung total sales per outlet berdasarkan promosi
    //     $salesPerOutlet = Aktual::select('outlet_id', DB::raw('SUM(sales) as total_sales'))
    //         ->whereIn('promosi_id', $promosiIds)
    //         ->groupBy('outlet_id')
    //         ->get()
    //         ->map(function ($row) {
    //             $outlet = Outlet::find($row->outlet_id);
    //             return [
    //                 'kode_outlet' => $outlet->kode_outlet,
    //                 'total_sales' => $row->total_sales,
    //             ];
    //         });

    //     return response()->json($salesPerOutlet);
    // }


    // public function getAjaxDataComparasi(Request $request){
    //     $promosiId = $request->promosi_id;
    //     $outlet1 = $request->outlet1;
    //     $outlet2 = $request->outlet2;

    //     // Ambil semua ID promosi yang punya judul sama
    //     $promosiIds = Promosi::where('judul_promosi', $promosiId)->pluck('id');

    //     // Ambil data aktual berdasarkan outlet & promosi yang cocok
    //     $data1 = Aktual::with('outlet')
    //         ->whereIn('promosi_id', $promosiIds)
    //         ->where('outlet_id', $outlet1)
    //         // ->orderBy('promo_date')
    //         ->get();

    //     $data2 = Aktual::with('outlet')
    //         ->whereIn('promosi_id', $promosiIds)
    //         ->where('outlet_id', $outlet2)
    //         // ->orderBy('promo_date')
    //         ->get();

    //     // Siapkan data untuk chart
    //     $chartData = [
    //         'categories' => $data1->pluck('promo_date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M')),
    //         'series' => [
    //             [
    //                 'name' => $data1->first()?->outlet->nama_outlet ?? 'Outlet 1',
    //                 'data' => $data1->pluck('sales'),
    //             ],
    //             [
    //                 'name' => $data2->first()?->outlet->nama_outlet ?? 'Outlet 2',
    //                 'data' => $data2->pluck('sales'),
    //             ],
    //         ]
    //     ];

    //     // Table
    //     $tableData = [];

    //     foreach ($data1 as $d) {
    //         $tableData[] = [
    //             // 'promo_date' => $d->promo_date->format('d M Y'),
    //             'outlet' => $d->outlet->nama_outlet,
    //             'traffic' => $d->traffic,
    //             'pax' => $d->pax,
    //             'bill' => $d->bill,
    //             'budget' => $d->budget,
    //             'sales' => $d->sales,
    //         ];
    //     }

    //     foreach ($data2 as $d) {
    //         $tableData[] = [
    //             // 'promo_date' => $d->promo_date->format('d M Y'),
    //             'outlet' => $d->outlet->nama_outlet,
    //             'traffic' => $d->traffic,
    //             'pax' => $d->pax,
    //             'bill' => $d->bill,
    //             'budget' => $d->budget,
    //             'sales' => $d->sales,
    //         ];
    //     }

        
        
    //     return response()->json([
            
    //         'chartData' => $chartData,
    //         'tableData' => $tableData,
    //     ]);
    // }
    

    // public function dataPromotion(Request $request){
    //     $user      = Auth::user();
    //     $brandId   = $request->get('brand_id');
    //     $startDate = $request->get('start_date');
    //     $endDate   = $request->get('end_date');
    //     $groupBy   = $request->get('group_by', 'day');

    //     // 1) Tentukan format DATE_FORMAT sesuai group_by
    //     $fmt = match($groupBy) {
    //         'week'  => '%Y-%u',    // tahun + ISO week number
    //         'month' => '%Y-%m',    // tahun-bulan
    //         'year'  => '%Y',       // tahun
    //         default => '%Y-%m-%d', // harian
    //     };

    //     // 2) Bangun query dasar: join outlet & aktual
    //     $q = DB::table('tb_promosi as p')
    //         ->join('tb_outlet as o',   'o.id', '=', 'p.outlet_id')
    //         ->leftJoin('tb_aktual as a','a.promosi_id', '=', 'p.id')
    //         ->select([
    //             'p.id',
    //             'p.jenis_promosi as promotion',
    //             'o.nama_outlet           as outlet',
    //             'o.brand_id       as brand_id',
    //             DB::raw("DATE_FORMAT(a.promo_date, '$fmt') as period"),
    //             DB::raw('SUM(a.sales)      as total')
    //         ]);

    //     // 3) Filter berdasarkan role
    //     if (! in_array($user->role, ['Super Admin', 'marketing'])) {
    //         $q->where('p.is_enabled', 1)
    //           ->where('p.outlet_id', $user->outlet_id);
    //     }

    //     // 4) Filter brand jika ada
    //     if ($brandId) {
    //         $q->where('o.brand_id', $brandId);
    //     }

    //     // 5) Filter rentang tanggal akhir_promosi jika ada
    //     if ($startDate && $endDate) {
    //         $q->whereBetween('p.akhir_promosi', [$startDate, $endDate]);
    //     }

    //     $rows = $q->groupBy(
    //         'p.id',
    //         'p.jenis_promosi',
    //         'o.nama_outlet',
    //         'o.brand_id',
    //         DB::raw("DATE_FORMAT(a.promo_date, '$fmt')")
    //     )
    //     ->orderBy('period')
    //     ->get();

    //     // 7) Restructure data jadi per-promotion dengan stats[periode] = total
    //     $out = [];
    //     foreach ($rows as $r) {
    //         $key = $r->id;
    //         if (! isset($out[$key])) {
    //             $out[$key] = [
    //                 'promotion' => $r->promotion,
    //                 'outlet'    => $r->outlet,
    //                 'brand_id'  => $r->brand_id,
    //                 'stats'     => []
    //             ];
    //         }
    //         $out[$key]['stats'][$r->period] = (float) $r->total;
    //     }

    //     // 8) Kembalikan array numerik
    //     return response()->json(array_values($out));
    // }

    // public function index()
    // {
    //     $user = Auth::user();
    //     $query = DB::table('tb_promosi')
    //         ->select('tb_promosi.id','outlet_id', 'jenis_promosi','akhir_promosi')
    //         ->join('tb_outlet','tb_outlet.id','=','tb_promosi.outlet_id');

    //     // Cek apakah user bukan superadmin
    //     if ($user->role !== 'Super Admin' && $user->role !== 'Marketing') {
            
    //         $query->where('is_enabled', '1');
    //         $query->where('outlet_id', $user->outlet_id);
    //     }

    //     $data_even = $query->get()->toArray();
        
    //     $outletIds = Outlet::select('id')->get();
    //     $brandIds = Brand::select('id')->get();
    //     // dd($data_even);
    //     // die();
    //     // $outletIds = array_unique(array_column($data_even, 'outlet_id'));

    //     return view('dashboard', ['data_even' => $data_even,'outletIds'=>$outletIds,'brandIds'=>$brandIds]);
    // }

}
