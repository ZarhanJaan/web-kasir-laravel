<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\Qris;

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
        
        $qris = Qris::first();
        $qris_image = $qris ? $qris->image_path : null;
        $qris_name = $qris ? $qris->name : null;

        return view('setting', compact('store_name', 'qris_image', 'qris_name'));
    }

    public function updateQris(Request $request)
    {
        $request->validate([
            'qris_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('qris_image')) {
            $qris = Qris::first();

            // Delete old QRIS if exists to save space (Optional but good practice)
            if ($qris && File::exists(public_path($qris->image_path))) {
                File::delete(public_path($qris->image_path));
            }

            $file = $request->file('qris_image');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $filename = time() . '_qris.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/qris'), $filename);

            if (!$qris) {
                $qris = new Qris();
            }
            $qris->image_path = 'images/qris/' . $filename;
            $qris->name = $originalName;
            $qris->save();
        }

        return redirect()->back()->with('success', 'Gambar QRIS berhasil diperbarui!');
    }

    public function updateStoreName(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
        ]);

        $settings = [];
        if (File::exists($this->settingsPath)) {
            $settings = json_decode(File::get($this->settingsPath), true);
        }

        $settings['store_name'] = $request->input('store_name');
        File::put($this->settingsPath, json_encode($settings));

        return redirect()->back()->with('success', 'Nama toko berhasil diperbarui!');
    }
}
