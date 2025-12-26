<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockWorkOrderLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'block_work_order_id',
        'log_type',
        'description',
        'field_name',
        'old_value',
        'new_value',
        'related_id',
        'related_type',
        'user_id',
    ];

    protected $casts = [
        'block_work_order_id' => 'integer',
        'related_id' => 'integer',
        'user_id' => 'integer',
    ];

    // Relationships
    public function blockWorkOrder()
    {
        return $this->belongsTo(BlockWorkOrder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Polymorphic relationship for related entities
    public function related()
    {
        return $this->morphTo();
    }

    // Log type constants
    const LOG_TYPES = [
        'created' => 'Work Order Created',
        'updated' => 'Work Order Updated',
        'status_changed' => 'Status Changed',
        'priority_changed' => 'Priority Changed',
        'assigned' => 'Work Order Assigned',
        'accepted' => 'Work Order Accepted',
        'rejected' => 'Work Order Rejected',
        'started' => 'Work Order Started',
        'paused' => 'Work Order Paused',
        'resumed' => 'Work Order Resumed',
        'completed' => 'Work Order Completed',
        'cancelled' => 'Work Order Cancelled',
        'work_docket_generated' => 'Work Docket Generated',
        'work_docket_regenerated' => 'Work Docket Regenerated',
        'comment_added' => 'Comment Added',
        'comment_updated' => 'Comment Updated',
        'attachment_added' => 'Attachment Added',
        'attachment_deleted' => 'Attachment Deleted',
    ];

    /**
     * Helper method to create a log entry
     */
    public static function createLog($blockWorkOrderId, $logType, $description, $options = [])
    {
        return static::create([
            'block_work_order_id' => $blockWorkOrderId,
            'log_type' => $logType,
            'description' => $description,
            'field_name' => $options['field_name'] ?? null,
            'old_value' => $options['old_value'] ?? null,
            'new_value' => $options['new_value'] ?? null,
            'related_id' => $options['related_id'] ?? null,
            'related_type' => $options['related_type'] ?? null,
            'user_id' => $options['user_id'] ?? auth()->id(),
        ]);
    }

    /**
     * Scope to get logs for a specific work order
     */
    public function scopeForWorkOrder($query, $workOrderId)
    {
        return $query->where('block_work_order_id', $workOrderId);
    }

    /**
     * Scope to get logs of a specific type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('log_type', $type);
    }
}
