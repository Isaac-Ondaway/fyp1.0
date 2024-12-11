<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-100 leading-tight">
            {{ __('Faculty Management') }}
        </h2>
    </x-slot>

    <div class="container mx-auto py-8">
        <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
            <h1 class="text-3xl font-bold text-white mb-4">Faculty List</h1>

            <a href="{{ route('faculty.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded mb-6 inline-block shadow-lg transition duration-200">
                + Add New Faculty
            </a>

            @if(session('success'))
                <div class="bg-green-500 text-white px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-hidden rounded-lg shadow-lg">
                <table class="min-w-full bg-gray-900 text-gray-100">
                    <thead>
                        <tr class="bg-gray-700 text-gray-400 uppercase text-sm tracking-wider">
                            <th class="px-6 py-3 text-left text-base font-semibold">Faculty ID</th>
                            <th class="px-6 py-3 text-left text-base font-semibold">Faculty Name</th>
                            <th class="px-6 py-3 text-left text-base font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($faculties as $faculty)
                            <tr class="hover:bg-gray-800 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $faculty->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $faculty->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                    <a href="{{ route('faculty.edit', $faculty->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-1 px-4 rounded transition duration-150 shadow-sm">
                                        Edit
                                    </a>
                                    <form action="{{ route('faculty.destroy', $faculty->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-semibold py-1 px-4 rounded transition duration-150 shadow-sm">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
