<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SettingController extends Controller
{
    private $settingsPath;

    public function __construct()
    {
        $this->settingsPath = storage_path('app/settings.json');
    }

    public function index()
    {
        $settings = [];
        if (File::exists($this->settingsPath)) {
            $settings = json_decode(File::get($this->settingsPath), true);
        }

        $store_name = $settings['store_name'] ?? 'Toko Sembako';
        $store_address = $settings['store_address'] ?? 'Jl. Contoh Alamat No.123';

        return view('setting', compact('store_name', 'store_address'));
    }

    public function updateStoreName(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'store_address' => 'required|string|max:500',
        ]);

        $settings = [];
        if (File::exists($this->settingsPath)) {
            $settings = json_decode(File::get($this->settingsPath), true);
        }

        $settings['store_name'] = $request->input('store_name');
        $settings['store_address'] = $request->input('store_address');
        File::put($this->settingsPath, json_encode($settings));

        return redirect()->back()->with('success', 'Informasi toko berhasil diperbarui!');
    }
}
