<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Device extends Model
{
   use HasFactory;
   protected $guarded = ['id'];
   const UPDATED_AT = null;
}
