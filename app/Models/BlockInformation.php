<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockInformation extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'block_id',
        'information_type_id',
        'description',
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
        'information_type_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
    ];

    /**
     * Get the block that owns the information.
     */
    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    /**
     * Get the information type.
     */
    public function informationType()
    {
        return $this->belongsTo(BlockInformationType::class, 'information_type_id');
    }

    /**
     * Get the creator of the block information.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    /**
     * Get the updater of the block information.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by')->withTrashed();
    }

    /**
     * Get the deleter of the block information.
     */
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by')->withTrashed();
    }

    /**
     * Scope a query to only include active block information.
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Get the display name for the information type.
     */
    public function getInformationTypeDisplayAttribute()
    {
        return $this->informationType ? $this->informationType->display_name ?? ucwords(str_replace('_', ' ', $this->informationType->name)) : 'N/A';
    }
}
