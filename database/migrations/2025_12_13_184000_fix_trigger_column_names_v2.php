<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Fix invalidate_reports_on_return_change
        DB::unprepared('DROP TRIGGER IF EXISTS invalidate_reports_on_return_change');
        DB::unprepared('
            CREATE TRIGGER invalidate_reports_on_return_change
            AFTER UPDATE ON return_items
            FOR EACH ROW
            BEGIN
                UPDATE daily_outlet_reports dor
                INNER JOIN returns r ON r.id = OLD.id_return
                INNER JOIN users u ON u.id = r.id_staff
                INNER JOIN daily_outlet_report_items dori ON dor.id = dori.id_outlet_report
                SET dor.is_validated = FALSE
                WHERE dori.id_item = NEW.id_item
                  AND dor.id_outlet = u.outlet_id
                  AND DATE(dor.report_date) = DATE(r.returned_at) -- FIXED: created_at -> returned_at
                  AND OLD.quantity != NEW.quantity;
            END
        ');

        // 2. Fix invalidate_reports_on_return_delete
        DB::unprepared('DROP TRIGGER IF EXISTS invalidate_reports_on_return_delete');
        DB::unprepared('
            CREATE TRIGGER invalidate_reports_on_return_delete
            AFTER DELETE ON return_items
            FOR EACH ROW
            BEGIN
                UPDATE daily_outlet_reports dor
                INNER JOIN returns r ON r.id = OLD.id_return
                INNER JOIN users u ON u.id = r.id_staff
                INNER JOIN daily_outlet_report_items dori ON dor.id = dori.id_outlet_report
                SET dor.is_validated = FALSE
                WHERE dori.id_item = OLD.id_item
                  AND dor.id_outlet = u.outlet_id
                  AND DATE(dor.report_date) = DATE(r.returned_at); -- FIXED: created_at -> returned_at
            END
        ');
    }

    public function down(): void
    {
        // Revert to previous state (which was broken with created_at, but had correct outlet_id)
        
        DB::unprepared('DROP TRIGGER IF EXISTS invalidate_reports_on_return_change');
        DB::unprepared('DROP TRIGGER IF EXISTS invalidate_reports_on_return_delete');
        
        // Recreate with created_at (broken state)
        DB::unprepared('
            CREATE TRIGGER invalidate_reports_on_return_change
            AFTER UPDATE ON return_items
            FOR EACH ROW
            BEGIN
                UPDATE daily_outlet_reports dor
                INNER JOIN returns r ON r.id = OLD.id_return
                INNER JOIN users u ON u.id = r.id_staff
                INNER JOIN daily_outlet_report_items dori ON dor.id = dori.id_outlet_report
                SET dor.is_validated = FALSE
                WHERE dori.id_item = NEW.id_item
                  AND dor.id_outlet = u.outlet_id
                  AND DATE(dor.report_date) = DATE(r.created_at)
                  AND OLD.quantity != NEW.quantity;
            END
        ');

        DB::unprepared('
            CREATE TRIGGER invalidate_reports_on_return_delete
            AFTER DELETE ON return_items
            FOR EACH ROW
            BEGIN
                UPDATE daily_outlet_reports dor
                INNER JOIN returns r ON r.id = OLD.id_return
                INNER JOIN users u ON u.id = r.id_staff
                INNER JOIN daily_outlet_report_items dori ON dor.id = dori.id_outlet_report
                SET dor.is_validated = FALSE
                WHERE dori.id_item = OLD.id_item
                  AND dor.id_outlet = u.outlet_id
                  AND DATE(dor.report_date) = DATE(r.created_at);
            END
        ');
    }
};
