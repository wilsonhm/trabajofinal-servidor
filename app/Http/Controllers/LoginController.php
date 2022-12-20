<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\Categoria;

use App\Models\User;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response([
                "message" => "El usuario no existe"
            ], 422);
        }

        $accessToken = Auth::user()->createToken('authTestToken')->accessToken;

        return response([
            "user" => Auth::user(),
            "access_token" => $accessToken
        ]);
    }

    public function createUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string',
            'password_confirm' => 'required|same:password'
        ]);
        if (User::where('email', $request->email)->first()) {
            return response([
                'message' => 'El email ya existe'
            ], 404);
        } else {
            $name_folder = Str::random(128);

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'user_folder' => $name_folder
            ])->sendEmailVerificationNotification();


            $user = User::where('email', $request->email)->firstOrFail();

            Categoria::create([
                'name' => 'Archivo',
                'user_id' => $user->id
            ]);

            Storage::disk('local')->put($name_folder . '/prueba.txt', 'Contents');
            return response()->json([
                'message' => 'El usuario se creo correctamente'
            ], 201);
        }
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
        ]);

        $email = $request->input('email');

        if (User::where('email', $email)->doesntExist()) {
            return response([
                'message' => 'El usuario no existe'
            ], 404);
        }

        $token = Str::random(10);

        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token
        ]);

        //Enviamos el email
        Mail::send('Emails.forgot', ['token' => $token], function ($message) use ($email) {
            $message->to($email)->subject('Reinicia tu contraseña');
        });
        return response([
            'message' => 'Revisa tu email'
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required',
            'password_confirm' => 'required|same:password'
        ]);
        $token = $request->input('token');

        if (!$passwordResets = DB::table('password_resets')->where('token', $token)->first()) {
            return response([
                'message' => 'Token invalido'
            ], 404);
        }

        if (!$user = user::where('email', $passwordResets->email)->first()) {
            return response([
                'message' => 'El usuario no existe'
            ], 404);
        }

        $user->password = bcrypt($request->input('password'));
        $user->save();

        return response([
            'message' => 'Success'
        ]);
    }


    public function logout(Request $request)
    {
        $request->validate([
            'token' => 'required'
        ]);

        $user = auth('api')->user();
        $user->token()->revoke();

        return response()->json([
            'message' => 'Ha cerrado la sesion'
        ]);
    }
}
