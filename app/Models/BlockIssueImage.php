<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockIssueImage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'block_issue_id',
        'block_issue_action_id',
        'tocken',
        'image_path',
        'image_name',
        'photo',
        's3_status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        's3_status' => 'boolean',
    ];

    /**
     * Get the block issue that owns the image.
     */
    public function blockIssue()
    {
        return $this->belongsTo(BlockIssue::class, 'block_issue_id');
    }

    /**
     * Get the block issue action that owns the image.
     */
    public function blockIssueAction()
    {
        return $this->belongsTo(BlockIssueAction::class, 'block_issue_action_id');
    }
}
