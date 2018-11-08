<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Badge extends Model
{
    protected $fillable = [
        'user_id',
        'url',
        'badge_api_id',
        'code',
        'do_not_possess',
        'login_enabled',
        'data',
        'comments',
        'origin_client_id'
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


    public function badgeApi() {
        return $this->belongsTo('App\BadgeApi');
    }


    public function getUrlAttribute() {
        if($this->badge_api_id) {
            return $this->badgeApi->url;
        }
        return $this->url;
    }

}