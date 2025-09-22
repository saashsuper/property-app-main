<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueStatus extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'label',
        'value',
        'btn_class',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'value' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the issues associated with this status.
     */
    public function issues()
    {
        return $this->hasMany(Issue::class, 'status');
    }

    /**
     * Get the block issues associated with this status.
     */
    public function blockIssues()
    {
        return $this->hasMany(BlockIssue::class, 'issue_status_id');
    }

    /**
     * Get the work orders associated with this status.
     */
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class, 'status');
    }

    /**
     * Get the block work orders associated with this status.
     */
    public function blockWorkOrders()
    {
        return $this->hasMany(BlockWorkOrder::class, 'status');
    }

    /**
     * Scope to get statuses ordered by value.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('value', 'asc');
    }

    /**
     * Get the CSS button class for this status.
     */
    public function getButtonClassAttribute()
    {
        return $this->btn_class ?? 'secondary';
    }

    /**
     * Get the formatted label with value.
     */
    public function getFormattedLabelAttribute()
    {
        return "{$this->label} ({$this->value})";
    }

    /**
     * Check if this status indicates the issue is open.
     */
    public function isOpen()
    {
        return in_array($this->value, [1, 2]); // Created, In Progress
    }

    /**
     * Check if this status indicates the issue is closed.
     */
    public function isClosed()
    {
        return in_array($this->value, [4, 5]); // Completed, Invoiced
    }

    /**
     * Check if this status indicates the issue is in work order phase.
     */
    public function isWorkOrder()
    {
        return $this->value === 3; // Work Order
    }
}

