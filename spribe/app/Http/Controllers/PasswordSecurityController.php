<?php

namespace App\Http\Controllers;

use App\PasswordSecurity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordSecurityController extends Controller
{
    public function show2faForm()
    {
        $user = Auth::user();

        $google2fa_url = "";
        if ($user->passwordSecurity()->exists()) {
            $google2fa = app('pragmarx.google2fa');
            $google2fa_url = $google2fa->getQRCodeGoogleUrl('spribe', $user->email, $user->passwordSecurity->google2fa_secret);
        }
        $data = [
            'user' => $user,
            'google2fa_url' => $google2fa_url
        ];
        return view('auth.2fa')->with('data', $data);
    }

    public function enable2fa(Request $request)
    {
        $user = Auth::user();

        $google2fa = app('pragmarx.google2fa');


        $secret = $request->input('verify-code');
        $valid = $google2fa->verifyKey($user->passwordSecurity->google2fa_secret, $secret);

        if($valid){
            $user->passwordSecurity->google2fa_enable = true;
            $user->passwordSecurity->save();
            return redirect('2fa')->with('success',"2FA is Enabled Successfully.");
        }else{
            return redirect('2fa')->with('error',"Invalid Verification Code, Please try again.");
        }
    }

    public function disable2fa(Request $request)
    {
        if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
            return redirect()->back()->with("error","Your  password does not matches with your account password. Please try again.");
        }

        $validatedData = $request->validate([
            'current-password' => 'required',
        ]);
        $user = Auth::user();
        $user->passwordSecurity->google2fa_enable = false;
        $user->passwordSecurity->save();
        return redirect('/2fa')->with('success',"2FA is now Disabled.");
    }

    public function generate2faSecret()
    {
        $user = Auth::user();
        $google2fa = app('pragmarx.google2fa');

        $passwordSecurity = new PasswordSecurity();
        $passwordSecurity['user_id'] = $user->id;
        $passwordSecurity['google2fa_enable'] = 0;
        $passwordSecurity['google2fa_secret'] = $google2fa->generateSecretKey();
        $passwordSecurity->save();

        return redirect('/2fa')->with('success','Secret key was generated. Please verify code to Enable 2FA');
    }
}
