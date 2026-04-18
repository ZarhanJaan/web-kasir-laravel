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
        $qris_image = $settings['qris_image'] ?? null;
        $qris_name = $settings['qris_name'] ?? null;

        return view('setting', compact('store_name', 'qris_image', 'qris_name'));
    }

    public function updateQris(Request $request)
    {
        $request->validate([
            'qris_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $settings = [];
        if (File::exists($this->settingsPath)) {
            $settings = json_decode(File::get($this->settingsPath), true);
        }

        if ($request->hasFile('qris_image')) {
            // Delete old QRIS if exists to save space (Optional but good practice)
            if (isset($settings['qris_image']) && File::exists(public_path($settings['qris_image']))) {
                File::delete(public_path($settings['qris_image']));
            }

            $file = $request->file('qris_image');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $filename = time() . '_qris.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/qris'), $filename);

            $settings['qris_image'] = 'images/qris/' . $filename;
            $settings['qris_name'] = $originalName;
            File::put($this->settingsPath, json_encode($settings));
        }

        return redirect()->back()->with('success', 'Gambar QRIS berhasil diperbarui!');
    }
}
