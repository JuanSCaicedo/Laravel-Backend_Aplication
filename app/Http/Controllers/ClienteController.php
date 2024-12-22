<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use App\Services\WhatsAppService;

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
        // Token de meta desde el archivo .env
        $accessToken = env('WHATSAPP_ACCESS_TOKEN');
        $recipientPhone = env('WHATSAPP_RECIPIENT_PHONE'); // Obtener el número de teléfono del archivo .env

        $nombre = $request->input('nombre');
        $apellido = $request->input('apellido');
        $nacimiento = $request->input('nacimiento');
        $edad = $request->input('edad');

        // URL a donde se manda el mensaje
        $url = 'https://graph.facebook.com/v21.0/316855561507121/messages';

        // Configuración del mensaje
        $messageData = [
            'messaging_product' => 'whatsapp',
            'to' => $recipientPhone,
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
                            ['type' => 'text', 'text' => $nombre],
                            ['type' => 'text', 'text' => $apellido],
                            ['type' => 'date_time', 'date_time' => ['fallback_value' => $nacimiento]],
                            ['type' => 'text', 'text' => $edad]
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

        // Ejecutar la solicitud cURL
        $response = curl_exec($ch);

        // Verificar si hay un error en la solicitud
        if (curl_errno($ch)) {
            // Manejar el error
            $error_message = curl_error($ch);
            curl_close($ch);
            return response()->json(['error' => $error_message], 500);
        }

        // Cerrar el recurso cURL
        curl_close($ch);

        // Procesar la respuesta, si es necesario
        $responseData = json_decode($response, true);
        if (isset($responseData['error'])) {
            return response()->json(['error' => $responseData['error']['message']], 500);
        }

        // Crear el cliente con los datos del formulario
        Cliente::create($request->all());

        // Responder con un mensaje de éxito
        return response()->json(['message' => 'Mensaje enviado y cliente creado con éxito'], 200);
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
