<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockBuilding extends Model
{
    use HasFactory;

    protected $table = 'block_buildings';
    protected $fillable = [
        'block_id',
        'building_type_id',
        'name',
        'floor_no',
        'roof_type',
        'no_lift',
    ];

    public function buildingType()
    {
        return $this->belongsTo(BlockBuildingType::class, 'building_type_id');
    }
}
