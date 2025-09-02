<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Aktual;
use App\Models\Promosi;
use Yajra\DataTables\Facades\DataTables;
use App\Imports\AktualImport;
use App\Imports\SrdrImport;
use Illuminate\Support\Facades\Auth;
use App\Models\Srdr;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\AktualResource;
use App\Http\Resources\PromosiResource;
use App\Http\Resources\BrandResource;
use App\models\Brand;
use App\Exports\SrdrForCsvExport;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use App\Imports\SrdrStreamConverter;

class AktualController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         return view('aktual.index'); // sesuaikan dengan view kamu
    }

    public function importAktual(){
        return view('aktual.import_akunting');
    }

    public function apiIndex(){
        // $aktual_data = Aktual::with('promosi.outlet')->get();
        $user = auth()->user();

        // $aktual_data = Aktual::when($user->role === 'pic', function ($query) use ($user) {
        //     $query->whereHas('promosi', function ($subQuery) use ($user) {
        //         $subQuery->where('outlet_id', $user->outlet_id);
        //     });
        // })
        // ->with('promosi.outlet') // tetap ambil relasi
        // ->get();

        // $aktual_data = Srdr::get();
        $aktual_data = Srdr::with('promosi')->select('srdr.*')->orderBy('menu_category', 'desc')
                            ->get();

        return Datatables::of($aktual_data)
        ->addIndexColumn()
        ->addColumn('nama_outlet', function ($data) {
            return optional($data->promosi->outlet)->nama_outlet ?? '-';
        })
        ->addColumn('aksi', function ($data) {
            $user = Auth::user();
            $buttons = '';
            if ($user->role === 'aspv' || $user->role === 'Super Admin') {
                $buttons .= '<button type="button" onclick="editForm(`/aktualAPI/'.$data->id.'/edit`)" class="btn btn-xs btn-success btn-flat"><i class="fas fa-edit"></i></button>';
                $buttons .= '<button type="button" onclick="deleteData(`/aktualAPI/deleteAktual/'. $data->id.'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>';
            }elseif($user->role === 'pic'){
                $buttons ='<span style="
                    background-color: #eb9a20ff;
                    color: white;
                    padding: 3px 8px;
                    border-radius: 5px;
                    font-size: 0.575rem;
                ">Fitur tidak tersedia</span>';
            }
            return $buttons;
        })
        ->rawColumns(['aksi'])
        ->make(true);
        // return response()->json($aktual_data, 200);
        // return response()->json([
        //     'aktual' => AktualResource::collection($aktual_data)
        // ]);

    }

    public function importSrdr(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new SrdrImport, $request->file('file'));

        return response()->json(['message' => 'Import selesai']);        
    }

    public function setup(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls']);

        $fileName = uniqid('import_') . '.csv';
        $filePath = storage_path('app/temp/' . $fileName);
        File::ensureDirectoryExists(storage_path('app/temp'));

        // Buka file handle untuk menulis CSV
        $fileHandle = fopen($filePath, 'w');

        // Siapkan class konverter dengan file handle
        $converter = new SrdrStreamConverter($fileHandle);
        
        // Tulis header ke file CSV terlebih dahulu
        fputcsv($fileHandle, $converter->getHeadings());

        // Mulai proses impor-konversi secara streaming
        Excel::import($converter, $request->file('file'));

        // Tutup file handle
        fclose($fileHandle);
        
        $totalRows = $converter->getProcessedRowCount();

        if ($totalRows === 0) {
            return response()->json(['error' => 'Tidak ada data yang ditemukan di file Excel.'], 422);
        }

        // Simpan info di session untuk permintaan selanjutnya
        session([
            'import_file' => $filePath,
            'import_total_rows' => $totalRows,
            'import_headings' => $converter->getHeadings()
        ]);

        return response()->json([
            'total_rows' => $totalRows,
            'file_name' => $fileName
        ]);
    }

    /**
     * Tahap 2: Memproses satu chunk dari file CSV yang sudah ada.
     */
    public function process(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = 500; // Proses 500 baris per permintaan

        $filePath = session('import_file');
        $totalRows = session('import_total_rows');
        $headings = session('import_headings');

        if (!$filePath || !File::exists($filePath)) {
            return response()->json(['error' => 'File impor tidak ditemukan. Silakan mulai dari awal.'], 422);
        }

        // Membaca CSV menggunakan SPL (sangat efisien memori)
        $file = new \SplFileObject($filePath, 'r');
        $file->seek($offset + 1); // +1 untuk melewati header jika offset 0
        
        $batchValues = [];
        $rowsRead = 0;
        while ($rowsRead < $limit && !$file->eof()) {
            $row = $file->fgetcsv();
            if ($row && isset($row[0])) { // Pastikan baris tidak kosong
                $batchValues[] = $row;
            }
            $rowsRead++;
        }
        
        // Jika tidak ada data di batch ini, anggap selesai untuk chunk ini
        if (!empty($batchValues)) {
            // 1. Siapkan daftar kolom (dijamin urutannya benar)
            $columnsSql = '`' . implode('`, `', $headings) . '`';

            // 2. Buat placeholder '?' untuk satu baris
            $rowPlaceholders = '(' . implode(', ', array_fill(0, count($headings), '?')) . ')';
            
            // 3. Gandakan placeholder untuk seluruh batch
            $sql = "INSERT INTO `srdr` ($columnsSql) VALUES " . implode(', ', array_fill(0, count($batchValues), $rowPlaceholders));

            // 4. Ratakan semua nilai dari batch menjadi satu array untuk binding
            $bindings = [];
            foreach ($batchValues as $row) {
                foreach ($row as $value) {
                    // Ganti string kosong dengan null agar cocok dengan tipe data database
                    $bindings[] = ($value === '') ? null : $value;
                }
            }
            
            // 5. Eksekusi query mentah (sangat cepat dan aman dari SQL Injection)
            DB::insert($sql, $bindings);
        }

        $newOffset = $offset + $rowsRead;

        if ($newOffset >= $totalRows) {
            File::delete($filePath);
            session()->forget(['import_file', 'import_total_rows', 'import_headings']);
        }

        return response()->json([
            'processed_rows' => count($batchValues),
            'total_processed' => min($newOffset, $totalRows),
            'total_rows' => $totalRows
        ]);
    }

    public function editFormSrdr()
    {
        // dd('jalane');
        // die();
        return view('aktual.import_editSrdr'); // Kita akan buat view ini
    }

    // public function setupUpdate(Request $request)
    // {
    //     ini_set('memory_limit', '-1');
    //     set_time_limit(0);

    //     $request->validate(['file' => 'required|mimes:xlsx,xls']);

    //     // --- Proses Konversi Excel ke CSV (sama seperti setup biasa) ---
    //     $fileName = uniqid('import_') . '.csv';
    //     $filePath = storage_path('app/temp/' . $fileName);
    //     File::ensureDirectoryExists(storage_path('app/temp'));
    //     $fileHandle = fopen($filePath, 'w');
    //     $converter = new SrdrStreamConverter($fileHandle);
    //     fputcsv($fileHandle, $converter->getHeadings());
    //     Excel::import($converter, $request->file('file'));
    //     fclose($fileHandle);
        
    //     // --- PROSES PENTING: HAPUS DATA LAMA ---
    //     $uniqueKeys = $converter->getUniqueKeys();

    //     if (!empty($uniqueKeys)) {
    //         // Gunakan transaction untuk memastikan proses aman
    //         DB::transaction(function () use ($uniqueKeys) {
    //             DB::table('srdr')->where(function ($query) use ($uniqueKeys) {
    //                 foreach ($uniqueKeys as $key) {
    //                     $query->orWhere(function ($subQuery) use ($key) {
    //                         $subQuery->where('branch', $key['branch'])
    //                                  ->where('brand', $key['brand'])
    //                                  ->whereDate('sales_date', $key['sales_date']);
    //                     });
    //                 }
    //             })->delete();
    //         });
    //     }
        
    //     $totalRows = $converter->getProcessedRowCount();

    //     if ($totalRows === 0) {
    //         return response()->json(['error' => 'Tidak ada data yang ditemukan di file Excel.'], 422);
    //     }

    //     // Simpan info di session (sama seperti setup biasa)
    //     session([
    //         'import_file' => $filePath,
    //         'import_total_rows' => $totalRows,
    //         'import_headings' => $converter->getHeadings()
    //     ]);

    //     return response()->json([
    //         'total_rows' => $totalRows,
    //         'deleted_keys_count' => count($uniqueKeys) // Info tambahan untuk frontend
    //     ]);
    // }

    public function setupUpdate(Request $request)
    {
        // ini_set('memory_limit', '-1');
        // set_time_limit(0);
        $request->validate(['file' => 'required|mimes:xlsx,xls']);

        // --- TAHAP 1: Konversi Excel ke CSV & Ekstrak Kunci Unik ---
        $fileName = uniqid('import_') . '.csv';
        $filePath = storage_path('app/temp/' . $fileName);
        File::ensureDirectoryExists(storage_path('app/temp'));
        
        $fileHandle = fopen($filePath, 'w');
        $converter = new \App\Imports\SrdrStreamConverter($fileHandle);
        fputcsv($fileHandle, $converter->getHeadings());
        Excel::import($converter, $request->file('file'));
        fclose($fileHandle);
        
        $uniqueKeys = $converter->getUniqueKeys();
        $totalRows = $converter->getProcessedRowCount();

        if ($totalRows === 0) {
            return response()->json(['error' => 'Tidak ada data yang ditemukan di file Excel.'], 422);
        }

        // --- TAHAP 2: Hapus Data Lama di Database ---
        $deletedRows = 0;
        if (!empty($uniqueKeys)) {
            // Menggunakan strategi 'whereBetween' yang andal
            $deletedRows = DB::table('srdr')->where(function ($query) use ($uniqueKeys) {
                foreach ($uniqueKeys as $key) {
                    if (!empty($key['branch']) && !empty($key['brand']) && !empty($key['sales_date'])) {
                        $date = $key['sales_date']; // Format Y-m-d
                        $query->orWhere(function ($subQuery) use ($key, $date) {
                            $subQuery->where('branch', $key['branch'])
                                     ->where('brand', $key['brand'])
                                     ->whereBetween('sales_date', [$date . ' 00:00:00', $date . ' 23:59:59']);
                        });
                    }
                }
            })->delete();
        }

        // --- TAHAP 3: Siapkan Sesi untuk Impor per-chunk ---
        session([
            'import_file' => $filePath,
            'import_total_rows' => $totalRows,
            'import_headings' => $converter->getHeadings(),
        ]);

        return response()->json([
            'total_rows' => $totalRows,
            'deleted_rows_count' => $deletedRows
        ]);
    }
    

    public function getDataFormAktual(){
        // $aktual_data = Aktual::with('promosi.outlet.brand')->get();
        $promosi_data = Promosi::with('outlet.brand')->get();
        // $data_brand_outlet = Brand::with('outlets')->get();
        return response()->json([
            'success' => true,
            'code' => 200,
            'data' => $promosi_data,
        ]);
    }

    public function downloadTemplet(){
        $filePath = public_path('files/Format_Import_SRDR.xlsx');

        if (!file_exists($filePath)) {
            abort(404, 'Template file not found.');
        }

        return response()->download($filePath, 'Template-SRDR.xlsx');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    // public function apiStore(Request $request){
    //     // dd($request);
    //     // die();
    //     $request->validate([
    //         'file' => 'required|mimes:xlsx,xls'
    //     ], [
    //         'file.required' => 'file excel wajib diupload'
    //     ]);

    //     $store = Excel::import(
    //         new AktualImport($request->promosi_id, $request->outlet_id),
    //         $request->file('file')
    //     );
    //     // dd($store);
    //     // die();
    //     return back()->with('success', 'Data berhasil diimport!');
    // }

    public function srdrStore(Request $request){
        // ini_set('max_excecution_time',3600);
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '1024M');
        Excel::import(new AktualImport($request->promosi_id, $request->mode_import), $request->file('file'));

        return response()->json([
            'message'        => 'Import berhasil',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        $aktual = Srdr::findOrFail($id);
        return response()->json($aktual);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $aktual = Srdr::findOrFail($id);
        $aktual->update([
            // 'promosi_id' => $aktual->promosi_id,
            'sales_number' => $request->sales_number,
            // 'sales_date' => $request->promosi_id,
            // 'sales_dine_in' => $request->promosi_id,
            // 'sales_dine_out' => $request->promosi_id,
            'branch' => $request->branch,
            'brand' => $request->brand,
            'city' => $request->city,
            'visit_purpose' => $request->visit_purpose,
            'payment_method' => $request->payment_method,
            'menu_category' => $request->menu_category,
            'menu_category_detail' => $request->menu_category_detail,
            'menu' => $request->menu,
            'menu_code' => $request->menu_code,
            'order_mode' => $request->order_mode,
            'qty' => $request->qty,
            'price' => $request->price,
            'subtotal' => $request->subtotal,
            'discount' => $request->discount,
            'total' => $request->total,
            'nett_sales' => $request->nett_sales,
            'bill_discount' => $request->bill_discount,
            'total_after_bill_discount' => $request->total_after_bill_discount,
            'waiters' => $request->waiters,
        ]);

        return response()->json([
            'success' => true,
            'code' => 200,
            'Message' => 'Data berhasil di update',
        ]);
        // return redirect()->back()->with('success', 'Data aktual berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $aktual = Srdr::findOrFail($id);
        $aktual->delete();

        return response()->json(['message' => 'Data berhasil dihapus']);
    }
}
