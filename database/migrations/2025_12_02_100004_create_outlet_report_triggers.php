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
        // Trigger 1: Invalidasi saat delivery items berubah
        DB::unprepared('
            CREATE TRIGGER invalidate_reports_on_delivery_change
            AFTER UPDATE ON delivery_items
            FOR EACH ROW
            BEGIN
                UPDATE daily_outlet_reports dor
                INNER JOIN deliveries d ON d.id_outlet = dor.id_outlet
                INNER JOIN daily_outlet_report_items dori ON dor.id = dori.id_outlet_report
                SET dor.is_validated = FALSE
                WHERE dori.id_item = NEW.id_item
                  AND DATE(dor.report_date) = DATE(d.assigned_at)
                  AND OLD.quantity != NEW.quantity;
            END
        ');

        // Trigger 2: Invalidasi saat delivery items dihapus
        DB::unprepared('
            CREATE TRIGGER invalidate_reports_on_delivery_delete
            AFTER DELETE ON delivery_items
            FOR EACH ROW
            BEGIN
                UPDATE daily_outlet_reports dor
                INNER JOIN deliveries d ON d.id = OLD.id_delivery
                INNER JOIN daily_outlet_report_items dori ON dor.id = dori.id_outlet_report
                SET dor.is_validated = FALSE
                WHERE dori.id_item = OLD.id_item
                  AND dor.id_outlet = d.id_outlet
                  AND DATE(dor.report_date) = DATE(d.assigned_at);
            END
        ');

        // Trigger 3: Invalidasi saat return items berubah
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

        // Trigger 4: Invalidasi saat return items dihapus
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

        // Trigger 5: Invalidasi saat expenses berubah
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

        // Trigger 6: Invalidasi saat expenses dihapus
        DB::unprepared('
            CREATE TRIGGER invalidate_reports_on_expense_delete
            AFTER DELETE ON other_expenses
            FOR EACH ROW
            BEGIN
                UPDATE daily_outlet_reports dor
                INNER JOIN daily_outlet_report_expenses dore ON dor.id = dore.id_outlet_report
                SET dor.is_validated = FALSE
                WHERE dore.id_expense = OLD.id
                  AND DATE(dor.report_date) = DATE(OLD.created_at);
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS invalidate_reports_on_delivery_change');
        DB::unprepared('DROP TRIGGER IF EXISTS invalidate_reports_on_delivery_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS invalidate_reports_on_return_change');
        DB::unprepared('DROP TRIGGER IF EXISTS invalidate_reports_on_return_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS invalidate_reports_on_expense_change');
        DB::unprepared('DROP TRIGGER IF EXISTS invalidate_reports_on_expense_delete');
    }
};
