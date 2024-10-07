<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Service\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;


class LoginController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {

        $this->authService = $authService;
    }
    /**
     * Load the login view
     * @return View
     */
    public function index(){
        if($this->authService->CheckIfLoggedIn()) return redirect()->route('dashboard');
        return view('sections.auth.login');
    }

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return redirect
     */
    public function _authenticate(Request $request){
        if ($this->authService->Authenticate($request->only('email', 'password'),$request->remember)) {
            return redirect()->route('dashboard');
        }else{
            return view('sections.auth.login',["error"=>"Invalid credentials, please check again !"]);
        }
    }

    /**
     * Handle logout attempt.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function _logout(Request $request){

        $this->authService->logout();

        // Clear the remember me cookie
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
