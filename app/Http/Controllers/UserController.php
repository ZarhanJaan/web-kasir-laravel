<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data = [
            'users' => User::all(),
        ];
        return view('t_user', $data);
    }

    public function add()
    {
        return view('t_adduser');
    }

    public function insert(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ], [
            'name.required' => 'Silakan isi Nama Pengguna.',
            'email.required' => 'Silakan isi Alamat Email.',
            'email.unique' => 'Alamat Email sudah terdaftar.',
            'password.required' => 'Silakan isi Password.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('user')->with('pesan_sukses', 'User Berhasil di Tambahkan');
    }

    public function edit($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            abort(404);
        }

        $data = [
            'user' => $user,
        ];
        return view('t_edituser', $data);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
        ], [
            'name.required' => 'Silakan isi Nama Pengguna.',
            'email.required' => 'Silakan isi Alamat Email.',
            'email.unique' => 'Alamat Email sudah terdaftar.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('user')->with('pesan_sukses', 'User Berhasil di Update');
    }

    public function delete($id)
    {
        $user = User::find($id);
        
        if ($user) {
            $user->delete();
            return redirect()->route('user')->with('pesan_hapus', 'User Berhasil di Delete');
        }

        return redirect()->route('user')->with('pesan_hapus', 'User Tidak Ditemukan');
    }
}
