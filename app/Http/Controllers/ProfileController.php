<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller {
    
    public function getUserProfileData(Request $request) {
        $user = Auth::user();
        if ($user) {
            return response()->json([
                "success" => true,
                "data" => $user
            ]);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Felhasználói adatok nem találhatók"
            ], 404);
        }
    }

    public function setUserProfileData(Request $request) {
        $messages = [
            'city_id.required' => 'A város mező kitöltése kötelező.',
            'city_id.integer' => 'A város mezőnek egész számnak kell lennie.',
        ];
    
        $validator = validator($request->all(), [
            'city_id' => 'required|integer',
        ], $messages);
    
        if ($validator->fails()) {
            $this->failedValidation($validator);
        }
    
        $user = Auth::user();
    
        $user->city_id = $request->city_id;
        $user->save();
    
        return response()->json([
            "message" => "Sikeres város frissítés",
            "success" => true
        ], 200);
    }

    public function setNewPassword(Request $request) {
        $messages = [
            "password.required" => "Jelszó elvárt",
            "password.min" => "Túl rövid a jelszó",
            "password.letters"=>"legyenek betűk",
            "password.mixed"=>"mixed case",
            "password.symbols"=>"Különleges karakter kell",
        ];
    
        $validator = validator($request->all(), [
            'password' => ["required",Password::min(6)->letters()->mixedCase()->numbers()->symbols()]
        ], $messages);
    
        if ($validator->fails()) {
            $this->failedValidation($validator);
        }
    
        $user = Auth::user();
    
        $user->password = $request->password;
        $user->save();
    
        return response()->json([
            "message" => "Sikeres jelszó frissítés",
            "success" => true
        ], 200);
    }

    public function deleteAccount() {
        $user = Auth::user();
        
        if ($user) {
            $user->delete();
        
            return response()->json([
               "success" => true,
                "message" => "Felhasználói fiók sikeresen törölve"
            ]);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Felhasználói fiók nem található"
            ], 404);
        }
    }
}
