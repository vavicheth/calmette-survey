<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginBasic extends Controller
{
  public function index(Request $request)
  {
    if ($request->session()->get('admin_logged_in')) {
      return redirect()->route('admin.dashboard');
    }

    $pageConfigs = ['myLayout' => 'blank'];
    return view('content.authentications.auth-login-basic', ['pageConfigs' => $pageConfigs]);
  }

  public function login(Request $request)
  {
    $request->validate([
      'email' => 'required|email',
      'password' => 'required',
    ]);

    // Authenticate user from database
    $user = User::where('email', $request->email)->first();

    if ($user && Hash::check($request->password, $user->password)) {
      $request->session()->put('admin_logged_in', true);
      $request->session()->put('admin_email', $user->email);
      $request->session()->put('user_id', $user->id);
      $request->session()->put('user_name', $user->name);
      $request->session()->save();

      return redirect()->route('admin.dashboard')->with('success', 'Login successful!');
    }

    return back()->withInput($request->only('email'))->with('error', 'Invalid credentials');
  }

  public function logout(Request $request)
  {
    $request->session()->forget(['admin_logged_in', 'admin_email', 'user_id', 'user_name']);
    $request->session()->save();
    return redirect()->route('auth-login-basic')->with('success', 'Logged out successfully');
  }
}
