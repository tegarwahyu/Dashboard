<?php

namespace App\Http\Controllers;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\models\Brand;


class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brand_data = Brand::get()->toArray();

        return view('brand.index', ['brand_data' => $brand_data]);
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
            'nama_brand' => 'required|string|unique:brand,nama_brand',
            'logo_brand' => 'required|image|mimes:jpeg,png,jpg||max:1548',
        ], [
            'nama_brand.unique' => 'Nama brand sudah ada.',
            'nama_brand.required' => 'Nama brand wajib diisi.',
            'logo_brand.required' => 'Logo brand wajib diunggah.',
            'logo_brand.image' => 'File logo harus berupa gambar.',
            'logo_brand.mimes' => 'Logo hanya boleh berformat jpeg, png, atau jpg.',
            'logo_brand.max' => 'Ukuran logo maksimal 1.5MB.'
        ]);

        // === Generate nama file unik ===
        $judulSlug = Str::slug($request->nama_brand, '_');
        $timestamp = time();
        $fileName = "{$judulSlug}_{$timestamp}.jpg"; // paksa ke jpg

        // === Path folder ===
        $destinationPath = public_path('logo_brand');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        // === Resize & compress dengan ImageManager ===
        $manager = new ImageManager(new Driver());

        // Baca file upload
        $img = $manager->read($request->file('logo_brand')->getPathname());

        // Resize max width 800px x height 800px
        $img = $img->resize(800, 800, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // Kompres iteratif biar < 1MB
        $quality = 85; // start dari 85
        $encoded = (string) $img->toJpeg($quality);
        $size = strlen($encoded);

        while ($size > 1024 * 1024 && $quality > 30) {
            $quality -= 5;
            $encoded = (string) $img->toJpeg($quality);
            $size = strlen($encoded);
        }

        // Simpan ke folder tujuan
        file_put_contents($destinationPath . '/' . $fileName, $encoded);

        // === Simpan ke database ===
        Brand::create([
            'nama_brand' => $request->nama_brand,
            'logo_path' => 'logo_brand/' . $fileName,
            'status' => 'aktif',
        ]);

        // return view('brand.index')->with('message', 'Berhasil disimpan');
        return redirect()->route('brand')->with('message', 'Berhasil disimpan!');
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
    public function update(Request $request)
    {
        $request->validate([
            'nama_brand' => 'required|string',
            'logo_brand' => 'required|image|mimes:jpeg,png,jpg||max:1548',
        ], [
            // 'nama_brand.unique' => 'Nama brand sudah ada.',
            'nama_brand.required' => 'Nama brand wajib diisi.',
            'logo_brand.required' => 'Logo brand wajib diunggah.',
            'logo_brand.image' => 'File logo harus berupa gambar.',
            'logo_brand.mimes' => 'Logo hanya boleh berformat jpeg, png, atau jpg.',
            'logo_brand.max' => 'Ukuran logo maksimal 1.5MB.'
        ]);

        // === Generate nama file unik ===
        $judulSlug = Str::slug($request->nama_brand, '_');
        $timestamp = time();
        $fileName = "{$judulSlug}.jpg"; // paksa ke jpg

        // === Path folder ===
        $destinationPath = public_path('logo_brand');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        // === Resize & compress dengan ImageManager ===
        $manager = new ImageManager(new Driver());

        // Baca file upload
        $img = $manager->read($request->file('logo_brand')->getPathname());

        // Resize max width 800px x height 800px
        $img = $img->resize(800, 800, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // Kompres iteratif biar < 1MB
        $quality = 85; // start dari 85
        $encoded = (string) $img->toJpeg($quality);
        $size = strlen($encoded);

        while ($size > 1024 * 1024 && $quality > 30) {
            $quality -= 5;
            $encoded = (string) $img->toJpeg($quality);
            $size = strlen($encoded);
        }

        // Simpan ke folder tujuan
        file_put_contents($destinationPath . '/' . $fileName, $encoded);

        $brand = Brand::findOrFail($request->id);

        // Siapkan data untuk update
        $data = [
            'nama_brand' => $request->nama_brand,
            'logo_path' => 'logo_brand/' . $fileName,
        ];

        $brand->update($data);

        // return view('event.index')->with('message', 'Data berhasil diperbarui');
        return redirect()->route('brand')->with('message', 'Berhasil disimpan!');

    }

    public function deactivate(string $id)
    {
        $brand = Brand::findOrFail($id);

        // Toggle status
        $brand->status = $brand->status === 'aktif' ? 'non aktif' : 'aktif';
        $brand->save();

        return redirect()->route('brand')->with('message', 'Data berhasil di nonaktifkan!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $brand = Brand::findOrFail($id);

        // Hapus gambar jika ada
        if ($brand->img_path && file_exists(public_path($brand->img_path))) {
            unlink(public_path($brand->img_path));
        }

        $brand->delete();
        return redirect()->route('brand')->with('message', 'Data berhasil di hapus!');
    }
}
