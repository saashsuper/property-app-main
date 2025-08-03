<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderImage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'work_order_id',
        'image',
        'common_status_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'work_order_id' => 'integer',
        'common_status_id' => 'integer',
    ];

    /**
     * Get the work order that owns the image.
     */
    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    /**
     * Get the image URL.
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/work-orders/' . $this->image);
        }
        
        return null;
    }
}
