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

    /**
     * Get the full image URL.
     */
    public function getImageUrlAttribute()
    {
        if ($this->image_path && $this->image_name) {
            return asset('storage/' . $this->image_path . '/' . $this->image_name);
        }
        
        return null;
    }

    /**
     * Get the image filename for display.
     */
    public function getDisplayNameAttribute()
    {
        if ($this->image_name) {
            // Remove timestamp prefix and show original name if possible
            $parts = explode('_', $this->image_name);
            if (count($parts) > 2) {
                array_shift($parts); // Remove timestamp
                array_shift($parts); // Remove random string
                return implode('_', $parts);
            }
            return $this->image_name;
        }
        
        return 'Image';
    }

    /**
     * Get the file size in human readable format.
     */
    public function getFileSizeAttribute()
    {
        $path = storage_path('app/public/' . $this->image_path . '/' . $this->image_name);
        if (file_exists($path)) {
            $bytes = filesize($path);
            $units = ['B', 'KB', 'MB', 'GB'];
            $bytes = max($bytes, 0);
            $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
            $pow = min($pow, count($units) - 1);
            $bytes /= pow(1024, $pow);
            return round($bytes, 2) . ' ' . $units[$pow];
        }
        
        return 'Unknown';
    }
}
