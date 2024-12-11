<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Google\Client;

class GenerateGoogleToken extends Command
{
    protected $signature = 'google:generate-token';
    protected $description = 'Generate Google OAuth Token for System Email';

    public function handle()
    {
        $client = new Client();
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->setScopes([\Google\Service\Gmail::GMAIL_SEND]);
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        $authUrl = $client->createAuthUrl();
        $this->info("Open this URL in your browser to authenticate: \n$authUrl");

        $authCode = $this->ask('Enter the authorization code here');
        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

        $tokenPath = storage_path('app/google/token.json');
        file_put_contents($tokenPath, json_encode($accessToken));
        $this->info("Access token saved to: $tokenPath");
    }
}
