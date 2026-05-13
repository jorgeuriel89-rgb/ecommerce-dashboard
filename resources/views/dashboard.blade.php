@extends('layouts.app')

@section('content')
    <div class="mb-6 flex gap-4">
        <a href="?tab=por_enviar"
           class="px-4 py-2 rounded-lg text-sm font-medium {{ $tab === 'por_enviar' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
            Por Enviar
        </a>
        <a href="?tab=retrasados"
           class="px-4 py-2 rounded-lg text-sm font-medium {{ $tab === 'retrasados' ? 'bg-red-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
            Retrasados
        </a>
        <a href="?tab=entregados"
           class="px-4 py-2 rounded-lg text-sm font-medium {{ $tab === 'entregados' ? 'bg-green-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
            Entregados
        </a>
        <a href="?tab=cancelados"
           class="px-4 py-2 rounded-lg text-sm font-medium {{ $tab === 'cancelados' ? 'bg-gray-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
            Cancelados
        </a>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3">#</th>
                    <th class="px-6 py-3">Cliente</th>
                    <th class="px-6 py-3">Total</th>
                    <th class="px-6 py-3">Fecha Entrega</th>
                    <th class="px-6 py-3">Productos</th>
                    <th class="px-6 py-3">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pedidos as $pedido)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-gray-500">{{ $pedido->id }}</td>
                        <td class="px-6 py-4 font-medium text-gray-800">{{ $pedido->cliente->nombre }}</td>
                        <td class="px-6 py-4 text-gray-700">${{ number_format($pedido->total, 2) }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $pedido->fecha_entrega }}</td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @foreach($pedido->productos as $producto)
                                    <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full">
                                        {{ $producto->nombre }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                {{ $pedido->estado === 'pendiente' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $pedido->estado === 'entregado' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $pedido->estado === 'cancelado' ? 'bg-red-100 text-red-700' : '' }}">
                                {{ ucfirst($pedido->estado) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            No hay pedidos en esta categoría.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-6 py-4 border-t">
            {{ $pedidos->appends(['tab' => $tab])->links() }}
        </div>
    </div>
@endsection