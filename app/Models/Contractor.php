<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contractor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = '1_contractors';

    protected $fillable = [
        'name',
        'code',
        'type',
        'address_1',
        'address_2',
        'address_3',
        'phone',
        'mobile',
        'emergency',
        'email',
        'secondary_email',
        'work_order_email',
        'main_contact',
        'main_contact_email',
        'main_contact_phone',
        'accounts_contact',
        'accounts_email',
        'email_remittence',
        'general_notes',
        'account_name',
        'bank',
        'bic',
        'iban',
        'pay_emts',
        'insurance_cover',
        'policy_number',
        'policy_expiry',
        'public_liablity_policy',
        'health_safety',
        'health_safety_statement',
        'common_status_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user who created this contractor.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this contractor.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

