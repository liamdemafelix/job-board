<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use App\Models\Keyword;
use Illuminate\Http\Request;

class JobPostController extends Controller
{
    public function index(Request $request)
    {
        try {
            $jobs = JobPost::with('user', 'jobPostDescriptions', 'keywords')->orderBy('created_at', 'DESC');
            if (!empty($request->tags)) {
                $tags = explode(',', $request->tags);
                $jobs->whereHas('keywords', function ($query) use ($tags) {
                    $query->whereIn('name', $tags);
                });
            }

            $jobs = $jobs->paginate(5);
            return response()->json([
                'data' => $jobs
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching job posts.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(JobPost $jobPost)
    {
        try {
            $job = JobPost::with('user', 'jobPostDescriptions', 'keywords')->findOrFail($jobPost->id);
            return response()->json([
                'data' => $job
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching the job post.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function keywords()
    {
        try {
            $keywords = Keyword::orderBy('name', 'ASC')->get();
            return response()->json([
                'data' => $keywords
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching keywords.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
