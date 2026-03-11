<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        return view('frontend.profile.index', compact('user', 'teacher'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'nip'      => ['nullable', 'string', Rule::unique('teachers')->ignore($teacher->id)],
            'nuptk'    => ['nullable', 'string', Rule::unique('teachers')->ignore($teacher->id)],
            'password' => 'nullable|min:6|confirmed',
        ]);

        // Update User
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        // Update Teacher
        $teacher->update([
            'name'  => $request->name,
            'nip'   => $request->nip,
            'nuptk' => $request->nuptk,
        ]);

        return redirect()->back()->with('success', 'Profil berhasil diperbarui!');
    }
}
