<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    protected $fillable = [
        'name',
    ];

    public function jobPosts()
    {
        return $this->belongsToMany(JobPost::class);
    }
}
