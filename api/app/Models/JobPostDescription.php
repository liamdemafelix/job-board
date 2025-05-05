<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class JobPostDescription extends Model
{
    use HasUlids;

    protected $fillable = [
        'job_post_id',
        'name',
        'value',
    ];

    public function jobPost()
    {
        return $this->belongsTo(JobPost::class);
    }
}
