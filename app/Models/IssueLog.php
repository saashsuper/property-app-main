<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'block_issue_id',
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
        'block_issue_id' => 'integer',
        'related_id' => 'integer',
        'user_id' => 'integer',
    ];

    // Relationships
    public function blockIssue()
    {
        return $this->belongsTo(BlockIssue::class);
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
        'created' => 'Issue Created',
        'updated' => 'Issue Updated',
        'status_changed' => 'Status Changed',
        'priority_changed' => 'Priority Changed',
        'assigned' => 'Issue Assigned',
        'work_order_created' => 'Work Order Created',
        'site_visit_assigned' => 'Site Visit Assigned',
        'action_added' => 'Action Added',
        'comment_added' => 'Comment Added',
        'attachment_added' => 'Attachment Added',
        'contractor_assigned' => 'Contractor Assigned',
    ];

    /**
     * Helper method to create a log entry
     */
    public static function createLog($blockIssueId, $logType, $description, $options = [])
    {
        return static::create([
            'block_issue_id' => $blockIssueId,
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
     * Scope to get logs for a specific issue
     */
    public function scopeForIssue($query, $issueId)
    {
        return $query->where('block_issue_id', $issueId);
    }

    /**
     * Scope to get logs of a specific type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('log_type', $type);
    }
}
