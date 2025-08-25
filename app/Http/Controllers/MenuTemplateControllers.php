<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Master_menu_template;
use App\Imports\MenuTemplateImport;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;

class MenuTemplateControllers extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('menu_template.index');
    }

    public function importMenuTemplate(Request $request){
        // dd($request);
        // die();
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ], [
            'file.required' => 'file excel wajib diupload'
        ]);

        Excel::import(new MenuTemplateImport, $request->file('file'));

        // $store = Excel::import(
        //     new MenuTemplateImport(),
        //     $request->file('file')
        // );

        return response()->json([
            'success' => true,
            'code' => 200,
            'Message' => 'Data berhasil di diimport',
        ]);
        // return back()->with('success', 'Data berhasil diimport!');
    }

    public function getData(){
        $menu = Master_menu_template::orderBy('created_at', 'desc');
                    // ->when(auth()->user()->role !== 'Super Admin', function ($query) {
                    //     return $query->whereNotIn('role', ['super admin', 'Super Admin']);
                    // })
                    // ->orderBy('created_at', 'asc') // ASC supaya yang terbaru di bawah
                    // ->get();
        // dd($users);
        // die();
        return Datatables::of($menu)
        ->addIndexColumn()
        ->addColumn('aksi', function ($data) {
            return '
                <button type="button" onclick="editForm(`/menu-template/'.$data->id.'/edit`)" class="btn btn-xs btn-primary btn-flat"><i class="fas fa-edit"></i></button>
                <button type="button" onclick="deleteData(`/menu-template/deleteMenuTemplate/'.$data->id.'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
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
        $aktual = Master_menu_template::findOrFail($id);
        return response()->json($aktual);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = Master_menu_template::findOrFail($id);
        $data->update([
            'menu_template_name' => $request->menu_template_name,
            'menu_category'     => $request->menu_category,
            'menu_category_detail'    => $request->menu_category_detail,
            'menu_name'  => $request->menu_name,
            'menu_short_name'   => $request->menu_short_name,
            'menu_code'    => $request->menu_code,
            'price'  => $request->price,
            'status'  => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'code' => 200,
            'Message' => 'Data berhasil di update',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Master_menu_template::findOrFail($id);
        $data->delete();

        return response()->json(['message' => 'Data berhasil dihapus']);
    }
}
