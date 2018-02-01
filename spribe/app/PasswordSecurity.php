<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PasswordSecurity extends Model
{
    protected $fillable = ['user_id'];

    public function user(){
        $this->belongsTo('App\User');
    }
}
