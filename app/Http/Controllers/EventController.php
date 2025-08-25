<?php

namespace App\Http\Controllers;

use App\models\Brand;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Outlet;
use App\Models\Promosi;
use App\Imports\PromosiExport;
use App\Models\PromosiKPI;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data_outlet = Brand::with('outlets')->orderBy('nama_brand')->get();

        
        // dd($query->get()->toArray());
        // die();
        // $data_even = $query->get()->toArray();
        
        return view('event.index', ['data_outlet' => $data_outlet]);
    }

    public function getDataEvent(){
        $user = Auth::user();
        $query = DB::table('tb_promosi')
            ->select(
                'tb_promosi.id',
                'judul_promosi',
                'img_path',
                'deskripsi',
                'schedule_status',
                'mulai_promosi',
                'akhir_promosi',
                'outlet_id',
                // 'jenis_promosi',
                'tb_outlet.nama_outlet',
                'is_enabled'
            )
            ->join('tb_outlet', 'tb_outlet.id', '=', 'tb_promosi.outlet_id');

        // Super Admin, Marketing, ASPV, MA => lihat semua data
        if ($user->role === 'pic') {
            // Marketing hanya data aktif
            $query->where('is_enabled', 1);

        } elseif (!in_array($user->role, ['Super Admin', 'aspv', 'ma', 'marketing'])) {
            // Role lain => hanya data aktif & filter uploaded_by & outlet
            $query->where('uploaded_by', Auth()->user()->id)
                ->where('is_enabled', 1)
                ->where('outlet_id', $user->outlet_id);
        }
        $query->get();
        return Datatables::of($query)
        ->addIndexColumn()
        ->addColumn('mulai_promosi', function($data) {
            return \Carbon\Carbon::parse($data->mulai_promosi)->format('Y-m-d');
        })
        ->addColumn('akhir_promosi', function($data) {
            return \Carbon\Carbon::parse($data->akhir_promosi)->format('Y-m-d');
        })
        ->addColumn('deskripsi', function ($data) {
            $maxLength = 100;
            $desc = strip_tags($data->deskripsi); // Amankan jika ada HTML

            if (strlen($desc) <= $maxLength) {
                return nl2br(e($desc));
            }

            // Pakai Str::limit atau substr
            $short = Str::limit($desc, $maxLength);

            return nl2br(e($short))
                . ' <button class="btn btn-link btn-sm text-primary show-desc" data-desc="'.e($desc).'">
                    <i class="fa fa-eye"></i> Lihat
                </button>';
        })
        ->addColumn('status', function($data) {
            if ($data->is_enabled == '1') {
                return '<span style="
                    background-color: #28a745;
                    color: white;
                    padding: 3px 8px;
                    border-radius: 5px;
                    font-size: 0.875rem;
                ">Active</span>';
            } elseif ($data->is_enabled == '0' && $data->is_enabled != '1') {
                return '<span style="
                    background-color: #dc3545;
                    color: white;
                    padding: 3px 8px;
                    border-radius: 5px;
                    font-size: 0.875rem;
                ">Inactive</span>';
            } else {
                return '<span style="
                    background-color:rgb(243, 255, 7);
                    color: black;
                    padding: 3px 8px;
                    border-radius: 5px;
                    font-size: 0.875rem;
                ">Butuh Aktivasi</span>';
            }
        })
        ->addColumn('gambar', function($data) {
            if ($data->img_path) {
                return '<img src="'.asset($data->img_path).'" width="100" class="img-thumbnail" style="cursor:pointer">';
            }
            return 'Tidak ada gambar';
        })
        ->addColumn('aksi', function($data) {
            $user = Auth::user();
            $buttons = '';

            // Tombol Ubah
            $buttons .= '
                <button class="btn btn-sm btn-primary edit-btn" data-id="'.$data->id.'">
                    Ubah
                </button> ';

            // Tombol Activate / Deactivate
            $statusClass = ($data->is_enabled == 1) ? 'btn-warning' : 'btn-success';
            $statusLabel = ($data->is_enabled == 1) ? 'Deactivated' : 'Activated';

            
            $buttons .= '
                <button class="btn btn-sm '.$statusClass.' toggle-status-btn change-status-btn"
                    data-id="'.$data->id.'">
                    '.$statusLabel.'
                </button> ';

            // Tombol Hapus hanya untuk Marketing & Super Admin
            if ($user->role === 'aspv' || $user->role === 'Super Admin') {
                $buttons .= '
                    <button class="btn btn-sm btn-danger delete-btn"
                        data-id="'.$data->id.'">
                        Hapus
                    </button>';
            }

            return $buttons;
        })
        ->rawColumns(['gambar','aksi', 'status','deskripsi'])
        ->make(true);
    }

    public function getOutletEditByBrand($id){
        $outlets = Promosi::with(['outlet','promosi_kip'])->get();
        return response()->json($outlets);
    }

    public function getOutletByBrand($id){
        $outlets = Outlet::where('brand_id', $id)
                     ->select('id', 'nama_outlet')
                     ->get();
        // $outlets = DB::table('tb_promosi as p')
        //                 ->join('tb_outlet as o', 'p.outlet_id', '=', 'o.id')
        //                 ->leftJoin('tb_promosi_kpi as kpi', 'p.id', '=', 'kpi.promo_id')
        //                 ->where('o.brand_id', $id)
        //                 ->select(
        //                     'o.id as outlet_id',
        //                     'o.nama_outlet',
        //                     'p.id as promosi_id',
        //                     'p.judul_promosi',
        //                     'kpi.traffic',
        //                     'kpi.pax',
        //                     'kpi.bill',
        //                     'kpi.budget',
        //                     'kpi.sales'
        //                 )
        //                 ->get();

        // dd($outlets);
        // die();

        return response()->json($outlets);
    }

    // Di MenuTemplateController.php
    public function getMenuTemplate()
    {
        $data = DB::table('master_menu_template_data')->select('id','menu_code', 'menu_name')->get();
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        // die();
        $request->validate([
            'judul' => 'required|string',
            'deskripsi' => 'required|string',
            'poster' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'judul.required' => 'Deskripsi event wajib diisi.',
            'deskripsi.required' => 'Deskripsi event wajib diisi.',
            'poster.image' => 'File logo harus berupa gambar.',
            'poster.mimes' => 'Logo hanya boleh berformat jpeg, png, atau jpg.',
            'poster.max' => 'Ukuran logo maksimal 1.5MB.'
        ]);
        
         // Format nama file: judul_outletid.jpg
        $judulSlug = Str::slug($request->judul, '_');
        $fileName = $judulSlug . '.' . $request->poster->getClientOriginalExtension();

        // Path folder tujuan
        $destinationPath = public_path('poster_promosi');

        // Buat folder jika belum ada
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        // Simpan file ke public/poster_promosi
        $request->poster->move($destinationPath, $fileName);

        foreach ($request->outlet_id as $outletId) {
            // 1) Insert ke tabel promosi
            $dataPromosi = [
                'target_sales' =>  json_encode($request->unit_type),
                'judul_promosi'   => $request->judul,
                'img_path'        => 'poster_promosi/' . $fileName,
                'deskripsi'       => $request->deskripsi,
                'schedule_status' => now()->lt($request->mulaiPromosi) ? 'Scheduled' : 'Active',
                'is_enabled'      => null,
                'mulai_promosi'   => $request->mulaiPromosi,
                'akhir_promosi'   => $request->akhirPromosi,
                'outlet_id'       => $outletId,
                'branch_id'       => $outletId,
                'uploaded_by'     => Auth()->user()->id
            ];
            // dd($dataPromosi);
            // die();
            
            $promosi = Promosi::create($dataPromosi);

            foreach ($request->menu_kode as $index => $kode) {
                PromosiKpi::create([
                    'promo_id' => $promosi->id,
                    'menu_kode' => $kode,
                    'menu_nama' => $request->menu_name[$index],
                    'qty_target' => $request->target_sales[$request->menu_name[$index]]['qty'] ?? 0,
                    'rupiah_target' => $request->target_sales[$request->menu_name[$index]]['rupiah'] ?? 0,
                ]);
            }
        }

        return redirect()->route('marketing')->with('message', 'Berhasil disimpan');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $promosi = Promosi::with('outlet.brand','promosi_kip')->findOrFail($id);
        return response()->json($promosi);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // dd($request);
        // die();
        $id_promosi = $request->id;
        $id_promosi_kpi = $request->promosi_kpi_id;

        $promosi = Promosi::findOrFail($id_promosi);

        // Siapkan data untuk update promosi
        $data_updated_promosi = [
            'judul_promosi' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'img_path' => $request->img_path,
            'mulai_promosi' => $request->mulai_date_edit,
            'akhir_promosi' => $request->akhir_date_edit,
            'outlet_id' => $request->outlet_id2[0] ?? null,
            'jenis_promosi' => $request->jenis_event,
        ];
        // 2) Ambil JSON KPI
        $kpis = json_decode($request->outletDateTimeField2, true);

        // 3) Loop update KPI satu per satu
        if ($kpis && is_array($kpis)) {
            foreach ($kpis as $kpi) {                
                if (!empty($id_promosi_kpi)) {
                    $promosiKPI = PromosiKPI::find($id_promosi_kpi);
                    if ($promosiKPI) {
                        $promosiKPI->update([
                            'promo_id' => $promosi->id,
                            'traffic' => $kpi['traffic'] ?? 0,
                            'pax' => $kpi['pax'] ?? 0,
                            'bill' => $kpi['bill'] ?? 0,
                            'budget' => $kpi['budget'] ?? 0,
                            'sales' => $kpi['sales'] ?? 0,
                        ]);
                    }
                }
            }
        }
        $promosi->update($data_updated_promosi);

        return response()->json([
            'success' => true,
            'code' => 200,
            'Message' => 'Data berhasil di update',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */

    public function deactivated(string $id)
    {
        // dd($id);
        // die();
        $promosi = Promosi::findOrFail($id);

        // Toggle status
        $promosi->is_enabled = $promosi->is_enabled == 1 ? 0 : 1;
        $promosi->save();

        return response()->json(['message' => 'Data berhasil diubah']);
    }

    public function destroy(string $id)
    {
        $promosi = Promosi::findOrFail($id);

        // Hapus gambar jika ada
        if ($promosi->img_path && file_exists(public_path($promosi->img_path))) {
            unlink(public_path($promosi->img_path));
        }

        $promosi->delete();
        return response()->json(['message' => 'Data berhasil dihapus']);
    }

    public function apiExportgetData(Request $request){
        $start = $request->input('start_date');
        $end = $request->input('end_date');

        // $promosi = Promosi::whereBetween('mulai_promosi', [$start, $end])->get();
        $promosi = Promosi::whereDate('mulai_promosi', '<=', $end)
                      ->whereDate('akhir_promosi', '>=', $start)
                      ->get();
        // dd($promosi);
        // die();
        return response()->json($promosi);
    }

    public function export(Request $request){
         $dateRange = $request->date_range; // ex: "2025-07-03 s/d 2025-07-07"
        $judul = $request->export_judul_promosi;

        if (!$dateRange || !str_contains($dateRange, ' s/d ')) {
            return back()->with('error', 'Rentang tanggal tidak valid!');
        }

        [$startDate, $endDate] = explode(' s/d ', $dateRange);

        $data = Promosi::with('outlet.brand','promosi_kip')->where('judul_promosi', $judul)
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('mulai_promosi', [$startDate, $endDate])
                    ->orWhereBetween('akhir_promosi', [$startDate, $endDate])
                    ->orWhere(function ($q2) use ($startDate, $endDate) {
                        $q2->where('mulai_promosi', '<=', $startDate)
                            ->where('akhir_promosi', '>=', $endDate);
                    });
            })->get();
        // dd($data);
        // die();
        // Jika mau di-download Excel
        return Excel::download(new PromosiExport($data), 'export_promosi.xlsx');
    }
}
