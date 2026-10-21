<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserDeletion extends Model
{
    use \App\LoginModule\Shared\SerializesLegacyDates;


    protected $fillable = [
        'user_id',
        'client_id',
    ];

}