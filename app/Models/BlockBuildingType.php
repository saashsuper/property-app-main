<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockBuildingType extends Model
{
    use HasFactory;

    protected $table = 'block_building_types';
    protected $fillable = ['name'];
}
