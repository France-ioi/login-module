<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlatformGroup extends Model
{
    protected $fillable = [
        'participation_code',
        'group_code',
        'client_id'
    ];

}
