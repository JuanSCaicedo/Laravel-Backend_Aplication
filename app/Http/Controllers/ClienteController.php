<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        // Tu lógica para obtener la lista de clientes
        $clientes = Cliente::all();

        // Agrega las cabeceras CORS manualmente a la respuesta
        return response()->json($clientes)->header('Access-Control-Allow-Origin', '*');
    }

    public function store(Request $request)
    {
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
