<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FirstLoginController extends Controller
{
    /**
     * Create verify.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $title = 'Mise à jour du mot de passe';
        return view('user.first-login',compact('title'));
    }

    /**
     * Update password.
     * @param Request $req
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $req)
    {
        $this->validate($req,[
            'password' => 'required|confirmed|min:6'
        ]);
        $user = auth()->user();
        $user->password = $req->password;
        $user->first_login = false;
        $user->save();
        return redirect()->route('index-dashboard');
    }
}
