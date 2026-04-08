<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Administrar Lotes') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.lots.import.form') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Importar Excel
                </a>
                <a href="{{ route('admin.lots.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Nuevo Lote
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{
        updateState(lotId, newState) {
            window.fetchApi(`/admin/lots/${lotId}/state`, {
                method: 'PUT',
                body: JSON.stringify({ estado: newState })
            }).then(res => {
                alert(res.message);
                window.location.reload();
            }).catch(err => {
                alert(err.message || 'Error actualizando estado');
            });
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <!-- Filters -->
                <div class="p-4 border-b border-gray-200 bg-gray-50 flex space-x-4">
                    <form method="GET" action="{{ route('admin.lots.index') }}" class="flex space-x-4 items-end">
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Manzana</label>
                            <select name="manzana" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                <option value="">Todas</option>
                                @foreach($manzanas as $m)
                                    <option value="{{ $m }}" {{ request('manzana') == $m ? 'selected' : '' }}>Manzana {{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Estado</label>
                            <select name="estado" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                <option value="">Todos</option>
                                @foreach(\App\Models\Lot::ESTADO_LABELS as $key => $label)
                                    <option value="{{ $key }}" {{ request('estado') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none">
                                Filtrar
                            </button>
                            <a href="{{ route('admin.lots.index') }}" class="ml-2 text-sm text-indigo-600 hover:text-indigo-900">Limpiar</a>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mza/Lote</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Detalles</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($lots as $lot)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900">Mza {{ $lot->manzana }}</div>
                                        <div class="text-sm text-gray-500">Lote {{ $lot->nro_lote }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ number_format($lot->superficie, 2) }} m&sup2; - USD {{ number_format($lot->precio, 0) }}</div>
                                        <div class="text-xs text-gray-500">{{ $lot->zona }} {{ $lot->observaciones ? '| '.$lot->observaciones : '' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <select @change="updateState({{ $lot->id }}, $event.target.value)" class="text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" style="background-color: {{ $lot->color }}; color: {{ in_array($lot->estado, ['reservado','vendido','no_disponible']) ? 'white' : 'black' }};">
                                            @foreach(\App\Models\Lot::ESTADO_LABELS as $key => $label)
                                                <option value="{{ $key }}" {{ $lot->estado === $key ? 'selected' : '' }} style="background-color: white; color: black;">
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.lots.edit', $lot) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>
                                        @if(in_array($lot->estado, ['disponible', 'oculto', 'no_disponible']))
                                            <form action="{{ route('admin.lots.destroy', $lot) }}" method="POST" class="inline" onsubmit="return confirm('¿Seguro quieres eliminar este lote?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-200">
                    {{ $lots->links() }}
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        window.fetchApi = async (url, options = {}) => {
            options.headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                ...options.headers
            };
            const res = await fetch(url, options);
            const data = await res.json();
            if (!res.ok) throw data;
            return data;
        };
    </script>
    @endpush
</x-app-layout>
