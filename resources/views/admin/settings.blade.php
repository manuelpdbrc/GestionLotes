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
                    
                    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-6 pb-6 border-b border-gray-200">
                            <h4 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-wider">Personalización Visual</h4>
                            
                            <div class="mb-4">
                                <label for="app_title" class="block text-sm font-medium text-gray-700">Título de la App</label>
                                @php
                                    $valTitle = \App\Models\SystemSetting::get('app_title', 'Gestión de Lotes');
                                @endphp
                                <input type="text" name="app_title" id="app_title" value="{{ $valTitle }}" class="mt-1 block max-w-sm w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <div>
                                <label for="app_logo" class="block text-sm font-medium text-gray-700">Logo de la App (Opcional)</label>
                                <p class="text-xs text-gray-500 mt-1 mb-2">Sube una imagen para reemplazar el texto en la barra superior (PNG, JPG, SVG max 2MB).</p>
                                @php
                                    $valLogo = \App\Models\SystemSetting::get('app_logo_path');
                                @endphp
                                @if($valLogo)
                                    <div class="mb-2 bg-gray-100 inline-block p-2 rounded">
                                        <img src="{{ Storage::url($valLogo) }}" alt="Logo Actual" class="h-10 object-contain">
                                    </div>
                                @endif
                                <input type="file" name="app_logo" id="app_logo" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            </div>
                        </div>

                        <div class="mb-6">
                            <h4 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-wider">Dashboard</h4>
                            <label for="trend_start_date" class="block text-sm font-medium text-gray-700">Día 1 (Fecha de inicio para la curva de tendencia)</label>
                            <p class="text-xs text-gray-500 mt-1 mb-2">Usado para acumular el histórico en la gráfica de línea.</p>
                            
                            @php
                                $val = \App\Models\SystemSetting::get('trend_start_date', \Carbon\Carbon::today()->toDateString());
                            @endphp
                            <input type="date" name="trend_start_date" id="trend_start_date" value="{{ $val }}" required class="mt-1 block max-w-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
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
