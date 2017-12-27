<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Badge extends Model
{
    protected $fillable = [
        'user_id',
        'url',
        'code',
        'do_not_possess',
        'login_enabled',
        'data'
    ];

    protected $visible = [
        'id',
        'url',
        'code',
        'data',
        'do_not_possess'
    ];


    protected $casts = [
        'do_not_possess' => 'boolean',
        'login_enabled' => 'boolean',
        'data' => 'array'
    ];


    public function user() {
        return $this->belongsTo('App\User');
    }

}