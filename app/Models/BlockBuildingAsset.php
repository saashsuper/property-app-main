<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockBuildingAsset extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'integer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'block_inspection_value_type_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
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
}
