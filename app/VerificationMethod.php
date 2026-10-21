<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VerificationMethod extends Model
{
    use \App\LoginModule\Shared\SerializesLegacyDates;

    protected $fillable = [
        'id',
        'name',
        'user_attributes',
        'public'
    ];

    protected $casts = [
        'user_attributes' => 'array'
    ];

    public function verifications() {
        return $this->hasMany('App\Verification', 'method_id');
    }
}
