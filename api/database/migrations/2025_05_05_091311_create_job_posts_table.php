<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('company')->nullable()->after('last_name');
        });

        Schema::create('job_posts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('import_id')->nullable();
            $table->string('subcompany')->nullable();
            $table->string('office');
            $table->string('department');
            $table->string('name');
            $table->string('employment_type');
            $table->string('seniority');
            $table->string('schedule');
            $table->string('years_of_experience');
            $table->string('occupation');
            $table->string('occupation_category');
            $table->timestamps();
        });

        Schema::create('job_post_descriptions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('job_post_id')->constrained('job_posts')->cascadeOnDelete();
            $table->string('name');
            $table->text('value');
            $table->timestamps();
        });

        Schema::create('keywords', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('job_post_keyword', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('job_post_id')->constrained('job_posts')->cascadeOnDelete();
            $table->foreignUlid('keyword_id')->constrained('keywords')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_post_keyword');
        Schema::dropIfExists('keywords');
        Schema::dropIfExists('job_post_descriptions');
        Schema::dropIfExists('job_posts');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('company');
        });
    }
};
