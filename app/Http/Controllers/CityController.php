<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;

class CityController extends Controller {
    public function addCity(Request $request){
        $validateData= $request->validate([
            "city"=> "required|string|max:255"
        ]);
        $city= new City();
        $city->city=$validateData["city"];
        $city->save();

        return response()->json(["message"=>"Város sikeresen hozzáadva","Város:"=>$city],201);
    }
}
