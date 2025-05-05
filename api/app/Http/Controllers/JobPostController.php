<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use Illuminate\Http\Request;

class JobPostController extends Controller
{
    public function index()
    {
        try {
            $jobs = JobPost::with('user', 'jobPostDescriptions')->orderBy('created_at', 'DESC')->paginate(10);
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
}
