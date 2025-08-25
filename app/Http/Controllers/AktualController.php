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
// use App\Imports\SrdrImport;

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
