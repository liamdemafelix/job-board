<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class JobPost extends Model
{
    use HasUlids;

    protected $fillable = [
        'user_id',
        'import_id',
        'subcompany',
        'office',
        'department',
        'name',
        'employment_type',
        'seniority',
        'schedule',
        'years_of_experience',
        'occupation',
        'occupation_category',
        'created_at' // needed to override model events
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobPostDescriptions()
    {
        return $this->hasMany(JobPostDescription::class);
    }

    public function keywords()
    {
        return $this->belongsToMany(Keyword::class);
    }
}
