<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{

    protected $fillable = [
        'user_id',
        'url'
    ];

    protected $visible = [
        'id',
        'url'
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }

}