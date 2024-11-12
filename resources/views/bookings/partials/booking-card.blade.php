<div class="event-card cursor-pointer bg-white shadow-md rounded-lg p-3 flex items-center" data-details="{{ json_encode($booking) }}">
    <div class="w-20 flex flex-col items-center justify-center text-center date-container">
        @php
            $startDate = \Carbon\Carbon::parse($booking->start_time);
            $endDate = \Carbon\Carbon::parse($booking->end_time);
        @endphp
        <span class="block text-red-500 font-bold">{{ $startDate->format('D') }}</span>
        <span class="block text-red-500 text-2xl leading-tight date-text">
            @if ($startDate->isSameDay($endDate))
                {{ $startDate->format('d') }}
            @else
                {{ $startDate->format('d') }} - {{ $endDate->format('d') }}
            @endif
        </span>
        <span class="block text-gray-500">
            @if ($startDate->isSameMonth($endDate))
                {{ $startDate->format('M') }}
            @else
                {{ $startDate->format('M') }} - {{ $endDate->format('M') }}
            @endif
        </span>
    </div>
    <div class="flex-grow pl-4 overflow-hidden">
        <h3 class="text-md font-semibold text-gray-800 truncate">{{ $booking->resource->name }}</h3>
        <p class="text-sm text-gray-500">{{ $startDate->format('H:i') }} - {{ $endDate->format('H:i') }}</p>
        <p class="text-sm text-gray-500 truncate">Program: {{ $booking->programName ?? 'N/A' }}</p>
        <p class="text-sm text-gray-500">
            @if (!$startDate->isSameDay($endDate))
                Duration: {{ $startDate->format('M d, H:i') }} - {{ $endDate->format('M d, H:i') }}
            @endif
        </p>
    </div>
</div>
