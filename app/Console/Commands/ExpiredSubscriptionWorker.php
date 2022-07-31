<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use App\Models\Api\Subscription;
use App\Models\Api\Device;
use App\Models\Api\Application;
use Carbon\Carbon;
use App\Http\Controllers\Api\GoogleApiController as GoogleApi;
use App\Http\Controllers\Api\IOSApiController as IosApi;
use App\Http\Controllers\Api\ApplicationController as App;
use App\Http\Controllers\Api\DummiesController as Dummy;
use App\Http\Controllers\Api\SubscriptionController;
use DB;

class ExpiredSubscriptionWorker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:subscription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifies expired subscriptions.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
  
    private $failed_jobs = 0;
    private $expired_subscription = 0;
    private $canceled_subscription = 0;
    private $part = 0;

    public function __construct()
    {
        parent::__construct();
    }
    
    public function deactive_subscription_in_db($subscription){
        $result = SubscriptionController::deactive_subscription($subscription);
        if ($result){
            return true;
        }
        return false;
    }

    public function check_subscription_on_api($receipt,$operating_system_id){
        if ($operating_system_id == 1){
            $response = GoogleApi::check_subscription($receipt);
            if ($response['status']==200){
                return 0;
            }
            else{
                return 1;
            }
        }
        else{
            $response = IosApi::check_subscription($receipt);
            if ($response['status']==200){
                return 0;
            }
            else{
                return 1;
            }
        }
    }

    public function query_results_render($subscriptions){
        
        foreach($subscriptions as $s){
            $this->expired_subscription++;
            $dummy = new Dummy;
            $receipt = rand(10,200);
            $response = $this->check_subscription_on_api($receipt,$s->operating_system_id);
            if ($response==0){   //subscription ended
                $this->deactive_subscription_in_db($s);
                $this->canceled_subscription++;
            }
            else{  //rate limit
                $this->failed_jobs++;
            }
        }
    }

    public function paginator()
    {
        $now = Carbon::now()->timezone('America/Chicago')->toDateTimeString();

        $start = Carbon::now();

        $where = [['subscriptions.state_id','!=' , 3],
                    ['subscriptions.subscription_end_at', '<',$now]];

        $get = ['subscriptions.id as subscription_id',
                'subscriptions.device_id',
                'applications.credential',
                'applications.operating_system_id'
                ];

        $limit = 2000;

        $offset = 0;

        while (true){
            $this->part++;
            echo 'PART'.$this->part.PHP_EOL;

            $subscriptions = DB::table('subscriptions')
            ->join('devices', 'subscriptions.device_id', '=', 'devices.id')
            ->join('applications', 'devices.app_id', '=', 'applications.id')
            ->where($where)->limit($limit)->offset($offset)->get($get)->toArray();

            if(! $subscriptions){
                break;
            }
            $offset+=$limit;
            $this->query_results_render($subscriptions);
        }

        $finish = Carbon::now();
        $diff = $start->diff($finish)->format('%H:%I:%S').PHP_EOL;
        echo 'Expired subscription: '.$this->expired_subscription.PHP_EOL;
        echo 'Canceled subscription: '.$this->canceled_subscription.PHP_EOL;
        echo 'Failed jobs: '.$this->failed_jobs.PHP_EOL;
        echo 'Ended: '.$finish.PHP_EOL;
        echo 'Duration :'.$diff;
        return true;
    }

    public function handle(){

        $this->paginator();

        if ($this->failed_jobs != 0){

            $this->expired_subscription=0;
            $this->canceled_subscription=0;
            $this->failed_jobs=0;
            $this->part=0;
            
            echo 'Started Failed Jobs';
            $this->paginator();
            echo 'End';
        }
    }
}

