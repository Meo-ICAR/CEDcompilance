<?php

namespace App\Providers;

use App\Services\GoogleDriveAdapter;
use Google\Client;
use Google\Service\Drive;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;

class GoogleDriveServiceProvider extends ServiceProvider
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
     */
    public function boot(): void
    {
        Storage::extend('google_drive', function ($app, $config) {
            $client = new Client();
            $client->setClientId($config['client_id']);
            $client->setClientSecret($config['client_secret']);
            $client->setAccessToken([
                'access_token' => $config['access_token'],
                'refresh_token' => $config['refresh_token'],
                'expires_in' => $config['expires_in'] ?? 3600,
                'token_type' => 'Bearer',
            ]);

            // Set up the Drive service
            $service = new Drive($client);
            
            // Create a custom adapter for Google Drive
            $adapter = new GoogleDriveAdapter($service, $config['folder_id'] ?? 'root');
            
            return new FilesystemAdapter(
                new Filesystem($adapter, $config),
                $adapter,
                $config
            );
        });
    }
}
