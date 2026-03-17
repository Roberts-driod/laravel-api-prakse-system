<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // We drop it first just in case it exists to avoid "Already Exists" errors
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_store_application");

        DB::unprepared("
            CREATE PROCEDURE sp_store_application(
                IN p_user_id INT,
                IN p_internship_id INT,
                IN p_group_id INT,
                IN p_motivation_letter TEXT
            )
            BEGIN
                DECLARE v_user_role VARCHAR(50);
                DECLARE v_exists INT;

                -- Check if User exists and get role
                SELECT r.role INTO v_user_role 
                FROM users u 
                JOIN roles r ON u.role_id = r.id 
                WHERE u.id = p_user_id;

                IF v_user_role IS NULL THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'User not found';
                END IF;

                -- Check if Internship exists
                IF (SELECT COUNT(*) FROM internships WHERE id = p_internship_id) = 0 THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Internship not found';
                END IF;

                -- Role Validation
                IF v_user_role != 'student' THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No permission';
                END IF;

                -- Check for existing application
                SELECT COUNT(*) INTO v_exists 
                FROM applications 
                WHERE user_id = p_user_id AND internship_id = p_internship_id;

                IF v_exists > 0 THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Already applied';
                END IF;

                -- Insert (Note: adjust column names if you disabled timestamps!)
                INSERT INTO applications (user_id, internship_id, group_id, motivation_letter, created_at, updated_at)
                VALUES (p_user_id, p_internship_id, p_group_id, p_motivation_letter, NOW(), NOW());
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_store_application");
    }
};