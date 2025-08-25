<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Outlet;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\Target_outlet;

class TargetOutletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('target_outlet.index');
    }

    public function getDataOutlet(){
        return DB::table('sub_branch')
        ->select('id', 'nama_sub_branch as name')
        ->orderBy('nama_sub_branch')
        ->get();
        // return DB::table('tb_outlet')
        // ->select('id', 'nama_outlet as name')
        // ->orderBy('nama_outlet')
        // ->get();
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

        $validated = $request->validate([
            'outlet_id'     => 'required|integer',
            'bulan'         => 'required|integer|min:1|max:12',
            'week_number'   => 'required|integer|min:1|max:5',
            'senin'   => 'required|integer|numeric',
            'selasa'      => 'required|numeric',
            'rabu'      => 'required|numeric',
            'kamis'    => 'required|numeric',
            'jumat'       => 'required|numeric',
            'sabtu'       => 'required|numeric',
            'minggu'       => 'required|numeric',
        ]);

        // simpan ke DB
        Target_outlet::create([
            'sub_branch_id'    => $validated['outlet_id'],
            // 'month'        => now()->setMonth($validated['bulan'])->startOfMonth(), // simpan sebagai date
            'month'        => date('Y') . '-' . str_pad((int)$validated['bulan'], 2, '0', STR_PAD_LEFT) . '-01',
            'week_number'  => $validated['week_number'],
            'senin'     => $validated['senin'],
            'selasa'     => $validated['selasa'],
            'rabu'   => $validated['rabu'],
            'kamis'      => $validated['kamis'],
            'jumat'      => $validated['jumat'],
            'sabtu'      => $validated['sabtu'],
            'minggu'      => $validated['minggu'],
        ]);

        return response()->json(['message' => 'Target berhasil disimpan'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        // Jika parameter kosong langsung return array kosong
        if (empty($request->outlet_id) || empty($request->bulan)) {
            return response()->json([]);
        }
        // Validasi input
        $validated = $request->validate([
            'outlet_id' => 'required|integer',
            'bulan'     => 'required|integer|min:1|max:12',
        ]);

        // Generate tanggal awal bulan untuk filter
        $month = date('Y') . '-' . str_pad((int)$validated['bulan'], 2, '0', STR_PAD_LEFT) . '-01';

        // Ambil data target berdasarkan outlet & bulan
        // Query
        $data = Target_outlet::where(function($q) use ($validated) {
                    $q->where('outlet_id', $validated['outlet_id'])
                    ->orWhere('sub_branch_id', $validated['outlet_id']);
                })
                ->where('month', $month)
                ->orderBy('week_number', 'asc')
                ->get();

        return response()->json($data);
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
        //
    }
}
