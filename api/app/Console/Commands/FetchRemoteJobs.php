<?php

namespace App\Console\Commands;

use App\Models\JobPost;
use App\Models\JobPostDescription;
use App\Models\Keyword;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class FetchRemoteJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-remote-jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches the latest remote jobs from the API and stores them in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $url = 'https://mrge-group-gmbh.jobs.personio.de/xml';
        $response = Http::get($url);
        if ($response->failed()) {
            $this->error('Failed to fetch remote jobs');
            return 1;
        }

        // Parse the XML response
        $xml = simplexml_load_string($response->body());
        if ($xml === false) {
            $this->error('Failed to parse XML response');
            return 1;
        }
        $position = $xml->position;

        // Parse into JobPost model structure
        $jobPostData = [
            'import_id' => trim((string) $position->id),
            'subcompany' => trim((string) $position->subcompany),
            'office' => trim((string) $position->office),
            'department' => trim((string) $position->department),
            'name' => trim((string) $position->name),
            'employment_type' => trim((string) $position->employmentType),
            'seniority' => trim((string) $position->seniority),
            'schedule' => trim((string) $position->schedule),
            'years_of_experience' => trim((string) $position->yearsOfExperience),
            'occupation' => trim((string) $position->occupation),
            'occupation_category' => trim((string) $position->occupationCategory),
            'created_at' => Carbon::parse(trim((string) $position->createdAt)),
        ];

        // Parse into Keyword model structure
        $keywordData = [];
        foreach (explode(',', (string) $position->keywords) as $keyword) {
            $keywordData[] = [
                'name' => trim($keyword),
            ];
        }

        // Parse into JobPostDescription model structure
        $descriptionData = [];
        foreach ($position->jobDescriptions->jobDescription as $description) {
            $descriptionData[] = [
                'name' => trim((string) $description->name),
                'value' => trim((string) $description->value),
            ];
        }

        // Create the records if needed
        try {
            DB::beginTransaction();

            // Update or create the job post based on import id
            $jobPost = JobPost::updateOrCreate([
                'import_id' => $jobPostData['import_id'],
            ], $jobPostData);
            if ($jobPost) {
                // First detach all existing keywords
                $jobPost->keywords()->detach();

                // Then attach each keyword individually
                foreach ($keywordData as $keyword) {
                    $keywordModel = Keyword::firstOrCreate($keyword);
                    $jobPost->keywords()->attach($keywordModel->id, ['id' => strtolower((string) \Illuminate\Support\Str::ulid())]);
                }

                // Create job post descriptions
                foreach ($descriptionData as $description) {
                    $description['job_post_id'] = $jobPost->id;
                    JobPostDescription::updateOrCreate([
                        'job_post_id' => $description['job_post_id'],
                        'name' => $description['name'],
                    ], $description);
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $this->error('Failed to create job post: ' . $e->getMessage());
            return 1;
        }
    }
}
