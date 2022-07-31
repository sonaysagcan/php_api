<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Api\Subscription;
use App\Http\Controllers\Api\RegisterController as Register;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $subscription = $this->check_subscription($request->client_token);
        if (! $subscription){
            return response()->json([
                'status' => 500,
                'message' => 'SUBSCRIPTION NOT FOUND',
            ]);
        }
        $state = [1=>'started', 2=>'renewed', 3=>'canceled'];
        return response()->json([
            'status' => 200,
            'message' => 'OK',
            'subscription_state' => $state[$subscription->state_id],
            'subscription_start_at' => $subscription->subscription_start_at,
            'subscription_end_at' => $subscription->subscription_end_at,
        ]);
    }

    public function check_subscription($client_token)
    {   
        $device_id = Register::check_token($client_token);
        $subscription =Subscription::where('device_id',$device_id)->first(['state_id','subscription_start_at','subscription_end_at']);
        if (! $subscription){
            return false;
        }
        return $subscription;

    }    

    public function deactive_subscription($data){
        $now = Carbon::now()->timezone('America/Chicago')->toDateTimeString();
        $subscription =Subscription::where('id',$data->subscription_id)->first(['id']);
        if ($subscription){
            $subscription->update(['state_id'=>3,'subscription_end_at'=>$now]);  // state = canceled
            return true;
        }
        return false;
    }

    public function save_subscription($data){
        $subscription =Subscription::where('device_id',$data['device_id'])->first(['id']);
        if ($subscription){
            $subscription->update(['subscription_start_at'=>$data['subscription_start_at'],
                                  'subscription_end_at'=>$data['subscription_end_at'],
                                  'state_id'=>2]);  // state = renewed
        }
        else{
            $create_subscription = Subscription::create(['device_id'=>$data['device_id'],'subscription_start_at'=>$data['subscription_start_at'],
                                'subscription_end_at'=>$data['subscription_end_at'],'state_id'=>1]);  //  state = started
        }
        return true;
    }
   
}
