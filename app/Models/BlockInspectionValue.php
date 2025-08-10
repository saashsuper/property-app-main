<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockInspectionValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'block_inspection_value_type_id',
        'name',
        'description',
    ];

    protected $casts = [
        'block_inspection_value_type_id' => 'integer',
    ];

    /**
     * Get the value type.
     */
    public function valueType()
    {
        return $this->belongsTo(BlockInspectionValueType::class, 'block_inspection_value_type_id');
    }

    /**
     * Get the inspection assets that use this value.
     */
    public function inspectionAssets()
    {
        return $this->hasMany(BlockInspectionAsset::class, 'block_inspection_value_id');
    }
}
