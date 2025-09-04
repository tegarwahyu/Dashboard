<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Outlet;
use App\Models\Outlet_salary;

class SalarOutletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('salary_outlet.index');
    }

    public function getDataSalaryOutlet(){
        $outletIds = Outlet::select('id','nama_outlet')->get();

        return response()->json([
            'data_outlet' => $outletIds
        ]);
    }

    public function getList(Request $request){
        $data = Outlet_salary::join('sub_branch as o', 'o.id', '=', 'outlet_salary_monthly.sub_branch_id')
            ->select(
                'outlet_salary_monthly.id',
                'o.nama_sub_branch as outlet_name',
                'outlet_salary_monthly.total_salary',
                'outlet_salary_monthly.month'
            );
        // $data = Outlet_salary::all();
        // dd($data->get());
        // die();

        return DataTables::of($data)
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-danger deleteBtn" data-id="'.$row->id.'">Hapus</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
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
        // dd($request->outlet_id);
        // die();
        $validated = $request->validate([
            'outlet_id' => 'required|integer|exists:tb_outlet,id',
            'nominal'   => 'required|numeric',
            'periode'   => 'required|date_format:Y-m',
        ]);

        // Ubah format periode (Y-m jadi Y-m-01)
        $periode = $validated['periode'].'-01';

        // Simpan data
        Outlet_salary::create([
            'sub_branch_id' => $request->outlet_id,
            'total_salary'  => $validated['nominal'],
            'month'         => $periode,
        ]);

        return response()->json(['message' => 'Target berhasil disimpan'], 200);
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Outlet_salary::findOrFail($id)->delete();
        return response()->json(['message' => 'Data berhasil dihapus']);
    }
}
