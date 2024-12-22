<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('buscar', '');
        
        $clientes = Cliente::query()
            ->where('nombre', 'LIKE', "%{$query}%")
            // o cualquier otra condición de búsqueda
            ->paginate(10);
        
        return response()->json($clientes);
    }

    public function store(Request $request)
    {

        //Token de meta
        $accessToken = 'EAAaDCZChZBmcgBOZCIL4nAdMvgu9dPfQZC51gtN5dSw2NXE0cUjj4me9nl8IeX0G5AsH2KyP1YPZAdJyHpuJR1oKprzuRUQl8xFEawJScBZBMHITfpE7Y2505vkNxq4Nx8gan1GAVjwCXGTFSOwFBQ05UYd3gXoMJ15f1PtO3RVg8LCjjSSW8YICde23117cMZC';
        $recipientPhone = '573002474532'; // Reemplazar con el número de teléfono del destinatario en formato internacional
        $nombre = $request->input('nombre');
        $apellido = $request->input('apellido');
        $nacimiento = $request->input('nacimiento');
        $edad = $request->input('edad');

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

        // Crear un recurso cURL
        $ch = curl_init();

        // Establecer la URL de la solicitud
        curl_setopt($ch, CURLOPT_URL, $url);

        // Establecer el método de la solicitud a POST
        curl_setopt($ch, CURLOPT_POST, true);

        // Establecer los encabezados de la solicitud
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$accessToken}",
            "Content-Type: application/json"
        ]);

        // Establecer el cuerpo de la solicitud con los datos del mensaje codificados en JSON
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($messageData));

        // Opcionalmente, capturar la respuesta del servidor
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Opcionalmente, manejar posibles errores
        // curl_setopt($ch, CURLOPT_VERBOSE, true); // Habilitar la salida detallada para depuración
        curl_setopt($ch, CURLOPT_STDERR, fopen('php://stderr', 'w')); // Redireccionar la salida de error a un archivo

        // Ejecutar la solicitud cURL
        $response = curl_exec($ch);

        // Cerrar el recurso cURL
        curl_close($ch);

        return Cliente::create($request->all());
    }

    public function update(Request $request, string $id)
    {
        $clien = Cliente::findOrFail($id);
        $clien->nombre = $request->nombre;
        $clien->apellido = $request->apellido;
        $clien->nacimiento = $request->nacimiento;
        $clien->edad = $request->edad;

        $clien->update();
        return $clien;
    }

    public function destroy(string $id)
    {
        $clien = Cliente::findOrFail($id);
        $clien->delete();
    }
}
