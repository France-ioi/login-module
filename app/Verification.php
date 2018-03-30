<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{


    protected $fillable = [
        'user_id',
        'method_id',
        'user_attributes',
        'rejected_attributes',
        'status',
        'approved_at',
        'confidence',
        'message',
        'file',
        'code'
    ];

    protected $casts = [
        'user_attributes' => 'array',
        'rejected_attributes' => 'array'
    ];

    protected $attributes = [
        'user_attributes' => '[]',
        'rejected_attributes' => '[]'
    ];

    protected static function boot() {
        static::saving(function($model) {
            if($model->isDirty('status') && $model->status == 'approved') {
                $model->approved_at = new \DateTime;
            }
        });
    }

    public function method() {
        return $this->belongsTo('App\VerificationMethod', 'method_id');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }
}
