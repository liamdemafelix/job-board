<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobPost extends Model
{
    protected $fillable = [
        'user_id',
        'import_id',
        'office',
        'department',
        'name',
        'employment_type',
        'seniority',
        'schedule',
        'years_of_experience',
        'occupation',
        'occupation_category',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function keywords()
    {
        return $this->belongsToMany(Keyword::class);
    }
}
