<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use App\Models\Keyword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

            // If user is not logged in, or is a seeker, only show public jobs
            if (Auth::guest() || !Auth::user()->is_moderator && !Auth::user()->company) {
                $jobs->where('spam_level', 0);
            } elseif (Auth::user()->company) {
                // The user is an employer. Show only jobs that are public, or jobs that they own even if they're not public.
                $jobs->where('spam_level', 0)->orWhere(function ($query) {
                    $query->where('user_id', Auth::user()->id)
                        ->where('spam_level', '!=', 0);
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
            if (Auth::guest() || !Auth::user()->is_moderator && $jobPost->user_id != Auth::user()->id) {
                if ($job->spam_level != 0) {
                    return response()->json([
                        'error' => 'This job post is not available.',
                    ], 404);
                }
            }
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

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'department' => ['required', 'string', 'max:255'],
                'descriptions' => ['required', 'array', 'min:1'],
                'descriptions.*.title' => ['required', 'string', 'max:255'],
                'descriptions.*.content' => ['required', 'string'],
                'employment_type' => ['required', 'string', 'max:255'],
                'keywords' => ['required', 'array', 'min:1'],
                'keywords.*' => ['required', 'string', 'max:255'],
                'name' => ['required', 'string', 'max:255'],
                'occupation' => ['required', 'string', 'max:255'],
                'occupation_category' => ['required', 'string', 'max:255'],
                'office' => ['required', 'string', 'max:255'],
                'schedule' => ['required', 'string', 'max:255'],
                'seniority' => ['required', 'string', 'max:255'],
                'years_of_experience' => ['required', 'integer', 'min:0'],
            ]);
            $validatedData['user_id'] = $request->user()->id;
            $validatedData['spam_level'] = 0; // Publish
            if (JobPost::where('user_id', $request->user()->id)->where('spam_level', 1)->count() == 0) {
                $validatedData['spam_level'] = -1; // Flag
            }

            DB::beginTransaction();
            $jobPost = JobPost::create($validatedData);
            foreach ($validatedData['keywords'] as $keyword) {
                $keywordModel = Keyword::firstOrCreate(['name' => $keyword]);
                $jobPost->keywords()->attach($keywordModel->id, ['id' => strtolower((string) Str::ulid())]);
            }
            foreach ($validatedData['descriptions'] as $description) {
                $jobPost->jobPostDescriptions()->create([
                    'name' => $description['title'],
                    'value' => nl2br($description['content'])
                ]);
            }
            DB::commit();

            return response()->json([
                'data' => $jobPost
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'An error occurred while creating the job post.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function posts()
    {
        if (Auth::guest()) {
            return response()->json([
                'error' => 'You must be logged in to view your job posts.'
            ], 403);
        }
        try {
            $posts = JobPost::with('user', 'jobPostDescriptions', 'keywords')->orderBy('created_at', 'DESC');

            if (!Auth::user()->is_moderator) {
                // Show only my posts
                $posts = $posts->where('user_id', Auth::user()->id);
            }

            $posts = $posts->paginate(5);

            return response()->json([
                'data' => $posts
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching your job posts.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function moderate(JobPost $jobPost, Request $request)
    {
        if (Auth::guest() || !Auth::user()->is_moderator) {
            return response()->json([
                'error' => 'You must be logged in as a moderator to moderate job posts.'
            ], 403);
        }
        try {
            $validatedData = $request->validate([
                'spam_level' => ['required', 'integer', 'in:-1,0,1'],
            ]);
            $jobPost->update($validatedData);
            return response()->json([
                'data' => $jobPost
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while moderating the job post.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
