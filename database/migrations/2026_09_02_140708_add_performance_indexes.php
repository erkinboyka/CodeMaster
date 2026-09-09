<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // problem_user: used in status checks, solved counts, progress queries
        Schema::table('problem_user', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'pu_user_status_idx');
            $table->index(['problem_id', 'status'], 'pu_problem_status_idx');
        });

        // user_activities: used in daily activity, XP tracking, daily bonus checks
        Schema::table('user_activities', function (Blueprint $table) {
            $table->index(['user_id', 'activity_type', 'activity_time'], 'ua_user_type_time_idx');
        });

        // problem_submissions: used in submission history, performance stats
        Schema::table('problem_submissions', function (Blueprint $table) {
            $table->index(['user_id', 'problem_id', 'status'], 'ps_user_problem_status_idx');
            $table->index(['problem_id', 'status'], 'ps_problem_status_idx');
        });

        // contest_submissions: used in leaderboard, user submission lookup
        Schema::table('contest_submissions', function (Blueprint $table) {
            $table->index(['contest_id', 'user_id', 'status'], 'cs_contest_user_status_idx');
        });

        // community_posts: used in listing, sorting
        Schema::table('community_posts', function (Blueprint $table) {
            $table->index('created_at', 'cp_created_at_idx');
        });

        // user_course_progress: used in dashboard stats, course enrollment checks
        Schema::table('user_course_progress', function (Blueprint $table) {
            $table->index(['user_id', 'completed'], 'ucp_user_completed_idx');
        });

        // notifications: used in mark-read, unread count
        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['user_id', 'is_read', 'notification_time'], 'n_user_read_time_idx');
        });

        // chat_messages: used in message history
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->index(['user_id', 'sent_at'], 'cm_user_time_idx');
        });

        // user_applications: used in vacancy chat, application lookup
        Schema::table('user_applications', function (Blueprint $table) {
            $table->index(['user_id', 'vacancy_id'], 'ua_user_vacancy_idx');
        });

        // practice_submissions: used in progress stats
        Schema::table('practice_submissions', function (Blueprint $table) {
            $table->index(['user_id', 'passed'], 'ps_user_passed_idx');
        });
    }

    public function down(): void
    {
        Schema::table('problem_user', function (Blueprint $table) {
            $table->dropIndex('pu_user_status_idx');
            $table->dropIndex('pu_problem_status_idx');
        });
        Schema::table('user_activities', function (Blueprint $table) {
            $table->dropIndex('ua_user_type_time_idx');
        });
        Schema::table('problem_submissions', function (Blueprint $table) {
            $table->dropIndex('ps_user_problem_status_idx');
            $table->dropIndex('ps_problem_status_idx');
        });
        Schema::table('contest_submissions', function (Blueprint $table) {
            $table->dropIndex('cs_contest_user_status_idx');
        });
        Schema::table('community_posts', function (Blueprint $table) {
            $table->dropIndex('cp_created_at_idx');
        });
        Schema::table('user_course_progress', function (Blueprint $table) {
            $table->dropIndex('ucp_user_completed_idx');
        });
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('n_user_read_time_idx');
        });
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropIndex('cm_user_time_idx');
        });
        Schema::table('user_applications', function (Blueprint $table) {
            $table->dropIndex('ua_user_vacancy_idx');
        });
        Schema::table('practice_submissions', function (Blueprint $table) {
            $table->dropIndex('ps_user_passed_idx');
        });
    }
};
