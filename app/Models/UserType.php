<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'user_types';

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the users that belong to this type
     */
    public function users()
    {
        return $this->hasMany(User::class, 'user_role_id');
    }
} 