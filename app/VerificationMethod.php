<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VerificationMethod extends Model
{
    protected $fillable = [
        'id',
        'name',
        'user_attributes',
        'public',
        'is_global'
    ];

    protected $casts = [
        'user_attributes' => 'array'
    ];

    public function verifications() {
        return $this->hasMany('App\Verification', 'method_id');
    }
}
