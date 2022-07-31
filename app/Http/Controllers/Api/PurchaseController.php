<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\RegisterController as Register;
use App\Http\Controllers\Api\SubscriptionController as Subscription;
use Illuminate\Http\Request;
use App\Models\Api\Device;
use App\Models\Api\Purchase;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;


class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        if ($request->client_token && $request->receipt){

            $device_data = Register::get_device_os($request->client_token);
            if (! $device_data){
                return response()->json([
                    'status' => 500,
                    'message' => 'REGISTERED DEVICE NOT FOUND',
                ]);
            }

            $purchase_request = $this->purchase_request($request->receipt, $device_data->operating_system_id);
            // response {"status":true,"expire-date":"2022-08-27 03:45:01"}
            if (! $purchase_request['status']){
                return response()->json($purchase_request);
            }

            $save_purchase = $this->save_purchase($device_data->id,$purchase_request);
            if (! $save_purchase){
                return response()->json([
                    'status' => 500,
                    'message' => 'PURCHASE SAVE FAILED',
                ]);
            }

            $save_subscription = Subscription::save_subscription($save_purchase);
            if (! $save_subscription){
                return response()->json([
                    'status' => 500,
                    'message' => 'SUBSCRIPTION SAVE FAILED',
                ]);
            }
        }            
        return response()->json([
            'status' => 200,
            'message' => 'OK',
        ]);
    }

    public function purchase_request($receipt,$device_os){

        try{
            if($device_os==1){
                return $this->google_purchase_api($receipt);
            }
            elseif($device_os==2){
                return $this->apple_purchase_api($receipt);
            }
            else{
                $body = ['status' => false, 'message'=> 'UNSUPPORTED OPERATING SYSTEM'];
                return $body;
            }
        } 
        catch(Exception $e){
            $body = ['status' => false, 'message'=> $e->getMessage()];
            return $body;
        }

    }

    public function google_purchase_api($receipt)
    {
        $body = ['status' => false,];
        
        if (substr($receipt,-1)%2==0){
            $subscription_start_date = Carbon::now()->timezone('America/Chicago')->toDateTimeString();
            $expire_date = Carbon::now()->timezone('America/Chicago')->addDays(30)->toDateTimeString();
            $body = ['status' => true, 'subscription_start_date'=>$subscription_start_date,'expire-date' => $expire_date];
        }
        Http::fake([
            'google.com/purchase' => Http::response($body, 200),
        ]);
        return $response = Http::post('google.com/purchase');

    }

    public function apple_purchase_api($receipt)
    {
        $body = ['status' => false,];
        
        if (substr($receipt,-1)%2==0){
            $subscription_start_date = Carbon::now()->timezone('America/Chicago')->toDateTimeString();
            $expire_date = Carbon::now()->timezone('America/Chicago')->addDays(30)->toDateTimeString();
            $body = ['status' => true, 'subscription_start_date'=>$subscription_start_date,'expire-date' => $expire_date];
        }
        Http::fake([
            'apple.com/purchase' => Http::response($body, 200),
        ]);
        return $response = Http::post('apple.com/purchase');

    }

    public function save_purchase($device_id,$response)
    {
        $data = ['device_id'=>$device_id,'subscription_start_at'=>$response['subscription_start_date'],'subscription_end_at'=>$response['expire-date']];
        $purchase = Purchase::create($data);
        if (! $purchase){
            return false;
        }
        return $data;
    }    

}

