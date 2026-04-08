<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Configuración del Sistema') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-4 bg-red-50 p-4 rounded-md">
                    <ul class="list-disc list-inside text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Gráficas de Tendencia</h3>
                    
                    <form method="POST" action="{{ route('admin.settings.update') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="trend_start_date" class="block text-sm font-medium text-gray-700">Día 1 (Fecha de inicio para la curva del gráfico line-chart)</label>
                            <p class="text-xs text-gray-500 mt-1 mb-2">Usado en el Dashboard para acumular reservas y bloqueos.</p>
                            
                            @php
                                $val = \App\Models\SystemSetting::get('trend_start_date', \Carbon\Carbon::today()->toDateString());
                            @endphp
                            <input type="date" name="trend_start_date" id="trend_start_date" value="{{ $val }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <div class="flex items-center">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Guardar Preferencias
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Auto Expiración</h3>
                    <p class="text-sm text-gray-600">El sistema tiene configurada una tarea programada para ejecutarse automáticamente cada minuto. Los bloques con fecha de expiración `<= now()` serán devueltos a estado Disponible.</p>
                    <div class="mt-4 p-3 bg-gray-50 rounded text-xs font-mono text-gray-600 border border-gray-200">
                        * * * * * cd {{ base_path() }} && php artisan schedule:run >> /dev/null 2>&1
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Esta línea debe estar configurada en el cron de Hostinger para que el proceso funcione.</p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
