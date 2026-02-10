<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Redirect users after login based on role (Spatie)
     */
    public function authenticated()
    {
        $user = Auth::user();

        // cek status aktif
        if ($user->status != 1) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', "This user is inactive and unable to log in.");
        }

        // ambil role pertama (Spatie)
        $role = $user->roles->first()?->name;

        // redirect berdasarkan role
        switch ($role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'teacher':
            case 'user':
                return redirect()->route('permission.index');
            default:
                return redirect('/'); // fallback
        }
    }

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Logout user (override supaya bisa pakai POST)
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}