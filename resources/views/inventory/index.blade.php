<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inventario de Lotes: Bariloche del Este') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{
        q_manzana: '',
        q_nro: '',
        q_estado: [],
        lots: [],
        showModal: false,
        loadingData: false,
        selectedLot: null,
        userRole: '{{ Auth::user()->role }}',
        isAdmin: {{ Auth::user()->isAdmin() ? 'true' : 'false' }},
        isVendedor: {{ Auth::user()->isVendedor() ? 'true' : 'false' }},
        isSupervisor: {{ Auth::user()->isSupervisor() ? 'true' : 'false' }},

        openModal(id) {
            this.showModal = true;
            this.loadingData = true;
            fetch('/inventory/' + id)
                .then(res => res.json())
                .then(data => {
                    this.selectedLot = data;
                    this.loadingData = false;
                })
                .catch(err => {
                    alert('Error cargando datos del lote.');
                    this.showModal = false;
                    this.loadingData = false;
                });
        },
        toggleFilter(list, value) {
            if (this[list].includes(value)) {
                this[list] = this[list].filter(item => item !== value);
            } else {
                this[list].push(value);
            }
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex flex-col md:flex-row">

                <!-- Sidebar / Filters -->
                <div class="w-full md:w-64 bg-gray-50 p-4 border-r border-gray-200 shrink-0">
                    <h3 class="font-bold text-gray-700 mb-4 uppercase text-sm tracking-wider">Filtros Rápidos</h3>

                    <!-- Manzana Filter -->
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Manzana</label>
                        <select x-model="q_manzana" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                            <option value="">Todas</option>
                            @foreach($manzanas as $m)
                                <option value="{{ $m }}">Manzana {{ $m }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Nro Lote Filter -->
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Número de Lote</label>
                        <input type="text" x-model="q_nro" placeholder="Ej: 05" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                    </div>

                    <!-- Estado Filter -->
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                        <div class="space-y-2">
                            @foreach($estados as $key => $label)
                                <label class="flex items-center text-sm cursor-pointer">
                                    <input type="checkbox"
                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 mr-2"
                                           @change="toggleFilter('q_estado', '{{ $key }}')"
                                           :checked="q_estado.includes('{{ $key }}')">
                                    <span class="flex items-center">
                                        <span class="w-3 h-3 rounded-full mr-2" style="background-color: {{ \App\Models\Lot::ESTADO_COLORS[$key] }}"></span>
                                        {{ $label }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-8 border-t pt-4">
                        <button @click="q_manzana=''; q_nro=''; q_estado=[];" class="w-full sm:w-auto text-xs text-indigo-600 hover:text-indigo-900 font-medium">
                            Limpiar Filtros
                        </button>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="flex-1 w-full overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lote</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Superficie</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Zona</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Acciones</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($lots as $lot)
                                <tr class="hover:bg-gray-50 transition-colors"
                                    x-show="
                                        (q_manzana === '' || '{{ $lot->manzana }}' === q_manzana) &&
                                        (q_nro === '' || '{{ $lot->nro_lote }}'.includes(q_nro)) &&
                                        (q_estado.length === 0 || q_estado.includes('{{ $lot->estado }}'))
                                    ">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-gray-900">Mza {{ $lot->manzana }}</div>
                                            <div class="text-sm text-gray-500 ml-2">Lote {{ $lot->nro_lote }}</div>
                                        </div>
                                        @if($lot->observaciones)
                                            <div class="text-xs text-gray-400 mt-1">{{ $lot->observaciones }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                        <div class="text-sm text-gray-900">{{ number_format($lot->superficie, 2) }} m&sup2;</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                        <div class="text-sm text-gray-500">{{ $lot->zona ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white"
                                              style="background-color: {{ $lot->color }}">
                                            <span class="hidden sm:inline">{{ $lot->label }}</span>
                                            <span class="sm:hidden">{{ $lot->short_label }}</span>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button @click="openModal({{ $lot->id }})" class="text-indigo-600 hover:text-indigo-900 font-bold bg-indigo-50 hover:bg-indigo-100 rounded-md px-3 py-1 transition-colors">
                                            Ver Ficha
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if($lots->isEmpty())
                        <div class="text-center py-10 text-gray-500">
                            No hay lotes en el inventario.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Render the modal component -->
        @include('inventory.partials.lot-modal')

    </div>

    @push('scripts')
        <script>
            // CSRF helper for frontend fetches
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

            // Notificaciones Toast Helper
            function showToast(message, isError = false) {
                // (opcional) implementar toast
                alert(message);
            }
        </script>
    @endpush
</x-app-layout>
