@extends('layouts.admintemp')

@section('title', 'Realtime Map')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Realtime Map</h1>
        <p class="text-sm text-gray-500">Peta Regional Telkom University</p>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
        <div class="relative w-full">
            <!-- Container Peta -->
            <div
                id="map"
                class="w-full rounded-lg border border-gray-200 bg-gray-100"
                style="width: 100%; height: 70vh; min-height: 420px;"
            ></div>

            <!-- Popup Container -->
            <div
                id="popup"
                class="hidden max-w-xs rounded-xl border border-gray-100 bg-white p-3 text-sm shadow-xl"
            >
                <div id="popup-content"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/interactivemap.js')
@endpush