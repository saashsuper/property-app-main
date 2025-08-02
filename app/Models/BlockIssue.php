<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockIssue extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ref_no',
        'block_id',
        'issued_from',
        'from_id',
        'contractor_type_id',
        'priority_id',
        'block_unit_id',
        'contact_details',
        'issue',
        'issue_details',
        'contact_name',
        'contact_mobile',
        'contact_email',
        'preferred_start_date_time',
        'preferred_end_date_time',
        'note_for_access',
        'issued_by',
        'block_visit_id',
        'block_inspection_id',
        'issued_date_time',
        'comment',
        'is_mobile',
        'created_by',
        'updated_by',
        'issue_status_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'preferred_start_date_time' => 'datetime',
        'preferred_end_date_time' => 'datetime',
        'issued_date_time' => 'datetime',
        'is_mobile' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Get the block that owns the issue.
     */
    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    /**
     * Get the unit that owns the issue.
     */
    public function unit()
    {
        return $this->belongsTo(BlockUnit::class, 'block_unit_id');
    }

    /**
     * Get the user who issued the issue.
     */
    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    /**
     * Get the user who created the issue.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the issue.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
