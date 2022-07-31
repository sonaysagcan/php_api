<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Api\Application;

class ApplicationController extends Controller
{
    public function all_app(){
        $result = [];
        $app_data = Application::all();

        if (! $app_data){
            return false;
        }
        foreach ($app_data as $app){
            $result[$app->id] = ['operating_system_id'=>$app->operating_system_id,'credential'=>$app->credential];
        }
        return $result;
    }
}
