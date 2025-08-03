<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'property_id',
        'contractor_id',
        'user_id',
        'priority',
        'priority_label',
        'fault_description',
        'issue_category',
        'issue_type',
        'issued_date',
        'deadline',
        'pricing',
        'contact_name',
        'contact_number',
        'contact_email',
        'preferred_day',
        'time_from',
        'time_to',
        'note',
        'report',
        'type',
        'type_id',
        'common_status_id',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'priority' => 'integer',
        'property_id' => 'integer',
        'contractor_id' => 'integer',
        'user_id' => 'integer',
        'type_id' => 'integer',
        'common_status_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Get the user that owns the work order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the images for the work order.
     */
    public function images()
    {
        return $this->hasMany(WorkOrderImage::class);
    }

    /**
     * Get the creator of the work order.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the updater of the work order.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include active work orders.
     */
    public function scopeActive($query)
    {
        return $query->where('common_status_id', 1);
    }

    /**
     * Get the priority text.
     */
    public function getPriorityTextAttribute()
    {
        return $this->priority_label ?? 'Normal';
    }

    /**
     * Get the status text.
     */
    public function getStatusTextAttribute()
    {
        return $this->common_status_id == 1 ? 'Active' : 'Inactive';
    }
}
