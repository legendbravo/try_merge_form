<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add all workflow statuses to each report table
        $statuses = [
            'submitted',
            'no submission',
            'pending',
            'approved',
            'rejected',
            'returned_for_resubmission',
            'resubmitted'
        ];
        $enum = implode("','", $statuses);
        DB::statement("ALTER TABLE weekly_reports MODIFY COLUMN status ENUM('$enum') DEFAULT 'no submission'");
        DB::statement("ALTER TABLE monthly_reports MODIFY COLUMN status ENUM('$enum') DEFAULT 'no submission'");
        DB::statement("ALTER TABLE quarterly_reports MODIFY COLUMN status ENUM('$enum') DEFAULT 'no submission'");
        DB::statement("ALTER TABLE semestral_reports MODIFY COLUMN status ENUM('$enum') DEFAULT 'no submission'");
        DB::statement("ALTER TABLE annual_reports MODIFY COLUMN status ENUM('$enum') DEFAULT 'no submission'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to only the original statuses
        DB::statement("ALTER TABLE weekly_reports MODIFY COLUMN status ENUM('submitted','no submission') DEFAULT 'no submission'");
        DB::statement("ALTER TABLE monthly_reports MODIFY COLUMN status ENUM('submitted','no submission') DEFAULT 'no submission'");
        DB::statement("ALTER TABLE quarterly_reports MODIFY COLUMN status ENUM('submitted','no submission') DEFAULT 'no submission'");
        DB::statement("ALTER TABLE semestral_reports MODIFY COLUMN status ENUM('submitted','no submission') DEFAULT 'no submission'");
        DB::statement("ALTER TABLE annual_reports MODIFY COLUMN status ENUM('submitted','no submission') DEFAULT 'no submission'");
    }
}; 