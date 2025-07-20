<?php
/**
 * Google Drive Token Generator
 *
 * This script helps you obtain the access token and refresh token
 * needed for Google Drive integration.
 *
 * Usage:
 * 1. Run: php get_google_tokens.php
 * 2. Visit the authorization URL
 * 3. Copy the authorization code
 * 4. Run: php get_google_tokens.php [authorization_code]
 */

require_once 'vendor/autoload.php';

// Your Google OAuth credentials
$clientId = env('GOOGLE_DRIVE_CLIENT_ID');
$clientSecret = env('GOOGLE_DRIVE_CLIENT_SECRET');
$redirectUri = 'http://localhost:8000/auth/google/callback'; // You can use any valid URL

$client = new Google\Client();
$client->setClientId($clientId);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope('https://www.googleapis.com/auth/drive');
$client->setAccessType('offline');
$client->setPrompt('consent');

if (!isset($argv[1])) {
    // Step 1: Generate authorization URL
    $authUrl = $client->createAuthUrl();

    echo "=== Google Drive Token Generator ===\n\n";
    echo "Step 1: Visit this URL to authorize the application:\n";
    echo $authUrl . "\n\n";
    echo "Step 2: After authorization, you'll be redirected to a URL with a 'code' parameter.\n";
    echo "Copy the code and run: php get_google_tokens.php [code]\n\n";
    echo "Example: php get_google_tokens.php 4/0AX4XfWh...\n";

} else {
    // Step 2: Exchange authorization code for tokens
    $authCode = $argv[1];

    try {
        $client->authenticate($authCode);
        $tokens = $client->getAccessToken();

        echo "=== SUCCESS! ===\n\n";
        echo "Your Google Drive tokens:\n\n";
        echo "GOOGLE_DRIVE_ACCESS_TOKEN=" . $tokens['access_token'] . "\n";
        echo "GOOGLE_DRIVE_REFRESH_TOKEN=" . $tokens['refresh_token'] . "\n\n";

        echo "Add these to your .env file along with:\n";
        echo "GOOGLE_DRIVE_CLIENT_ID=" . $clientId . "\n";
        echo "GOOGLE_DRIVE_CLIENT_SECRET=" . $clientSecret . "\n";
        echo "GOOGLE_DRIVE_FOLDER_ID=19cU-q2z0IFYoTfrKh2lnncJFlVIzth12\n";
        echo "GOOGLE_DRIVE_EXPIRES_IN=3600\n\n";

        // Test the tokens by making a simple API call
        $service = new Google\Service\Drive($client);
        $results = $service->files->listFiles([
            'q' => "'19cU-q2z0IFYoTfrKh2lnncJFlVIzth12' in parents",
            'pageSize' => 5,
            'fields' => 'files(id,name)'
        ]);

        echo "=== TEST SUCCESSFUL ===\n";
        echo "Found " . count($results->getFiles()) . " files in the Google Drive folder.\n\n";

        if (count($results->getFiles()) > 0) {
            echo "Sample files:\n";
            foreach ($results->getFiles() as $file) {
                echo "- " . $file->getName() . " (ID: " . $file->getId() . ")\n";
            }
        }

    } catch (Exception $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
        echo "Please check your authorization code and try again.\n";
    }
}
?>
