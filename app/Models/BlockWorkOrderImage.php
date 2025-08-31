<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockWorkOrderImage extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'block_work_order_id',
        'image_name',
        'image_path',
        'strat_time',
        's3_status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'block_work_order_id' => 'integer',
        's3_status' => 'integer',
    ];

    /**
     * Get the block work order that owns the image.
     */
    public function blockWorkOrder()
    {
        return $this->belongsTo(BlockWorkOrder::class);
    }

    /**
     * Get the image URL.
     */
    public function getImageUrlAttribute()
    {
        if ($this->image_path && $this->image_name) {
            return asset('storage/' . $this->image_path . '/' . $this->image_name);
        }
        
        return null;
    }
}
