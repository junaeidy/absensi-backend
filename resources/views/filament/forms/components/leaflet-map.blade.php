<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div 
        x-data="{
            state: $wire.$entangle('{{ $getStatePath() }}'),
            latitude: $wire.$entangle('data.latitude'),
            longitude: $wire.$entangle('data.longitude'),
            map: null,
            marker: null,
            isLoadingLocation: false,
            locationError: null,
            
            init() {
                this.$nextTick(() => {
                    this.initMap();
                    // Auto-detect location hanya jika koordinat belum ada (halaman create)
                    if (!this.latitude || !this.longitude) {
                        this.getCurrentLocation();
                    }
                });
            },
            
            getCurrentLocation() {
                if ('geolocation' in navigator) {
                    this.isLoadingLocation = true;
                    this.locationError = null;
                    
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            // Success - update coordinates
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            
                            this.latitude = lat.toFixed(6);
                            this.longitude = lng.toFixed(6);
                            
                            // Update map and marker
                            this.marker.setLatLng([lat, lng]);
                            this.map.setView([lat, lng], {{ $getDefaultZoom() }});
                            
                            this.isLoadingLocation = false;
                            
                            // Show success notification
                            new FilamentNotification()
                                .title('Lokasi Terdeteksi')
                                .success()
                                .body('Lokasi perangkat berhasil dideteksi. Anda bisa menyesuaikan marker jika diperlukan.')
                                .send();
                        },
                        (error) => {
                            // Error handling
                            this.isLoadingLocation = false;
                            let errorMessage = '';
                            
                            switch(error.code) {
                                case error.PERMISSION_DENIED:
                                    errorMessage = 'Izin lokasi ditolak. Silakan aktifkan izin lokasi di browser Anda.';
                                    break;
                                case error.POSITION_UNAVAILABLE:
                                    errorMessage = 'Informasi lokasi tidak tersedia. Pastikan GPS/WiFi aktif.';
                                    break;
                                case error.TIMEOUT:
                                    errorMessage = 'Waktu permintaan lokasi habis. Coba lagi atau atur koordinat secara manual.';
                                    break;
                                default:
                                    errorMessage = 'Terjadi kesalahan saat mendeteksi lokasi.';
                            }
                            
                            this.locationError = errorMessage;
                            
                            // Hanya tampilkan notifikasi jika user yang klik tombol
                            // Tidak auto-show jika timeout di background
                            if (error.code !== error.TIMEOUT || this.isLoadingLocation) {
                                new FilamentNotification()
                                    .title('Gagal Mendeteksi Lokasi')
                                    .warning()
                                    .body(errorMessage)
                                    .send();
                            }
                        },
                        {
                            enableHighAccuracy: false, // Ubah ke false untuk lebih cepat
                            timeout: 15000, // Tambah jadi 15 detik
                            maximumAge: 30000 // Boleh cache 30 detik untuk performa
                        }
                    );
                } else {
                    this.locationError = 'Browser tidak mendukung Geolocation API.';
                    
                    new FilamentNotification()
                        .title('Geolocation Tidak Didukung')
                        .warning()
                        .body('Browser Anda tidak mendukung deteksi lokasi otomatis.')
                        .send();
                }
            },
            
            initMap() {
                // Initialize map
                this.map = L.map('map-{{ $getId() }}').setView(
                    [
                        this.latitude || {{ $getDefaultLatitude() }}, 
                        this.longitude || {{ $getDefaultLongitude() }}
                    ], 
                    {{ $getDefaultZoom() }}
                );
                
                // Add OpenStreetMap tile layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href=\'http://www.openstreetmap.org/copyright\'>OpenStreetMap</a>'
                }).addTo(this.map);
                
                // Create draggable marker
                this.marker = L.marker(
                    [
                        this.latitude || {{ $getDefaultLatitude() }}, 
                        this.longitude || {{ $getDefaultLongitude() }}
                    ],
                    { draggable: true }
                ).addTo(this.map);
                
                // Update coordinates when marker is dragged
                this.marker.on('dragend', (event) => {
                    const position = event.target.getLatLng();
                    this.latitude = position.lat.toFixed(6);
                    this.longitude = position.lng.toFixed(6);
                });
                
                // Add click event to map to move marker
                this.map.on('click', (event) => {
                    const position = event.latlng;
                    this.marker.setLatLng(position);
                    this.latitude = position.lat.toFixed(6);
                    this.longitude = position.lng.toFixed(6);
                });
                
                // Watch for manual coordinate changes to update map
                this.$watch('latitude', (value) => {
                    if (value && this.longitude) {
                        const lat = parseFloat(value);
                        const lng = parseFloat(this.longitude);
                        if (!isNaN(lat) && !isNaN(lng)) {
                            this.marker.setLatLng([lat, lng]);
                            this.map.setView([lat, lng]);
                        }
                    }
                });
                
                this.$watch('longitude', (value) => {
                    if (value && this.latitude) {
                        const lat = parseFloat(this.latitude);
                        const lng = parseFloat(value);
                        if (!isNaN(lat) && !isNaN(lng)) {
                            this.marker.setLatLng([lat, lng]);
                            this.map.setView([lat, lng]);
                        }
                    }
                });
            }
        }"
        wire:ignore
    >
        <!-- Leaflet CSS -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
            crossorigin=""/>
        
        <!-- Leaflet JS -->
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>
        
        <!-- Custom CSS untuk fix z-index -->
        <style>
            /* Pastikan container peta tidak menutupi topbar Filament */
            .leaflet-container {
                z-index: 1 !important;
            }
            
            /* Leaflet controls (zoom, attribution) tetap di atas peta tapi di bawah topbar */
            .leaflet-control-container {
                z-index: 2 !important;
            }
            
            .leaflet-pane {
                z-index: auto !important;
            }
            
            /* Leaflet popup dan tooltip di atas peta tapi tetap di bawah topbar */
            .leaflet-popup-pane {
                z-index: 3 !important;
            }
            
            .leaflet-tooltip-pane {
                z-index: 3 !important;
            }
        </style>
        
        <div class="space-y-2">
            <!-- Loading Indicator -->
            <div x-show="isLoadingLocation" class="flex items-center gap-2 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                <svg class="animate-spin h-5 w-5 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm text-blue-700 dark:text-blue-300 font-medium">Mendeteksi lokasi perangkat...</span>
            </div>

            <!-- Map Container -->
            <div class="relative isolate" style="z-index: 1;">
                <div 
                    id="map-{{ $getId() }}" 
                    style="height: {{ $getMapHeight() }}px; width: 100%; border-radius: 0.5rem; border: 1px solid rgb(209 213 219);"
                    class="leaflet-container"
                ></div>
                
                <!-- Detect Location Button (Floating) -->
                <button 
                    type="button"
                    @click="getCurrentLocation()"
                    :disabled="isLoadingLocation"
                    class="absolute top-3 right-3 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg rounded-lg p-2.5 border border-gray-300 dark:border-gray-600 transition-all"
                    style="z-index: 1000;"
                    title="Deteksi Lokasi Saya"
                >
                    <svg class="w-5 h-5 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
            </div>
            
            <!-- Instructions -->
            <div class="text-sm text-gray-500 dark:text-gray-400">
                <p class="flex items-center gap-2">
                    <span x-show="!latitude || !longitude">Klik tombol lokasi untuk mendeteksi posisi Anda, atau drag marker/klik peta untuk mengatur koordinat secara manual.</span>
                    <span x-show="latitude && longitude">Drag marker, klik peta, atau klik tombol lokasi untuk menyesuaikan koordinat.</span>
                </p>
            </div>
        </div>
    </div>
</x-dynamic-component>
