<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::with('role')->get();
        $roles = Role::all();
        
        return view('manajemen_user', compact('users', 'roles'));
    }

    /**
     * Update the specified user's role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::findOrFail($request->user_id);

        if (auth()->user()->role->role == 'admin' && $user->role && $user->role->role == 'owner') {
            return redirect()->back()->with('error', 'Anda tidak dapat mengubah role owner.');
        }

        $user->role_id = $request->role_id;
        $user->save();

        return redirect()->back()->with('success', 'Role user berhasil diperbarui.');
    }

    public function updateStatus(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        $user = User::with('role')->findOrFail($request->user_id);

        if (auth()->id() == $user->id) {
            return redirect()->back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }
        if (auth()->user()->role->role == 'admin' && $user->role && $user->role->role == 'owner') {
            return redirect()->back()->with('error', 'Anda tidak dapat mengubah status owner.');
        }

        $user->status = !$user->status;
        $user->save();

        return redirect()->back()->with('success', 'Status user ' . ($user->status ? 'diaktifkan.' : 'dinonaktifkan.'));
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (auth()->user()->id == $id) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user = User::findOrFail($id);

        if (auth()->user()->role->role == 'admin' && $user->role && $user->role->role == 'owner') {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus owner.');
        }

        $user->delete();

        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }
}
