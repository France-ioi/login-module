<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\LoginModule\Platform\BadgeApi;

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


    protected static function boot() {
        static::updating(function($model) {
            if($model->isDirty('do_not_possess') && $model->do_not_possess == 0) {
                BadgeApi::update($model->url, $model->code, $model->user_id);
            }
        });
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

}