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

    private WhatsAppService $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    public function store(Request $request)
    {
        try {
            // Crear el cliente primero
            $cliente = Cliente::create($request->all());

            // Preparar los parámetros para la plantilla
            $parameters = [
                ['type' => 'text', 'text' => $request->input('nombre')],
                ['type' => 'text', 'text' => $request->input('apellido')],
                ['type' => 'date_time', 'date_time' => ['fallback_value' => $request->input('nacimiento')]],
                ['type' => 'text', 'text' => $request->input('edad')]
            ];

            // Enviar mensaje de WhatsApp
            $this->whatsAppService->sendTemplateMessage(
                config('services.whatsapp.recipient_phone'),
                'nuevo_registro',
                $parameters
            );

            return response()->json($cliente, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el cliente',
                'error' => $e->getMessage()
            ], 500);
        }
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
