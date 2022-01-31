<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientUser extends Model
{
    
    protected $primaryKey = ['client_id', 'user_id'];
    protected $table = 'oauth_client_user';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'client_id',
        'admin',
        'last_activity',
        'banned'
    ];




    protected function setKeysForSaveQuery($query) {
        $keys = $this->getKeyName();
        if(!is_array($keys)){
            return parent::setKeysForSaveQuery($query);
        }

        foreach($keys as $keyName){
            $query->where($keyName, '=', $this->getNamedKeyForSaveQuery($keyName));
        }

        return $query;
    }


    protected function getNamedKeyForSaveQuery($keyName) {
        if(is_null($keyName)){
            $keyName = $this->getKeyName();
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }    
}
