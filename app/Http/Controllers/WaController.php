<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WaController extends Controller
{
    public function envia()
    {
        //Token de meta
        $accessToken = 'WHATSAPP_ACCESS_TOKEN';
        $recipientPhone = 'WHATSAPP_RECIPIENT_PHONE'; // Replace with the recipient's phone number in international format
        $nombre = 'Juan Sebastian';
        $apellido = 'Caicedo Mambuscay';
        $nacimiento = 'OCTUBRE 15, 2020';
        $edad = '16';


        //Url a donde se manda el mensaje
        $url = 'https://graph.facebook.com/v21.0/316855561507121/messages';

        // Configuración del mensaje
        $messageData = [
            'messaging_product' => 'whatsapp',
            'to' => $recipientPhone, // Aquí colocas la variable $recipientPhone si es necesario
            'type' => 'template',
            'template' => [
                'name' => 'nuevo_registro',
                'language' => [
                    'code' => 'es'
                ],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $nombre
                            ],
                            [
                                'type' => 'text',
                                'text' => $apellido
                            ],
                            [
                                'type' => 'date_time',
                                'date_time' => [
                                    'fallback_value' => $nacimiento
                                ]
                            ],
                            [
                                'type' => 'text',
                                'text' => $edad
                            ]
                        ]
                    ]
                ]
            ]
        ];

        // Create a cURL resource
        $ch = curl_init();

        // Set the request URL
        curl_setopt($ch, CURLOPT_URL, $url);

        // Set the request method to POST
        curl_setopt($ch, CURLOPT_POST, true);

        // Set the request headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$accessToken}",
            "Content-Type: application/json"
        ]);

        // Set the request body with JSON-encoded message data
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($messageData));

        // Optionally, capture the server response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Optionally, handle potential errors
        // curl_setopt($ch, CURLOPT_VERBOSE, true); // Enable verbose output for debugging
        curl_setopt($ch, CURLOPT_STDERR, fopen('php://stderr', 'w')); // Redirect error output to a file

        // Execute the cURL request
        $response = curl_exec($ch);

        // Close the cURL resource
        curl_close($ch);

        if ($response === false) {
            // Handle cURL errors (e.g., network issues)
            $error = curl_error($ch);
            echo "cURL Error: $error\n";
            exit(1);
        } else {
            // Process the server response (check for success or error codes)
            $responseData = json_decode($response, true);
            if (isset($responseData['error'])) {
                // Handle Facebook Graph API errors
                echo "Facebook Graph API Error: " . $responseData['error']['message'] . "\n";
            } else {
                // Success! Message sent or scheduled (process response)
                echo "Mensaje enviado correctamente!\n";
            }
        }
    }
}
