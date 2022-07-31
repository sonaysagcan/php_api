<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;


class IosApiController extends Controller
{
    public function purchase($receipt)
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
    public function check_subscription($receipt)
    {
        if (substr($receipt,-2)%6==0){
            $body = ['status' => 402,];
            $status = 402;
            Http::fake([
                'apple.com/check-subscription' => Http::response($body, $status),
            ]);
            $response_err = Http::post('apple.com/check-subscription');
            return  $response_err;
        }
        $body = ['status' => 200];
        $status = 200;
        Http::fake([
            'apple.com/check-subscriptions' => Http::response($body, $status),
        ]);
        $response_ok = Http::post('apple.com/check-subscriptions');
        return $response_ok;
    }
}
