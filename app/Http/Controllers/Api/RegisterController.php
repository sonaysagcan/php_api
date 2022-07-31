<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Api\Device;
use App\Http\Controllers\Api\OperatingSystemController as OS;



class RegisterController extends Controller
{

    public function index(Request $request)
    {
        if ($request->device_uid){
            $client_token = $this->get_token($request->device_uid,$request->app_id);

            if (! $client_token){
                $client_token = $this->create_token();
                $data = ['device_uid'=>$request->device_uid,'app_id'=>$request->app_id,'language'=>$request->language,
                        'operating_system_id'=>OS::get_os_id($request->operating_system),'client_token'=>$client_token];
                $device = Device::create($data);
            }
            return response()->json([
                'status' => 200,
                'message' => 'OK',
                'client_token' => $client_token,
            ]);
        }
        return response()->json([
            'status' => 500,
            'message' => 'NOT_OK',
        ]);
    }

    public function check_token($client_token)
    {
        $device_data = Device::where('client_token', $client_token)->first(['id']);

        if (! $device_data){
            return false;
        }
        return $device_data->id;
    }

    public function get_token($device_uid,$app_id)
    {
        $device_data = Device::where([['device_uid', $device_uid],['app_id',$app_id]])->first(['client_token']);

        if (! $device_data){
            return false;
        }
        return $device_data->client_token;
    }

    public function create_token()
    {
        $n = 200;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
        return $randomString;
    }

    public function get_device_os($client_token)
    {
        $device_data = Device::where('client_token', $client_token)->first(['id','operating_system_id']);

        if (! $device_data){
            return false;
        }
        return $device_data;
    }
    
}
