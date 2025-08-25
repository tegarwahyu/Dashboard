<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Validated;
use App\Models\Kompetitor_outlet;
use App\Models\Kompetitor_visit;
use Yajra\DataTables\Facades\DataTables;


class KompetitorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('kompetitor.index');
    }
    
    public function getDataKompetitor(){
        $users = Kompetitor_outlet::get();
        // dd($users);
        // die();
        return Datatables::of($users)
        ->addIndexColumn()
        ->addColumn('aksi', function ($data) {
            return '
                <button type="button" onclick="editData(`'. url('user/showUser', $data->id) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fas fa-edit"></i></button>
                <button type="button" onclick="deleteData(`'. url('user/deleteUser', $data->id) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
            ';
        })
        ->rawColumns(['aksi'])
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
        // dd(auth()->user()->id);
        // die();
         // Validasi sederhana
        $validated = $request->validate([
            'nama_outlet' => 'required|string',
            'lokasi' => 'required|string',
            'kapasitas_outlet' => 'required|integer',
            'waktu_visit' => 'required|date',
            'estimasi_pengunjung' => 'required|integer',
        ]);

        $kom_outlt = Kompetitor_outlet::create([
            'nama_outlet' => $validated['nama_outlet'],
            'lokasi' => $validated['lokasi'],
            'kapasitas_outlet' => $validated['kapasitas_outlet'],
            'user_id ' => auth()->user()->id
        ]);

        $kom_vist = Kompetitor_visit::create([
            'competitor_outlet_id' => $kom_outlt->id,
            'waktu_visit' => $validated['waktu_visit'],
            'estimasi_pengunjung' => $validated['estimasi_pengunjung']
        ]);
        // dd($request->waktu_visit);
        // die();

        return response()->json(['message' => 'User berhasil dibuat']);
        
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
        //
    }
}
