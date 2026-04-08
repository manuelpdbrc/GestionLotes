<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Importar Lotes desde Excel') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <div class="mb-6 border-b pb-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Instrucciones</h3>
                        <p class="text-sm text-gray-600 mb-2">El archivo debe ser un Excel (.xlsx, .xls) o CSV con las siguientes columnas en la primera fila (los nombres deben coincidir exactamente, sin omitir los obligatorios):</p>
                        <ul class="list-disc list-inside text-sm text-gray-700 bg-gray-50 p-4 rounded-md">
                            <li><strong>manzana</strong> (Requerido)</li>
                            <li><strong>nro_lote</strong> (Requerido)</li>
                            <li><strong>superficie</strong> (Requerido, numérico)</li>
                            <li><strong>precio</strong> (Requerido, numérico USD)</li>
                            <li>estado (Opcional. Valores: disponible, reservado, vendido, no_disponible. Si no se indica, arranca 'oculto')</li>
                            <li>zona (Opcional)</li>
                            <li>fot (Opcional, numérico)</li>
                            <li>fos (Opcional, numérico)</li>
                            <li>h_maxima (Opcional, numérico)</li>
                            <li>observaciones (Opcional)</li>
                        </ul>
                    </div>

                    @if ($errors->any())
                        <div class="mb-4 bg-red-50 p-4 rounded-md">
                            <ul class="list-disc list-inside text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Show import errors reported by the parser -->
                    @if(session('import_errors') && count(session('import_errors')) > 0)
                        <div class="mb-6 bg-yellow-50 p-4 rounded-md border border-yellow-200">
                            <h4 class="text-sm font-bold text-yellow-800 mb-2">Se omitieron {{ count(session('import_errors')) }} filas por errores/duplicados:</h4>
                            <div class="max-h-40 overflow-y-auto">
                                <ul class="list-disc list-inside text-xs text-yellow-700">
                                    @foreach(session('import_errors') as $err)
                                        @if(isset($err['manzana']))
                                            <li>Mza: {{ $err['manzana'] }}, Lote: {{ $err['nro_lote'] }} - {{ $err['reason'] }}</li>
                                        @else
                                            <li>Fila con datos faltantes - {{ $err['reason'] }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.lots.import') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label for="file" class="block font-medium text-sm text-gray-700 mb-2">Archivo Excel / CSV</label>
                            <input type="file" name="file" id="file" accept=".xlsx,.xls,.csv" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>

                        <div class="mt-6 flex items-center">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">
                                Subir e Importar
                            </button>
                            <a href="{{ route('admin.lots.index') }}" class="ml-4 text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
