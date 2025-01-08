<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientKey extends Model
{
    protected $fillable = ['user_id','public_key'];

    public function user(){
        return  $this->belongsTo(User::class);
    }
}
