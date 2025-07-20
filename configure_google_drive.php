<?php
/**
 * Google Drive Configuration Helper
 * 
 * This script helps configure Google Drive environment variables
 * and generate access tokens safely without exposing credentials in git.
 */

// Check if .env file exists
$envFile = __DIR__ . '/.env';
if (!file_exists($envFile)) {
    echo "ERROR: .env file not found. Please make sure you're in the Laravel root directory.\n";
    exit(1);
}

// Google Drive configuration - credentials should be provided as arguments
if (count($argv) < 4) {
    echo "Usage: php configure_google_drive.php <client_id> <client_secret> <folder_id>\n";
    echo "Example: php configure_google_drive.php 'your_client_id' 'your_client_secret' '19cU-q2z0IFYoTfrKh2lnncJFlVIzth12'\n";
    exit(1);
}

$googleDriveConfig = [
    'GOOGLE_DRIVE_CLIENT_ID' => $argv[1],
    'GOOGLE_DRIVE_CLIENT_SECRET' => $argv[2],
    'GOOGLE_DRIVE_FOLDER_ID' => $argv[3],
    'GOOGLE_DRIVE_EXPIRES_IN' => '3600'
];

// Read current .env content
$envContent = file_get_contents($envFile);

echo "=== Google Drive Configuration ===\n\n";

// Check if Google Drive variables already exist
$hasGoogleDriveConfig = false;
foreach ($googleDriveConfig as $key => $value) {
    if (strpos($envContent, $key) !== false) {
        $hasGoogleDriveConfig = true;
        break;
    }
}

if ($hasGoogleDriveConfig) {
    echo "Google Drive configuration already exists in .env file.\n";
    echo "Current configuration:\n\n";
    
    $lines = explode("\n", $envContent);
    foreach ($lines as $line) {
        if (strpos($line, 'GOOGLE_DRIVE_') === 0) {
            echo $line . "\n";
        }
    }
    
    echo "\nDo you want to update the configuration? (y/n): ";
    $handle = fopen("php://stdin", "r");
    $input = trim(fgets($handle));
    fclose($handle);
    
    if (strtolower($input) !== 'y') {
        echo "Configuration unchanged.\n";
        exit(0);
    }
}

// Add or update Google Drive configuration
echo "Adding Google Drive configuration to .env file...\n";

// Remove existing Google Drive configuration
$lines = explode("\n", $envContent);
$filteredLines = [];
foreach ($lines as $line) {
    if (strpos($line, 'GOOGLE_DRIVE_') !== 0) {
        $filteredLines[] = $line;
    }
}

// Add Google Drive configuration section
$filteredLines[] = '';
$filteredLines[] = '# Google Drive Configuration';
foreach ($googleDriveConfig as $key => $value) {
    $filteredLines[] = $key . '=' . $value;
}

// Add placeholders for tokens (to be filled manually)
$filteredLines[] = 'GOOGLE_DRIVE_ACCESS_TOKEN=';
$filteredLines[] = 'GOOGLE_DRIVE_REFRESH_TOKEN=';

// Write back to .env file
$newEnvContent = implode("\n", $filteredLines);
file_put_contents($envFile, $newEnvContent);

echo "✅ Google Drive configuration added to .env file!\n\n";

echo "=== Next Steps ===\n";
echo "1. You still need to obtain ACCESS_TOKEN and REFRESH_TOKEN\n";
echo "2. Visit this URL to authorize the application:\n\n";

// Generate authorization URL
require_once 'vendor/autoload.php';

$client = new Google\Client();
$client->setClientId($googleDriveConfig['GOOGLE_DRIVE_CLIENT_ID']);
$client->setClientSecret($googleDriveConfig['GOOGLE_DRIVE_CLIENT_SECRET']);
$client->setRedirectUri('http://localhost:8000/auth/google/callback');
$client->addScope('https://www.googleapis.com/auth/drive');
$client->setAccessType('offline');
$client->setPrompt('consent');

$authUrl = $client->createAuthUrl();
echo $authUrl . "\n\n";

echo "3. After authorization, you'll get a code. Run:\n";
echo "   php configure_google_drive.php '" . $argv[1] . "' '" . $argv[2] . "' '" . $argv[3] . "' [authorization_code]\n\n";

echo "4. The tokens will be automatically added to your .env file\n\n";

// If authorization code is provided as 5th argument, exchange it for tokens
if (isset($argv[4])) {
    echo "=== Exchanging authorization code for tokens ===\n";
    
    try {
        $client->authenticate($argv[4]);
        $tokens = $client->getAccessToken();
        
        // Update .env file with tokens
        $envContent = file_get_contents($envFile);
        $envContent = str_replace('GOOGLE_DRIVE_ACCESS_TOKEN=', 'GOOGLE_DRIVE_ACCESS_TOKEN=' . $tokens['access_token'], $envContent);
        $envContent = str_replace('GOOGLE_DRIVE_REFRESH_TOKEN=', 'GOOGLE_DRIVE_REFRESH_TOKEN=' . $tokens['refresh_token'], $envContent);
        
        file_put_contents($envFile, $envContent);
        
        echo "✅ Tokens successfully added to .env file!\n\n";
        
        // Test the configuration
        echo "=== Testing Google Drive Connection ===\n";
        $service = new Google\Service\Drive($client);
        $results = $service->files->listFiles([
            'q' => "'" . $googleDriveConfig['GOOGLE_DRIVE_FOLDER_ID'] . "' in parents",
            'pageSize' => 5,
            'fields' => 'files(id,name)'
        ]);
        
        echo "✅ Connection successful!\n";
        echo "Found " . count($results->getFiles()) . " files in the Google Drive folder.\n\n";
        
        if (count($results->getFiles()) > 0) {
            echo "Sample files:\n";
            foreach ($results->getFiles() as $file) {
                echo "- " . $file->getName() . "\n";
            }
        }
        
        echo "\n🎉 Google Drive integration is now fully configured!\n";
        echo "You can now visit: http://localhost:8000/google-drive\n";
        
    } catch (Exception $e) {
        echo "❌ ERROR: " . $e->getMessage() . "\n";
        echo "Please check your authorization code and try again.\n";
    }
}
?>
