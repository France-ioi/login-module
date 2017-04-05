<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class PmsAdminBadge extends Model
{
    protected $fillable = [
        'pms_id',
        'badge',
    ];

    protected $visible = [
        'pms_id',
        'badge',
    ];
}
