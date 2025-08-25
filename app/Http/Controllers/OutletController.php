<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\models\Brand;
use App\models\Outlet;

class OutletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data_brand = Brand::select('id','nama_brand')->get();
        $query = DB::table('tb_outlet')
            ->select('brand.nama_brand as nama_brand','tb_outlet.*')
            ->join('brand','brand.id','=','tb_outlet.brand_id');

        $outlet_data = $query->get()->toArray();
        return view('outlet.index', ['outlet_data' => $outlet_data,'data_brand'=> $data_brand]);
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
            'nama_outlet' => 'required|string|unique:tb_outlet,nama_outlet',
            'brand_id' => 'required|string',
            'kode_outlet' => 'required|string',
            'lokasi' => 'required|string',
        ], [
            'nama_outlet.unique' => 'Nama outlet sudah ada.',
            'nama_outlet.required' => 'Nama outlet wajib diisi.',
            'brand_id.required' => 'silahkan pilih brand karena wajib diisi.',
            'kode_outlet.required' => 'kode outlet wajib diisi.',
            'lokasi.required' => 'lokasi outlet wajib diisi.'
        ]);

        // === Simpan ke database ===
        Outlet::create([
            'kode_outlet' => $request->kode_outlet,
            'brand_id' => $request->brand_id,
            'nama_outlet' => $request->nama_outlet,
            'lokasi' => $request->lokasi,
        ]);

        return redirect()->route('outlet')->with('message', 'Berhasil disimpan!');
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
        $request->validate([
            'nama_outlet' => 'required|string',
            'brand_id' => 'required|string',
            'kode_outlet' => 'required|string',
            'lokasi' => 'required|string',
        ], [
            'nama_outlet.unique' => 'Nama outlet sudah ada.',
            'nama_outlet.required' => 'Nama outlet wajib diisi.',
            'brand_id.required' => 'silahkan pilih brand karena wajib diisi.',
            'kode_outlet.required' => 'kode outlet wajib diisi.',
            'lokasi.required' => 'lokasi outlet wajib diisi.'
        ]);

        // === Simpan ke database ===
        $data = [
            'kode_outlet' => $request->kode_outlet,
            'brand_id' => $request->brand_id,
            'nama_outlet' => $request->nama_outlet,
            'lokasi' => $request->lokasi,
        ];

        $outlet = Outlet::findOrFail($request->id);
        $outlet->update($data);

        return redirect()->route('outlet')->with('message', 'Data outlet berhasil diubah!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $outlet = Outlet::findOrFail($id);

        $outlet->delete();
        return redirect()->route('outlet')->with('message', 'Data berhasil di hapus!');
    }
}
