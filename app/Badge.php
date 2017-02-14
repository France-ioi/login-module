<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $fillable = [
        'user_id',
        'url',
        'infos',
        'do_not_possess'
    ];

    protected $visible = [
        'id',
        'url',
        'infos',
        'do_not_possess'
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }
}
