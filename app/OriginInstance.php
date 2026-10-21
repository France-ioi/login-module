<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OriginInstance extends Model
{
    use \App\LoginModule\Shared\SerializesLegacyDates;


    protected $fillable = [
        'id',
        'name'
    ];

}
