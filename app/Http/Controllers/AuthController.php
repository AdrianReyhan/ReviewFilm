<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    //

    public function showRegister()
    {
        return view('register');
    }

    public function showLogin()
    {
        if(session()->has('is_loggedin')){
            return redirect()->to(url('/movies'));
        }
        return view('login');
    }

    public function registerProcess(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'name'  => 'required|unique:users',
            'password' => 'required|string|min:6',

        ]);

        if ($validator->fails()) {
            return redirect('/register')
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {
                $user = new User();
                $user->username = $request->username;
                $user->email = $request->email;
                $user->name = $request->name;
                $user->password = Hash::make($request->password);
                $user->save();

                // Optionally, you can log in the user after registration
                $credentials = $request->only('username', 'password');
                $token = JWTAuth::attempt($credentials);
                $update['token'] = $token;
                $user->where('username', $request->username)->update($update);
                session(['is_loggedin' => 1]);
                session(['token_access' => $update['token']]);
                session(['id_user' => $user->id]);

                return redirect('/movies');
            } catch (Exception $e) {
                return redirect('/register')
                    ->withErrors(['Error: Registration failed'])
                    ->withInput($request->all());
            }
        }
    }

    public function loginProcess(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'username' => 'required',
            'password' => 'required|string'
        ]);
        if($validator->fails()){
            return redirect('/')
            ->withErrors($validator)
            ->withInput($request->all());
        }else{
            $user = new User();
            $credentials = $request->only('username', 'password');
            $token = JWTAuth::attempt($credentials);
            $update['token'] = $token;
            try {
                if (!$token = JWTAuth::attempt($credentials)) {
                    return redirect('/')
                    ->withErrors(__("messages.invalid_login"))
                    ->withInput();
                }else{
                    $username = $request->username;
                    $dataUser = $user->where('username', $username)->first();
                    $request->session()->regenerate();
                    $dataUser->where('username', $username)->update($update);
                    session(['is_loggedin' => 1]);
                    session(['token_access' => $update['token']]);
                    session(['id_user' => $dataUser->id]);
                    return redirect('/movies');
                }
            } catch (JWTException $e) {
                return redirect('/')
                ->withErrors(['Error : Tidak bisa create token'])
                ->withInput();
            }
        }
    }

    public function logoutProcess()
    {
        if(session()->has('is_loggedin')){
            $getToken = User::where('id', session('id_user'))->first();
            try {
                JWTAuth::manager()->invalidate(new \Tymon\JWTAuth\Token($getToken->token), $forceForever = false);
            } catch (Exception $e) {
                //null
            }
            User::where('id', session('id_user'))->update([
                'token' => null
            ]);
            session()->flush();
            return redirect('/');
        }else{
            return redirect()->to(url(''));
        }
    }

    public function changeLanguage($langCode)
    {
        App::setLocale($langCode);
        session(['lang_code' => $langCode]);
        return redirect()->back();
    }
}
