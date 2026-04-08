<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Principal') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="dashboardData()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- KPIs -->
            <div class="flex justify-between items-end">
                <h3 class="text-lg font-bold text-gray-700">Métricas de Desempeño</h3>
                <div class="flex items-center space-x-2">
                    <label class="text-sm text-gray-500 font-medium">Fecha:</label>
                    <input type="date" x-model="kpiDate" @change="loadKPIs" class="border-gray-300 rounded-md shadow-sm text-sm p-1">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white overflow-hidden shadow-sm hover:shadow-md transition sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Bloqueos</div>
                        <div class="text-3xl font-bold text-indigo-600" x-text="kpis.blocks_today">0</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm hover:shadow-md transition sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Reservas</div>
                        <div class="text-3xl font-bold text-gray-900" x-text="kpis.reservations_today">0</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm hover:shadow-md transition sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Bloqueos Caídos</div>
                        <div class="text-3xl font-bold text-red-600" x-text="kpis.expired_today">0</div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 1 -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Pie Chart: Inventario -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col items-center">
                    <h3 class="text-lg font-bold text-gray-700 w-full mb-4">Estado del Inventario</h3>
                    <div class="relative h-48 w-full flex justify-center">
                        <canvas id="inventoryChart"></canvas>
                    </div>
                    <div class="mt-4 text-sm text-gray-500 w-full text-center pb-2 border-b border-gray-200">
                        Total Lotes Visibles: <span class="font-bold text-gray-900" x-text="inventoryTotal"></span>
                    </div>
                    <div class="w-full mt-2">
                        <table class="w-full text-sm text-left">
                            <tbody>
                                <template x-for="item in inventoryData" :key="item.label">
                                    <tr class="border-b last:border-b-0 border-gray-100">
                                        <td class="py-2 w-8"><div class="w-4 h-4 rounded shadow-sm" :style="`background-color: ${item.color}`"></div></td>
                                        <td class="py-2 font-medium text-gray-700" x-text="item.label"></td>
                                        <td class="py-2 text-right font-bold text-gray-900" x-text="item.value"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Line Chart: Tendencia -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 md:col-span-2">
                    <h3 class="text-lg font-bold text-gray-700 mb-4">Tendencia (Acumulado desde Inicio)</h3>
                    <div class="relative h-64 w-full">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Charts Row 2 -->
            <div class="grid grid-cols-1 gap-6">
                <!-- Bar Chart: Performance Vendedores -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-700">Rendimiento: Bloqueos por Vendedor</h3>
                        <div class="flex space-x-2 text-sm">
                            <input type="date" x-model="perfFrom" @change="loadPerformance" class="border-gray-300 rounded-md shadow-sm">
                            <input type="date" x-model="perfTo" @change="loadPerformance" class="border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>
                    <div class="relative h-72 w-full">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Table: Reporte Bloqueos Caídos -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-700">Reporte: Bloqueos Caídos (Expirados)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha/Hora</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lote</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendedor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teléfono</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="row in expiredData" :key="row.date + row.lote">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="row.date"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        Mza <span x-text="row.manzana"></span>, Lte <span x-text="row.nro_lote"></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="row.vendedor"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="row.client_name"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="row.client_phone"></td>
                                </tr>
                            </template>
                            <tr x-show="expiredData.length === 0">
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No hay registros para mostrar.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination info -->
                <div class="px-6 py-3 border-t border-gray-200 bg-gray-50 flex justify-between items-center text-sm text-gray-500">
                    <span x-text="`Página ${pagination.current_page} de ${pagination.last_page}`"></span>
                    <div class="space-x-2">
                        <button class="text-indigo-600 hover:text-indigo-900" :disabled="pagination.current_page <= 1" @click="loadExpired(pagination.current_page - 1)">Anterior</button>
                        <button class="text-indigo-600 hover:text-indigo-900" :disabled="pagination.current_page >= pagination.last_page" @click="loadExpired(pagination.current_page + 1)">Siguiente</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('dashboardData', () => ({
                kpiDate: '{{ \Carbon\Carbon::today()->toDateString() }}',
                kpis: { blocks_today: 0, reservations_today: 0, expired_today: 0 },
                inventoryTotal: 0,
                inventoryData: [],
                
                perfFrom: '',
                perfTo: '',
                perfChartInstance: null,
                
                expiredData: [],
                pagination: { current_page: 1, last_page: 1, total: 0 },

                async init() {
                    this.loadKPIs();
                    this.loadInventory();
                    this.loadTrend();
                    this.loadPerformance();
                    this.loadExpired(1);
                },

                async loadKPIs() {
                    let res = await fetch('/api/dashboard/kpis?date=' + this.kpiDate);
                    this.kpis = await res.json();
                },

                async loadInventory() {
                    let res = await fetch('/api/dashboard/inventory');
                    let data = await res.json();
                    this.inventoryTotal = data.total;
                    this.inventoryData = data.data;

                    new Chart(document.getElementById('inventoryChart'), {
                        type: 'doughnut',
                        data: {
                            labels: data.data.map(d => d.label),
                            datasets: [{
                                data: data.data.map(d => d.value),
                                backgroundColor: data.data.map(d => d.color),
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } }
                        }
                    });
                },

                async loadTrend() {
                    let res = await fetch('/api/dashboard/trend');
                    let data = await res.json();

                    new Chart(document.getElementById('trendChart'), {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [
                                {
                                    label: 'Bloqueos Acumulados',
                                    data: data.blocks,
                                    borderColor: '#eab308',
                                    backgroundColor: 'rgba(234, 179, 8, 0.1)',
                                    fill: true,
                                    tension: 0.4
                                },
                                {
                                    label: 'Reservas Acumuladas',
                                    data: data.reservations,
                                    borderColor: '#1e1e1e',
                                    backgroundColor: 'rgba(30, 30, 30, 0.1)',
                                    fill: true,
                                    tension: 0.4
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: { mode: 'index', intersect: false },
                        }
                    });
                },

                async loadPerformance() {
                    let url = '/api/dashboard/performance?';
                    if (this.perfFrom) url += `from=${this.perfFrom}&`;
                    if (this.perfTo) url += `to=${this.perfTo}`;
                    
                    let res = await fetch(url);
                    let data = await res.json();

                    if (this.perfChartInstance) this.perfChartInstance.destroy();

                    this.perfChartInstance = new Chart(document.getElementById('performanceChart'), {
                        type: 'bar',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Bloqueos Generados',
                                data: data.values,
                                backgroundColor: '#4f46e5',
                                borderRadius: 4,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                        }
                    });
                },

                async loadExpired(page = 1) {
                    let res = await fetch('/api/dashboard/expired-report?page=' + page);
                    let result = await res.json();
                    this.expiredData = result.data;
                    this.pagination = result.pagination;
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>
