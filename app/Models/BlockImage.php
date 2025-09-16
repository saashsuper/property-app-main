<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BlockImage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'block_id',
        'original_name',
        'stored_name',
        'file_path',
        'file_extension',
        'file_size',
        'mime_type',
        'sort_order',
        'is_primary',
        'description',
        'uploaded_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'file_size' => 'integer',
        'sort_order' => 'integer',
        'is_primary' => 'boolean',
        'uploaded_by' => 'integer',
    ];

    /**
     * Get the block that owns the image.
     */
    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    /**
     * Get the user who uploaded the image.
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the full file path for the image.
     */
    public function getFullPathAttribute()
    {
        return $this->file_path . '/' . $this->stored_name;
    }

    /**
     * Get the URL for the image.
     */
    public function getUrlAttribute()
    {
        return Storage::url($this->full_path);
    }

    /**
     * Get the file size in human readable format.
     */
    public function getFileSizeHumanAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Scope to get images ordered by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at');
    }

    /**
     * Scope to get primary image.
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Delete the image file when the model is deleted.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($image) {
            // Delete the file from storage
            if (Storage::exists($image->full_path)) {
                Storage::delete($image->full_path);
            }
        });
    }
}