<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServerKey extends Model
{
    protected $fillable =['public_key', 'private_key'];
}
