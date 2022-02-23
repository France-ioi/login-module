<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use App\Notifications\EmailVerificationNotification;

class Verification extends Model
{

    use Notifiable;

    protected $fillable = [
        'user_id',
        'method_id',
        'client_id',
        'user_attributes',
        'rejected_attributes',
        'status',
        'approved_at',
        'confidence',
        'message',
        'file',
        'code',
        'email'
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
        parent::boot();
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


    public function sendVerificationCode() {
        $this->code = str_random(10);
        try {
            $this->notify(new EmailVerificationNotification());
        } catch (\Exception $e) {
            return false;
        }
        $this->save();
        return true;
    }    


    public function getCodeInputUrl() {
        return route('verification.email_domain.input_code', ['id' => $this->id]).'?code='.urlencode($this->code);
    }
}