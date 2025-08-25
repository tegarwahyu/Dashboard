<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch; // Pastikan model sesuai dengan tabel brand
use App\Models\Sub_branch;
use Yajra\DataTables\DataTables;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('branch.index');
    }

    public function sub_index(){
        return view('branch.sub_index');
    }

    public function getSubBranchList(){
        $branchs = Branch::select('id', 'nama_branch')->get();
        return response()->json($branchs);
    }

    public function getSubBranchData(){
        $branches = Sub_branch::with('branch')->get();
        return Datatables::of($branches)
            ->addIndexColumn() // Tambahkan baris index
            ->addColumn('nama_brand', function ($branch) {
                return $branch->brand ? $branch->brand->nama_brand : '-';
            })
            ->make(true);
    }

    public function getBranchData(){
        $branches = Branch::with('brand')->get();
        return Datatables::of($branches)
            ->addIndexColumn() // Tambahkan baris index
            ->addColumn('nama_brand', function ($branch) {
                return $branch->brand ? $branch->brand->nama_brand : '-';
            })
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function getBrandList()
    {
        $brands = \App\Models\Brand::select('id', 'nama_brand')->get();
        return response()->json($brands);
    }

    public function sub_store(Request $request){
        $request->validate([
            'nama_sub_branch' => 'required|string|max:255',
        ]);

        $branch = new \App\Models\Sub_branch();
        $branch->nama_sub_branch = $request->nama_sub_branch;
        $branch->branch_id  = $request->branch_id;
        $branch->save();

        return response()->json(['success' => true]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $request->validate([
            'nama_branch' => 'required|string|max:255',
        ]);

        $branch = new \App\Models\Branch();
        $branch->nama_branch = $request->nama_branch;
        $branch->brand_id = $request->brand_id;
        $branch->save();

        return response()->json(['success' => true]);
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
