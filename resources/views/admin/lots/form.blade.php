<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $lot->exists ? __('Editar Lote') : __('Nuevo Lote') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    @if ($errors->any())
                        <div class="mb-4 bg-red-50 p-4 rounded-md">
                            <ul class="list-disc list-inside text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ $lot->exists ? route('admin.lots.update', $lot) : route('admin.lots.store') }}">
                        @csrf
                        @if($lot->exists)
                            @method('PUT')
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Manzana -->
                            <div>
                                <label for="manzana" class="block font-medium text-sm text-gray-700">Manzana <span class="text-red-500">*</span></label>
                                <input type="text" name="manzana" id="manzana" value="{{ old('manzana', $lot->manzana) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <!-- Nro Lote -->
                            <div>
                                <label for="nro_lote" class="block font-medium text-sm text-gray-700">Número de Lote <span class="text-red-500">*</span></label>
                                <input type="text" name="nro_lote" id="nro_lote" value="{{ old('nro_lote', $lot->nro_lote) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <!-- Superficie -->
                            <div>
                                <label for="superficie" class="block font-medium text-sm text-gray-700">Superficie (m&sup2;) <span class="text-red-500">*</span></label>
                                <input type="number" step="0.01" name="superficie" id="superficie" value="{{ old('superficie', $lot->superficie) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <!-- Precio -->
                            <div>
                                <label for="precio" class="block font-medium text-sm text-gray-700">Precio (USD) <span class="text-red-500">*</span></label>
                                <input type="number" step="0.01" name="precio" id="precio" value="{{ old('precio', $lot->precio) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            
                            <!-- Estado -->
                            <div>
                                <label for="estado" class="block font-medium text-sm text-gray-700">Estado Inicial <span class="text-red-500">*</span></label>
                                <select name="estado" id="estado" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @foreach(\App\Models\Lot::ESTADO_LABELS as $key => $label)
                                        <option value="{{ $key }}" {{ old('estado', $lot->estado ?? 'oculto') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Zona -->
                            <div>
                                <label for="zona" class="block font-medium text-sm text-gray-700">Zona</label>
                                <input type="text" name="zona" id="zona" value="{{ old('zona', $lot->zona) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <!-- FOT -->
                            <div>
                                <label for="fot" class="block font-medium text-sm text-gray-700">FOT</label>
                                <input type="number" step="0.01" name="fot" id="fot" value="{{ old('fot', $lot->fot) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <!-- FOS -->
                            <div>
                                <label for="fos" class="block font-medium text-sm text-gray-700">FOS</label>
                                <input type="number" step="0.01" name="fos" id="fos" value="{{ old('fos', $lot->fos) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <!-- H Maxima -->
                            <div>
                                <label for="h_maxima" class="block font-medium text-sm text-gray-700">Altura Máxima</label>
                                <input type="number" step="0.01" name="h_maxima" id="h_maxima" value="{{ old('h_maxima', $lot->h_maxima) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <!-- Observaciones -->
                            <div>
                                <label for="observaciones" class="block font-medium text-sm text-gray-700">Observaciones (Ej: Esquina)</label>
                                <input type="text" name="observaciones" id="observaciones" value="{{ old('observaciones', $lot->observaciones) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                        </div>

                        <div class="mt-6 flex items-center justify-end">
                            <a href="{{ route('admin.lots.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Guardar
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
