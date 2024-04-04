<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;


class AuthController extends Controller {
    
    public function register(Request $request) {
        $messages = [
            'name.required' => 'A név mező kitöltése kötelező.',
            'username.required' => 'A felhasználónév mező kitöltése kötelező.',
            'username.unique' => 'A felhasználónév már foglalt.',
            'email.required' => 'Az email mező kitöltése kötelező.',
            'email.email' => 'A megadott email cím formátuma érvénytelen.',
            'email.unique' => 'Ez az email cím már foglalt.',
            "password.required" => "Jelszó elvárt",
            "password.min" => "Túl rövid a jelszó",
            "password.letters"=>"legyenek betűk",
            "password.mixed"=>"mixed case",
            "password.symbols"=>"Különleges karakter kell",
            "password_confirmation.required" => "Nem egyező jelszó",
            "confirm_password.required"=>"Hiányzó jelszó megerősítés",
            'borndate.required' => 'A születési dátum mező kitöltése kötelező.',
            'borndate.date' => 'A születési dátum formátuma érvénytelen.',
            'city_id.required' => 'A város mező kitöltése kötelező.',
            'city_id.integer' => 'A város mezőnek egész számnak kell lennie.',
        ];
    
        $validator = validator($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            "password"=>["required",Password::min(6)->letters()->mixedCase()->numbers()->symbols(),"confirmed"],
            "password_confirmation"=>["required"],
            'borndate' => 'required|date',
            'city_id' => 'required|integer',
        ], $messages);
    
        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                "success" => false,
                "message" => "Adatbeviteli hiba",
                "data" => $validator->errors()
            ], 422));
        }
    
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
    
        $user = User::create($input);
    
        $success['name'] = $user->name;
    
        return response()->json([
            "message" => "Sikeres regisztráció",
            "success" => $success
        ], 201);
    
    }

    public function login( Request $request ) {

        $messages = [
            'username.required' => 'A felhasználónév mező kitöltése kötelező.',
            'password.required' => 'A jelszó mező kitöltése kötelező.',
        ];
    
        $validator = validator($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ], $messages);
    
        if ($validator->fails()) {
            $this->failedValidation($validator);
        }
    
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            $authUser = Auth::user();
            $success['username'] = $authUser->username;
            $success['token'] = $authUser->createToken($authUser->username . "token")->plainTextToken;
    
            return response()->json([
                "message" => "Sikeres azonosítás",
                "success" => $success
            ]);
        }
    
        return response()->json([
            "message" => "Sikertelen azonosítás",
            "success" => false
        ], 401);
    }
    
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            "success" => false,
            "message" => "Adatbeviteli hiba",
            "data" => $validator->errors()
        ], 422));
    }    
    

    public function logout( Request $request ) {

        auth( "sanctum" )->user()->currentAccessToken()->delete();

        return response()->json([ "message" => "Sikeres kijelentkezés" ]);
    }
}
