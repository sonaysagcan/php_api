<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Api\Device;
use App\Models\Api\Purchase;
use App\Models\Api\Subscription;
use App\Models\Api\Application;

use Carbon\Carbon;

class DummiesController extends Controller
{

    public function create_dummy_device(){

        for ($i=0;$i<1000000;$i++){
            $device_uid = $this->create_dummies(9,true);
            $app_id = rand(1,100);
            $token = $this->create_dummies(50,false);
            $lang = 'EN_tr';
            $os = rand(1,2);
            Device::create(['device_uid'=>$device_uid,'app_id'=>$app_id,'client_token'=>$token,'language'=>$lang,'operating_system_id'=>$os]);
        }
    }

    public function create_dummy_app(){

        for ($i=0;$i<100;$i++){
            $data = [
            'app_name' => $this->create_dummies(7,false),
            'operating_system_id' => rand(1,2),
            'credential' => $this->create_dummies(10,false) . ':' . $this->create_dummies(10,false),
            ];
            Application::create($data);
        }
    }

    public function receipt(){
        $receipt = (int)$this->create_dummies(10,true);
        return $receipt;
    }

    public function create_dummy_purchase(){

        for ($i=1;$i<800000;$i++){
            $device_id = $i;
            $smonth=rand(1,8);
            $emonth=rand(5,10);
            $sday = rand(1,30);
            $shour = rand(0,23);
            $smin= rand(0,59);
            $ssecond = rand(0,59);
            $start = Carbon::create(2022, $smonth, $sday, $shour, $smin, $ssecond);
            $end = Carbon::create(2022, $emonth, $sday, $shour, $smin, $ssecond);

            Purchase::create(['device_id'=>$device_id,'subscription_start_at'=>$start,'subscription_end_at'=>$end]);
        }
    }

    public function create_dummy_subscription(){

        for ($i=589684;$i<800000;$i++){
            $device_id = $i;

            $smonth=rand(1,8);
            $emonth=rand(5,10);

            $sday = rand(1,30);
            $shour = rand(0,23);
            $smin= rand(0,59);
            $ssecond = rand(0,59);
            $start = Carbon::create(2022, $smonth, $sday, $shour, $smin, $ssecond);
            $end = Carbon::create(2022, $emonth, $sday, $shour, $smin, $ssecond);
            $state_id = rand(1,3);

            Subscription::create(['device_id'=>$device_id,'state_id'=>$state_id,'subscription_start_at'=>$start,'subscription_end_at'=>$end]);
        }
    }

    public function create_dummies_os()
    {   
        $index = rand(0,1);
        if ($index == 0){
            $os = 'Android';
        }else{
            $os = 'IOS';
        }
        return $os;
    }

    public function create_dummies($n,$is_numeric)
    {
        if ($is_numeric){
            $characters = '0123456789';
        }else{
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        $randomString = '';
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
        return $randomString;
    }
}
