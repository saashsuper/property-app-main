<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockContractor extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'block_id',
        'contractor_type_id',
        'contractor_id',
        'status',
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
        'status' => 'integer',
        'contractor_type_id' => 'integer',
        'contractor_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
    ];

    /**
     * Get the block that owns the contractor.
     */
    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    /**
     * Get the creator of the contractor.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the updater of the contractor.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the deleter of the contractor.
     */
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Scope a query to only include active contractors.
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Scope a query to only include default contractors.
     */
    public function scopeDefault($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Get the status text.
     */
    public function getStatusTextAttribute()
    {
        return $this->status == 1 ? 'Default' : 'Active';
    }
}
