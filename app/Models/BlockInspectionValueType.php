<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockInspectionValueType extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'integer';

    protected $fillable = [
        'id',
        'name',
    ];

    /**
     * Get the inspection values for this type.
     */
    public function inspectionValues()
    {
        return $this->hasMany(BlockInspectionValue::class, 'block_inspection_value_type_id');
    }

    /**
     * Get the building assets for this type.
     */
    public function buildingAssets()
    {
        return $this->hasMany(BuildingAsset::class, 'block_inspection_value_type_id');
    }
}
