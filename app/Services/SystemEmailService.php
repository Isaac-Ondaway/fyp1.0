<?php

namespace App\Services;

use Google\Client;
use Google\Service\Gmail;

class SystemEmailService
{
    private $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuthConfig(storage_path('app/google/credentials.json'));
        $this->client->addScope(Gmail::GMAIL_SEND);

        $tokenPath = storage_path('app/google/token.json'); // Token for the system email
        if (!file_exists($tokenPath)) {
            throw new \Exception('Token for system email not found. Run `php artisan google:generate-token` to authenticate.');
        }

        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $this->client->setAccessToken($accessToken);

        if ($this->client->isAccessTokenExpired()) {
            if (!$this->client->getRefreshToken()) {
                throw new \Exception('Refresh token is missing. Re-authenticate to generate a new token.');
            }
            $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));
        }
        
    }

    public function sendEmail($to, $subject, $body)
    {
        $gmail = new Gmail($this->client);
        $message = new \Google\Service\Gmail\Message();

        $rawMessage = "From: umsacademichub@gmail.com\r\n";
        $rawMessage .= "To: {$to}\r\n";
        $rawMessage .= "Subject: {$subject}\r\n\r\n";
        $rawMessage .= "{$body}";

        $encodedMessage = base64_encode($rawMessage);
        $encodedMessage = str_replace(['+', '/', '='], ['-', '_', ''], $encodedMessage);

        $message->setRaw($encodedMessage);

        return $gmail->users_messages->send('me', $message);
    }
}
