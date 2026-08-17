<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'title', 'slug', 'client', 'category', 'summary', 'description',
        'cover_image', 'images', 'technologies', 'project_url', 'repository_url',
        'year', 'status', 'is_featured', 'is_published', 'sort_order',
    ];

    protected $casts = [
        'images' => 'array',
        'technologies' => 'array',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'year' => 'integer',
        'sort_order' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
