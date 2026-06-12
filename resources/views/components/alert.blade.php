@props(['type' => 'info', 'message' => ''])

@php
    $classes = [
        'success' => 'bg-green-100 border-green-500 text-green-700',
        'error' => 'bg-red-100 border-red-500 text-red-700',
        'warning' => 'bg-yellow-100 border-yellow-500 text-yellow-700',
        'info' => 'bg-blue-100 border-blue-500 text-blue-700',
    ][$type];
    
    $icons = [
        'success' => 'fa-check-circle',
        'error' => 'fa-exclamation-circle',
        'warning' => 'fa-exclamation-triangle',
        'info' => 'fa-info-circle',
    ][$type];
@endphp

@if($message)
    <div class="{{ $classes }} border-l-4 p-4 mb-6 rounded-r-lg">
        <div class="flex items-center">
            <i class="fas {{ $icons }} mr-3"></i>
            <span>{{ $message }}</span>
        </div>
    </div>
@endif