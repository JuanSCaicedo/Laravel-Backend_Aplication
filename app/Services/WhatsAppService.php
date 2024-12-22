<?php

namespace App\Services;

class WhatsAppService
{
    private string $accessToken;
    private string $apiVersion;
    private string $accountId;
    private string $baseUrl;

    public function __construct()
    {
        $this->accessToken = config('services.whatsapp.access_token');
        $this->apiVersion = config('services.whatsapp.api_version');
        $this->accountId = config('services.whatsapp.business_account_id');
        $this->baseUrl = "https://graph.facebook.com/{$this->apiVersion}/{$this->accountId}/messages";
    }

    public function sendTemplateMessage(string $to, string $template, array $parameters)
    {
        $messageData = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => $template,
                'language' => [
                    'code' => 'es'
                ],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => $parameters
                    ]
                ]
            ]
        ];

        return $this->sendRequest($messageData);
    }

    private function sendRequest(array $data)
    {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$this->accessToken}",
                "Content-Type: application/json"
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_RETURNTRANSFER => true
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new \Exception("Error al enviar mensaje de WhatsApp: $error");
        }

        return json_decode($response, true);
    }
}