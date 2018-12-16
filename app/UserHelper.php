<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserHelper extends Model
{
    protected $fillable = [
        'user_id',
        'searches_amount',
        'changes_amount',
        'user_attributes'
    ];

    protected $casts = [
        'user_attributes' => 'array'
    ];

    protected $attributes = [
        'user_attributes' => '[]'
    ];


}
