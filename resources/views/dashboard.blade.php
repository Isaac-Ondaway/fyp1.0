<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}

                        <div class="mt-4">
                            <a href="{{ route('programs.create') }}" class="btn btn-primary bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Add New Program
                            </a>
                        </div>
                    <!-- Show Synced Google Account -->
                    @if (Auth::user()->google_email)
                        <div class="mt-4 text-sm text-gray-600">
                            <strong>Google Account Synced:</strong> {{ Auth::user()->google_email }}
                        </div>
                    @else
                        <div class="mt-4 text-sm text-gray-600">
                            <strong>Google Account Synced:</strong> None
                        </div>
                    @endif
                        <!-- Calendar Component -->
                        <div id="app">
                            <calendar-component></calendar-component>
                        </div>

                        
                </div>
            </div>
        </div>
    </div>
    @vite(['resources/js/app.js', 'resources/css/app.css'])
</x-app-layout>
