<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public const STATUSES = ['new', 'contacted', 'discussion', 'in_progress', 'completed', 'cancelled'];

    protected $fillable = [
        'name', 'phone', 'email', 'organization', 'project_type', 'details',
        'budget_range', 'desired_deadline', 'contact_preference', 'status',
        'admin_notes', 'source',
    ];

    protected $casts = [
        'desired_deadline' => 'date',
    ];
}
