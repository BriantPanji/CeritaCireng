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
                  AND DATE(dor.report_date) = DATE(r.created_at)
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
                  AND DATE(dor.report_date) = DATE(r.created_at);
            END
        ');

        // 3. Fix invalidate_reports_on_expense_change
        DB::unprepared('DROP TRIGGER IF EXISTS invalidate_reports_on_expense_change');
        DB::unprepared('
            CREATE TRIGGER invalidate_reports_on_expense_change
            AFTER UPDATE ON other_expenses
            FOR EACH ROW
            BEGIN
                UPDATE daily_outlet_reports dor
                INNER JOIN users u ON u.id = NEW.id_staff
                INNER JOIN daily_outlet_report_expenses dore ON dor.id = dore.id_outlet_report
                SET dor.is_validated = FALSE
                WHERE dore.id_expense = NEW.id
                  AND dor.id_outlet = u.outlet_id
                  AND DATE(dor.report_date) = DATE(NEW.created_at)
                  AND (OLD.cost != NEW.cost OR OLD.category != NEW.category);
            END
        ');
    }

    public function down(): void
    {
        // Revert to broken state (original triggers)
        DB::unprepared('DROP TRIGGER IF EXISTS invalidate_reports_on_return_change');
        DB::unprepared('DROP TRIGGER IF EXISTS invalidate_reports_on_return_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS invalidate_reports_on_expense_change');
        
        // Recreate original triggers with wrong column name (u.id_outlet)
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
                  AND dor.id_outlet = u.id_outlet
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
                  AND dor.id_outlet = u.id_outlet
                  AND DATE(dor.report_date) = DATE(r.created_at);
            END
        ');

        DB::unprepared('
            CREATE TRIGGER invalidate_reports_on_expense_change
            AFTER UPDATE ON other_expenses
            FOR EACH ROW
            BEGIN
                UPDATE daily_outlet_reports dor
                INNER JOIN users u ON u.id = NEW.id_staff
                INNER JOIN daily_outlet_report_expenses dore ON dor.id = dore.id_outlet_report
                SET dor.is_validated = FALSE
                WHERE dore.id_expense = NEW.id
                  AND dor.id_outlet = u.id_outlet
                  AND DATE(dor.report_date) = DATE(NEW.created_at)
                  AND (OLD.cost != NEW.cost OR OLD.category != NEW.category);
            END
        ');
    }
};
