<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class TempDirectoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * This service provider ensures that the Livewire temporary upload directory exists
     * and is writable. This fixes issues on Windows where PHP's default upload_tmp_dir
     * may not be configured properly.
     */
    public function boot(): void
    {
        $this->ensurePhpTempDirectoryExists();
        $this->ensureLivewireTempDirectoryExists();
    }

    /**
     * Ensure the PHP upload temp directory exists.
     * This is particularly important on Windows where the default temp directory may not exist.
     */
    protected function ensurePhpTempDirectoryExists(): void
    {
        $phpTmpDir = storage_path('app/private/tmp');

        if (!is_dir($phpTmpDir)) {
            $created = @mkdir($phpTmpDir, 0755, true);

            if (!$created && !is_dir($phpTmpDir)) {
                Log::warning("TempDirectoryServiceProvider: Failed to create PHP temp directory: {$phpTmpDir}");
            }
        }
    }

    /**
     * Ensure the Livewire temporary upload directory exists and is writable.
     */
    protected function ensureLivewireTempDirectoryExists(): void
    {
        $disk = config('livewire.temporary_file_upload.disk', config('filesystems.default'));
        $directory = config('livewire.temporary_file_upload.directory', 'livewire-tmp');

        $diskConfig = config("filesystems.disks.{$disk}");

        if (isset($diskConfig['root']) && isset($diskConfig['driver']) && $diskConfig['driver'] === 'local') {
            $fullPath = $diskConfig['root'] . DIRECTORY_SEPARATOR . $directory;

            if (!is_dir($fullPath)) {
                $created = @mkdir($fullPath, 0755, true);

                if (!$created && !is_dir($fullPath)) {
                    Log::warning("TempDirectoryServiceProvider: Failed to create Livewire temp directory: {$fullPath}");
                }
            }
        }
    }
}
