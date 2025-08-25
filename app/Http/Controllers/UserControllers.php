<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Outlet;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;

class UserControllers extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('users.index');
    }

    public function apiGetData(){
        $users = User::with('outlet')
                    ->when(auth()->user()->role !== 'Super Admin', function ($query) {
                        return $query->whereNotIn('role', ['super admin', 'Super Admin']);
                    })
                    ->orderBy('created_at', 'asc') // ASC supaya yang terbaru di bawah
                    ->get();
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
        
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|string',
            'password' => 'required|string|min:6',
            'lokasi' => 'required'
        ]);
    // dd($validated['lokasi']);
    // die();
        User::create([
            'name' => $validated['nama'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'outlet_id' => $validated['lokasi'],
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json(['message' => 'User berhasil dibuat']);
    }

    public function getOutlets()
    {
        $outlets = Outlet::all();
        return response()->json($outlets);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function getOutletsEdit()
    {
        return Outlet::select('id', 'nama_outlet')->get();
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
        // 1) Validasi input
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email',
            'role' => 'required|string',
            // 'password' => 'nullable|string|min:6', // opsional
        ]);

        // 2) Ambil user
        $user = User::findOrFail($id);

        // 3) Update data user
        $user->name = $request->nama;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->outlet_id = $request->lokasi;

        $user->save();

        // 5) Redirect atau response JSON
        return response()->json(['message' => 'Data berhasil update']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = User::findOrFail($id);
        $data->delete();

        return response()->json(['message' => 'Data berhasil dihapus']);
    }
}
