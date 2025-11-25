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
        DB::statement("
            CREATE VIEW ccv_user_details AS
            SELECT 
                u.id AS user_id,
                u.display_name,
                u.username,
                u.phone,
                u.status,
                u.role_id,
                r.name AS role_name,
                u.outlet_id,
                o.name AS outlet_name,
                u.created_at,

                (SELECT COUNT(*) FROM attendances a WHERE a.id_user = u.id AND a.status = 'HADIR') AS total_hadir,
                (SELECT COUNT(*) FROM attendances a WHERE a.id_user = u.id AND a.status = 'IZIN') AS total_izin,
                (SELECT COUNT(*) FROM attendances a WHERE a.id_user = u.id AND a.status = 'SAKIT') AS total_sakit,
                (SELECT COUNT(*) FROM attendances a WHERE a.id_user = u.id AND a.status = 'ABSEN') AS total_absen,

                (SELECT COUNT(*) FROM deliveries d WHERE d.id_kurir = u.id) AS delivery_as_kurir,

                (SELECT COUNT(*) FROM deliveries d WHERE d.id_inventaris = u.id) AS delivery_as_inventaris,

                (SELECT COUNT(*) FROM delivery_mistakes dm WHERE dm.id_staff = u.id) AS mistakes_reported,

                (SELECT COUNT(*) FROM delivery_mistake_confirmations dc WHERE dc.id_inventaris = u.id) AS mistakes_confirmed,

                (SELECT COUNT(*) FROM other_expenses oe WHERE oe.id_staff = u.id) AS total_other_expenses

            FROM users u
            LEFT JOIN roles r ON r.id = u.role_id
            LEFT JOIN outlets o ON o.id = u.outlet_id;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS user_details_view");
    }
};
