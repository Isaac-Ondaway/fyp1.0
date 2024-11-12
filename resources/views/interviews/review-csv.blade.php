<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-100 leading-tight">
            {{ __('Review CSV Data') }}
        </h2>
    </x-slot>

    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-extrabold mb-8 text-gray-200">Review CSV Data</h1>

        <!-- Display the parsed CSV data in a table -->
        <form action="{{ route('interviews.bulkStoreCsv') }}" method="POST">
            @csrf
            <input type="hidden" name="filePath" value="{{ $path }}">

            <table class="table-auto w-full text-left text-gray-100">
                <thead class="bg-gray-700 text-white">
                    <tr>
                        @foreach($headers as $header)
                            <th class="px-4 py-2">{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-gray-800 text-gray-200">
                    @foreach($rows as $row)
                        @if(!empty(array_filter($row)))  <!-- This ensures only non-empty rows are rendered -->
                            <tr>
                                @foreach($row as $value)
                                    <td class="border px-4 py-2">{{ $value }}</td>
                                @endforeach
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>

            <!-- Buttons Section -->
            <div class="mt-8 flex space-x-4">
                <!-- Back Button -->
                <a href="javascript:history.back()" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg">
                    Back
                </a>

                <!-- Confirm and Submit Button -->
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                    Confirm and Submit
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
