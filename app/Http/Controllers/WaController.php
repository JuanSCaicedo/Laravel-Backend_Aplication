<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WaController extends Controller
{
    public function envia()
    {
        //Token de meta
        $accessToken = 'EAAaDCZChZBmcgBOZCIL4nAdMvgu9dPfQZC51gtN5dSw2NXE0cUjj4me9nl8IeX0G5AsH2KyP1YPZAdJyHpuJR1oKprzuRUQl8xFEawJScBZBMHITfpE7Y2505vkNxq4Nx8gan1GAVjwCXGTFSOwFBQ05UYd3gXoMJ15f1PtO3RVg8LCjjSSW8YICde23117cMZC';
        $recipientPhone = '573002474532'; // Replace with the recipient's phone number in international format
        $nombre = 'Juan Sebastian';
        $apellido = 'Caicedo Mambuscay';
        $nacimiento = 'OCTUBRE 15, 1998';
        $edad = '25';


        //Url a donde se manda el mensaje
        $url = 'https://graph.facebook.com/v19.0/322693020921363/messages';

        // Configuración del mensaje
        $messageData = [
            'messaging_product' => 'whatsapp',
            'to' => $recipientPhone, // Aquí colocas la variable $recipientPhone si es necesario
            'type' => 'template',
            'template' => [
                'name' => 'clientes_creados',
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
