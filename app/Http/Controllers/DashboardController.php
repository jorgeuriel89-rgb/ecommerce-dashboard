<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'por_enviar');

        $pedidos = match($tab) {
            'retrasados' => Pedido::retrasados()->with(['cliente', 'productos'])->paginate(15),
            'entregados' => Pedido::entregados()->with(['cliente', 'productos'])->paginate(15),
            'cancelados' => Pedido::cancelados()->with(['cliente', 'productos'])->paginate(15),
            default => Pedido::porEnviar()->with(['cliente', 'productos'])->paginate(15),
        };

        return view('dashboard', compact('pedidos', 'tab'));
    }
}