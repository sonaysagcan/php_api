<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Api\OperatingSystem;

class OperatingSystemController extends Controller
{
    public function get_os_id($os_name)
    {
        $os_data = OperatingSystem::where('os_name', $os_name)->first(['id']);

        if (! $os_data){
            return false;
        }
        return $os_data->id;
    }
}
