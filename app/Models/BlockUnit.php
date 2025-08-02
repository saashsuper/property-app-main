<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockUnit extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'block_id',
        'block_building_id',
        'block_unit_type_id',
        'unit_code',
        'unit_name',
        'owners_name',
        'salutation',
        'email',
        'resident',
        'address1',
        'address2',
        'address3',
        'country_id',
        'state_id',
        'zip',
        'mobile_no',
        'phone_number',
        'letting_agent',
        'misc_info',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'resident' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
    ];

    /**
     * Get the block that owns the unit.
     */
    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    /**
     * Get the building that owns the unit.
     */
    public function building()
    {
        return $this->belongsTo(BlockBuilding::class, 'block_building_id');
    }

    /**
     * Get the unit type that owns the unit.
     */
    public function unitType()
    {
        return $this->belongsTo(BlockUnitType::class, 'block_unit_type_id');
    }
}
