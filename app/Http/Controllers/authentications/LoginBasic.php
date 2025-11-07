<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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

    // Simple authentication - in production, use proper user authentication
    if ($request->email === 'admin@survey.com' && $request->password === 'admin123') {
      $request->session()->put('admin_logged_in', true);
      $request->session()->put('admin_email', $request->email);
      $request->session()->save();

      return redirect()->route('admin.dashboard')->with('success', 'Login successful!');
    }

    return back()->withInput($request->only('email'))->with('error', 'Invalid credentials');
  }

  public function logout(Request $request)
  {
    $request->session()->forget(['admin_logged_in', 'admin_email']);
    $request->session()->save();
    return redirect()->route('auth-login-basic')->with('success', 'Logged out successfully');
  }
}
