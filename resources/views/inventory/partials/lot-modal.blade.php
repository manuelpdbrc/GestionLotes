<!-- Lot Detail Modal -->
<div class="fixed inset-0 z-50 overflow-y-auto" x-show="showModal" style="display: none;"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     x-init="$watch('showModal', value => { if (value) document.body.classList.add('overflow-hidden'); else document.body.classList.remove('overflow-hidden'); })">
    
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <!-- Overlay -->
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" @click="showModal = false"></div>

        <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
             x-show="showModal"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

            <!-- Loading State -->
            <div x-show="loadingData" class="flex justify-center items-center py-10">
                <svg class="w-8 h-8 text-indigo-500 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            <!-- Modal Content -->
            <div x-show="!loadingData && selectedLot" style="display: none;">
                <!-- Header -->
                <div class="flex justify-between items-start mb-5 border-b pb-4">
                    <div>
                        <h3 class="text-xl font-bold leading-6 text-gray-900" id="modal-title">
                            Manzana <span x-text="selectedLot?.manzana"></span>, Lote <span x-text="selectedLot?.nro_lote"></span>
                        </h3>
                        <div class="mt-2 flex items-center space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white"
                                  :style="`background-color: ${selectedLot?.color}`"
                                  x-text="selectedLot?.label">
                            </span>
                            <span class="text-sm text-gray-500" x-show="selectedLot?.observaciones" x-text="selectedLot?.observaciones"></span>
                        </div>
                    </div>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" @click="showModal = false">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    </button>
                </div>

                <!-- Grid specs -->
                <div class="grid grid-cols-2 gap-4 text-sm mb-6">
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                        <span class="block text-gray-500 text-xs uppercase tracking-wider font-semibold mb-1">Superficie</span>
                        <span class="text-gray-900 font-medium" x-text="`${selectedLot?.superficie} m²`"></span>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100 text-right">
                        <span class="block text-gray-500 text-xs uppercase tracking-wider font-semibold mb-1">Precio</span>
                        <span class="text-green-600 font-bold text-base" x-text="`USD ${selectedLot?.precio}`"></span>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg justify-center flex flex-col items-center">
                        <span class="block text-gray-500 text-xs uppercase tracking-wider font-semibold">FOT</span>
                        <span class="text-gray-900 font-medium" x-text="selectedLot?.fot ?? '-'"></span>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg justify-center flex flex-col items-center">
                        <span class="block text-gray-500 text-xs uppercase tracking-wider font-semibold">FOS</span>
                        <span class="text-gray-900 font-medium" x-text="selectedLot?.fos ?? '-'"></span>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg justify-center flex flex-col items-center col-span-2">
                        <span class="block text-gray-500 text-xs uppercase tracking-wider font-semibold">Alt. Máxima</span>
                        <span class="text-gray-900 font-medium" x-text="selectedLot?.h_maxima ? `${selectedLot.h_maxima} m` : '-'"></span>
                    </div>
                </div>

                <!-- Bloqueo Info -->
                <div x-show="selectedLot?.block" class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Lote Bloqueado</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p><strong>Vendedor:</strong> <span x-text="selectedLot?.block?.vendedor"></span></p>
                                <p><strong>Cliente:</strong> <span x-text="selectedLot?.block?.client_name"></span></p>
                                <p x-show="isSupervisor || selectedLot?.block?.is_own"><strong>Tel:</strong> <span x-text="selectedLot?.block?.client_phone"></span></p>
                                <p class="mt-1 font-semibold"><strong>Vence:</strong> <span x-text="selectedLot?.block?.expires_at"></span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acciones Vendedor (Bloquear) -->
                <div x-data="{ client_name: '', client_phone: '', loading: false }" 
                     x-show="isVendedor && selectedLot?.is_blockable" class="mt-4 border-t pt-4">
                    <h4 class="text-sm font-bold text-gray-700 mb-2">Bloquear este lote</h4>
                    <div class="space-y-3">
                        <input type="text" x-model="client_name" placeholder="Nombre completo del cliente" required class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                        <input type="text" x-model="client_phone" placeholder="Teléfono" required class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                        <button type="button" 
                                @click="
                                    if(!client_name || !client_phone) { alert('Completá los datos del cliente'); return; }
                                    loading = true;
                                    window.fetchApi('/blocks', { method: 'POST', body: JSON.stringify({ lot_id: selectedLot.id, client_name, client_phone }) })
                                    .then(res => { alert(res.message); window.location.reload(); })
                                    .catch(err => { alert(err.message || 'Error bloqueando lote'); loading = false; });
                                "
                                :disabled="loading"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:text-sm disabled:opacity-50">
                            Confirmar Bloqueo
                        </button>
                    </div>
                </div>

                <!-- Acciones Vendedor (Cancelar) -->
                <div x-show="isVendedor && selectedLot?.block?.is_own" class="mt-4 border-t pt-4">
                    <button type="button" 
                            @click="if(confirm('¿Seguro quieres cancelar tu bloqueo?')) {
                                window.fetchApi('/blocks/' + selectedLot.block.id, { method: 'DELETE' })
                                .then(res => { alert(res.message); window.location.reload(); })
                                .catch(err => alert(err.message));
                            }"
                            class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:text-sm">
                        Cancelar mi bloqueo
                    </button>
                </div>

                <!-- Acciones Supervisor -->
                <div x-show="isSupervisor && selectedLot?.estado === 'disponible'" class="mt-4 border-t pt-4 grid grid-cols-2 gap-2">
                    <button type="button" 
                            @click="if(confirm('Marcar como reservado?')) {
                                window.fetchApi('/lots/' + selectedLot.id + '/reserve', { method: 'PUT' })
                                .then(res => { alert(res.message); window.location.reload(); });
                            }"
                            class="w-full justify-center rounded-md border border-transparent px-4 py-2 bg-gray-800 text-white hover:bg-gray-900 focus:outline-none sm:text-sm">
                        Reservar
                    </button>
                    <button type="button" 
                            @click="if(confirm('Marcar como vendido?')) {
                                window.fetchApi('/lots/' + selectedLot.id + '/sell', { method: 'PUT' })
                                .then(res => { alert(res.message); window.location.reload(); });
                            }"
                            class="w-full justify-center rounded-md border border-transparent px-4 py-2 bg-gray-900 text-white hover:bg-black focus:outline-none sm:text-sm">
                        Vender
                    </button>
                </div>

                <div x-data="{ extendTime: '', showExtend: false }" x-show="isSupervisor && selectedLot?.block" class="mt-4 border-t pt-4">
                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <button type="button" @click="showExtend = !showExtend" class="w-full justify-center rounded-md border border-yellow-300 px-4 py-2 bg-yellow-100 text-yellow-800 hover:bg-yellow-200">
                            Extender
                        </button>
                        <button type="button" 
                            @click="if(confirm('Liberar bloqueo manualmente?')) {
                                window.fetchApi('/blocks/' + selectedLot.block.id + '/release', { method: 'PUT' })
                                .then(res => { alert(res.message); window.location.reload(); });
                            }" class="w-full justify-center rounded-md border border-red-300 px-4 py-2 bg-red-100 text-red-800 hover:bg-red-200">
                            Liberar
                        </button>
                    </div>
                    <!-- Form extend -->
                    <div x-show="showExtend" class="mt-3 flex space-x-2">
                         <input type="datetime-local" x-model="extendTime" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                         <button @click="
                             if(!extendTime) { alert('Elige fecha'); return; }
                             window.fetchApi('/blocks/' + selectedLot.block.id + '/extend', { 
                                 method: 'PUT', 
                                 body: JSON.stringify({ expires_at: extendTime.replace('T', ' ') }) 
                             }).then(res => { alert(res.message); window.location.reload(); });
                         " class="bg-yellow-500 text-white px-3 py-2 rounded-md text-sm font-medium">OK</button>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-2 mt-4 pt-4 border-t border-gray-100">
                        <button type="button" 
                                @click="if(confirm('Marcar como reservado?')) {
                                    window.fetchApi('/lots/' + selectedLot.id + '/reserve', { method: 'PUT' })
                                    .then(res => { alert(res.message); window.location.reload(); });
                                }"
                                class="w-full justify-center rounded-md border border-transparent px-4 py-2 bg-gray-800 text-white hover:bg-gray-900 focus:outline-none sm:text-sm">
                            Pasar a Reserva
                        </button>
                        <button type="button" 
                                @click="if(confirm('Marcar como vendido?')) {
                                    window.fetchApi('/lots/' + selectedLot.id + '/sell', { method: 'PUT' })
                                    .then(res => { alert(res.message); window.location.reload(); });
                                }"
                                class="w-full justify-center rounded-md border border-transparent px-4 py-2 bg-gray-900 text-white hover:bg-black focus:outline-none sm:text-sm">
                            Confirmar Venta
                        </button>
                    </div>
                </div>

                <!-- Footer Whatsapp Link -->
                <div class="mt-6 border-t pt-4">
                    <a :href="selectedLot?.whatsapp_url" target="_blank" class="w-full flex justify-center items-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-500 text-base font-medium text-white hover:bg-green-600 focus:outline-none sm:text-sm">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                        Enviar por WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
