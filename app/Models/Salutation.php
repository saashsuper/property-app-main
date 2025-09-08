<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salutation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'common_status_id',
    ];

    protected $casts = [
        'common_status_id' => 'integer',
    ];
}
