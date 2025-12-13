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
        // 1. Trigger for inventory stock changes
        DB::unprepared('
            CREATE TRIGGER ccl_log_inventory_changes
            AFTER UPDATE ON inventory
            FOR EACH ROW
            BEGIN
                IF OLD.stock != NEW.stock THEN
                    INSERT INTO inventory_change_log (
                        id_item,
                        old_stock,
                        new_stock,
                        change_amount,
                        timestamp
                    ) VALUES (
                        NEW.id_item,
                        OLD.stock,
                        NEW.stock,
                        NEW.stock - OLD.stock,
                        NOW()
                    );
                END IF;
            END
        ');

        // 2. Trigger for delivery status changes
        DB::unprepared('
            CREATE TRIGGER ccl_log_delivery_status_change
            AFTER UPDATE ON deliveries
            FOR EACH ROW
            BEGIN
                IF OLD.status != NEW.status THEN
                    INSERT INTO delivery_status_audit (
                        id_delivery,
                        old_status,
                        new_status,
                        timestamp
                    ) VALUES (
                        NEW.id,
                        OLD.status,
                        NEW.status,
                        NOW()
                    );
                END IF;
            END
        ');

        // 3. Trigger for item creation
        DB::unprepared('
            CREATE TRIGGER ccl_log_item_insert
            AFTER INSERT ON items
            FOR EACH ROW
            BEGIN
                INSERT INTO item_change_log (
                    id_item,
                    action,
                    field_changed,
                    old_value,
                    new_value,
                    timestamp
                ) VALUES (
                    NEW.id,
                    "CREATE",
                    NULL,
                    NULL,
                    CONCAT("name:", NEW.name, "|cost:", NEW.cost, "|unit:", NEW.unit, "|type:", NEW.type),
                    NOW()
                );
            END
        ');

        // 4. Trigger for item updates
        DB::unprepared('
            CREATE TRIGGER ccl_log_item_update
            AFTER UPDATE ON items
            FOR EACH ROW
            BEGIN
                -- Log name change
                IF OLD.name != NEW.name THEN
                    INSERT INTO item_change_log (id_item, action, field_changed, old_value, new_value, timestamp)
                    VALUES (NEW.id, "UPDATE", "name", OLD.name, NEW.name, NOW());
                END IF;

                -- Log cost change
                IF OLD.cost != NEW.cost THEN
                    INSERT INTO item_change_log (id_item, action, field_changed, old_value, new_value, timestamp)
                    VALUES (NEW.id, "UPDATE", "cost", CAST(OLD.cost AS CHAR), CAST(NEW.cost AS CHAR), NOW());
                END IF;

                -- Log unit change
                IF OLD.unit != NEW.unit THEN
                    INSERT INTO item_change_log (id_item, action, field_changed, old_value, new_value, timestamp)
                    VALUES (NEW.id, "UPDATE", "unit", OLD.unit, NEW.unit, NOW());
                END IF;

                -- Log type change
                IF OLD.type != NEW.type THEN
                    INSERT INTO item_change_log (id_item, action, field_changed, old_value, new_value, timestamp)
                    VALUES (NEW.id, "UPDATE", "type", OLD.type, NEW.type, NOW());
                END IF;

                -- Log soft delete
                IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
                    INSERT INTO item_change_log (id_item, action, field_changed, old_value, new_value, timestamp)
                    VALUES (NEW.id, "DELETE", "deleted_at", NULL, CAST(NEW.deleted_at AS CHAR), NOW());
                END IF;

                -- Log restore
                IF OLD.deleted_at IS NOT NULL AND NEW.deleted_at IS NULL THEN
                    INSERT INTO item_change_log (id_item, action, field_changed, old_value, new_value, timestamp)
                    VALUES (NEW.id, "RESTORE", "deleted_at", CAST(OLD.deleted_at AS CHAR), NULL, NOW());
                END IF;
            END
        ');

        // 5. Trigger for daily report updates
        DB::unprepared('
            CREATE TRIGGER ccl_log_daily_report_update
            AFTER UPDATE ON daily_outlet_reports
            FOR EACH ROW
            BEGIN
                DECLARE v_action VARCHAR(32);

                -- Determine action type based on validation status change
                IF OLD.is_validated = FALSE AND NEW.is_validated = TRUE THEN
                    SET v_action = "VALIDATED";
                ELSEIF OLD.is_validated = TRUE AND NEW.is_validated = FALSE THEN
                    SET v_action = "INVALIDATED";
                ELSE
                    SET v_action = "UPDATED";
                END IF;

                INSERT INTO daily_report_log (
                    id_report,
                    action,
                    old_is_validated,
                    new_is_validated,
                    timestamp
                ) VALUES (
                    NEW.id,
                    v_action,
                    OLD.is_validated,
                    NEW.is_validated,
                    NOW()
                );
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS ccl_log_inventory_changes');
        DB::unprepared('DROP TRIGGER IF EXISTS ccl_log_delivery_status_change');
        DB::unprepared('DROP TRIGGER IF EXISTS ccl_log_item_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS ccl_log_item_update');
        DB::unprepared('DROP TRIGGER IF EXISTS ccl_log_daily_report_update');
    }
};
