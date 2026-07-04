<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KategoriController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $kategori = DB::table('t_kategori')->orderBy('id_kategori', 'desc')->get();
        return view('t_kategori', compact('kategori'));
    }

    public function insert(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required'
        ]);

        try {
            DB::table('t_kategori')->insert([
                'nama_kategori' => $request->nama_kategori,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            return redirect()->back()->with('pesan_sukses', 'Kategori berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('pesan_error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            DB::table('t_kategori')->where('id_kategori', $id)->delete();
            return redirect()->back()->with('pesan_sukses', 'Kategori berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('pesan_error', 'Terjadi kesalahan saat menghapus: ' . $e->getMessage());
        }
    }
}
