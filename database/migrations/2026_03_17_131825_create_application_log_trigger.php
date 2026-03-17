<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {

        DB::unprepared("
            CREATE TRIGGER after_application_insert
            AFTER INSERT ON applications
            FOR EACH ROW
            BEGIN
                INSERT INTO user_logs (user_id, action, table_name, record_id,details, created_at)
                VALUES (NEW.user_id, 'SUCCESSFUL_APPLICATION', 'applications', NEW.user_id,  'From trigger ', NOW());
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS after_application_insert");
    }
};