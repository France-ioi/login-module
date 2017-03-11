<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Badge extends Model
{
    protected $fillable = [
        'user_id',
        'url',
        'code',
        'do_not_possess'
    ];

    protected $visible = [
        'id',
        'url',
        'code',
        'do_not_possess'
    ];


    protected $casts = [
        'do_not_possess' => 'boolean'
    ];


    public function user() {
        return $this->belongsTo('App\User');
    }

}