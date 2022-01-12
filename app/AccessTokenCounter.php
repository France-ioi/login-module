<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class AccessTokenCounter extends Model
{

    protected $primaryKey = ['user_id', 'client_id'];

    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'client_id',
        'last_created_at',
        'total'
    ];


    protected function setKeysForSaveQuery($query) {
        return $query
            ->where('user_id', $this->user_id)
            ->where('client_id', $this->client_id);
    }

}