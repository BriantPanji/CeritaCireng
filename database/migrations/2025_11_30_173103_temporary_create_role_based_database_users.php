<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * TEMPORARY: All users get ALL PRIVILEGES until proper role permissions are defined
     */
    public function up(): void
    {
        $database = env('DB_DATABASE_DEV', 'cirengdb');

        // Create Owner User
        DB::statement("CREATE USER IF NOT EXISTS 'owner_user'@'localhost' IDENTIFIED BY 'secret123'");
        DB::statement("GRANT ALL PRIVILEGES ON {$database}.* TO 'owner_user'@'localhost' WITH GRANT OPTION");

        // Create Admin User
        DB::statement("CREATE USER IF NOT EXISTS 'admin_user'@'localhost' IDENTIFIED BY 'secret123'");
        DB::statement("GRANT ALL PRIVILEGES ON {$database}.* TO 'admin_user'@'localhost'");

        // Create Inventaris User
        DB::statement("CREATE USER IF NOT EXISTS 'inventaris_user'@'localhost' IDENTIFIED BY 'secret123'");
        DB::statement("GRANT ALL PRIVILEGES ON {$database}.* TO 'inventaris_user'@'localhost'");

        // Create Kurir User
        DB::statement("CREATE USER IF NOT EXISTS 'kurir_user'@'localhost' IDENTIFIED BY 'secret123'");
        DB::statement("GRANT ALL PRIVILEGES ON {$database}.* TO 'kurir_user'@'localhost'");

        // Create Staff User
        DB::statement("CREATE USER IF NOT EXISTS 'staff_user'@'localhost' IDENTIFIED BY 'secret123'");
        DB::statement("GRANT ALL PRIVILEGES ON {$database}.* TO 'staff_user'@'localhost'");

        // Create Guest User
        DB::statement("CREATE USER IF NOT EXISTS 'guest_user'@'localhost' IDENTIFIED BY 'secret123'");
        DB::statement("GRANT ALL PRIVILEGES ON {$database}.* TO 'guest_user'@'localhost'");

        // Flush privileges to apply changes
        DB::statement("FLUSH PRIVILEGES");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop users in reverse order
        DB::statement("DROP USER IF EXISTS 'guest_user'@'localhost'");
        DB::statement("DROP USER IF EXISTS 'staff_user'@'localhost'");
        DB::statement("DROP USER IF EXISTS 'kurir_user'@'localhost'");
        DB::statement("DROP USER IF EXISTS 'inventaris_user'@'localhost'");
        DB::statement("DROP USER IF EXISTS 'admin_user'@'localhost'");
        DB::statement("DROP USER IF EXISTS 'owner_user'@'localhost'");

        DB::statement("FLUSH PRIVILEGES");
    }
};
