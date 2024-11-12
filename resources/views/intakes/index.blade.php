<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-100 leading-tight">
            {{ __('Manage Program Entry Levels and Intakes') }}
        </h2>
    </x-slot>

    <div class="container mx-auto py-8">
        <div class="overflow-hidden shadow-lg rounded-lg bg-gray-900 p-6 mb-10">
            @if(Auth::user()->role === 'admin')
                <h3 class="text-lg font-bold text-gray-200 mb-4">All Programs</h3>
            @else
                <h3 class="text-lg font-bold text-gray-200 mb-4">Your Programs</h3>
            @endif

            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal text-left">
                    <thead>
                        <tr class="bg-gray-700 text-gray-300 uppercase text-sm tracking-wider">
                            <th class="py-2 px-4">Program ID</th>
                            <th class="py-2 px-4">Program Name</th>
                            <th class="py-2 px-4">Batch ID</th>
                            <th class="py-2 px-4">Entry Level</th>
                            <th class="py-2 px-4">Intake Count</th>
                            <th class="py-2 px-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-gray-800 text-gray-400">
                        @foreach($programs as $program)
                            <tr class="border-b border-gray-600 hover:bg-gray-700">
                                <form action="{{ route('intakes.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="program_id[{{ $program->programID }}]" value="{{ $program->programID }}">
                                    <td class="py-2 px-4">{{ $program->programID }}</td>
                                    <td class="py-2 px-4">{{ $program->programName }}</td>
                                    <td class="py-2 px-4">
                                        <select name="batch_id[{{ $program->programID }}]" class="form-select bg-gray-700 text-gray-100 rounded-md w-full focus:border-blue-500 focus:ring-blue-500">
                                            @foreach($batches as $batch)
                                                <option value="{{ $batch->batchID }}">{{ $batch->batchID }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="py-2 px-4">
                                        <select name="entry_level_id[{{ $program->programID }}]" class="form-select bg-gray-700 text-gray-100 rounded-md w-full focus:border-blue-500 focus:ring-blue-500">
                                            <option value="" disabled selected>Select an entry level</option>
                                            @foreach($entryLevels as $entryLevel)
                                                <option value="{{ $entryLevel->entryLevelID }}">{{ $entryLevel->entryLevelName }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="py-2 px-4">
                                        <input type="number" name="intake_count[{{ $program->programID }}]" class="form-input bg-gray-700 text-gray-100 rounded-md w-full focus:border-blue-500 focus:ring-blue-500" placeholder="Intake Count" value="0" required>
                                    </td>
                                    <td class="py-2 px-4 text-center">
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1 px-4 rounded-lg">
                                            Save
                                        </button>
                                    </td>
                                </form>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="overflow-hidden shadow-lg rounded-lg bg-gray-900 p-6">
            <h3 class="text-lg font-bold text-gray-200 mb-4">Existing Program Entry Levels</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal text-left">
                    <thead>
                        <tr class="bg-gray-700 text-gray-300 uppercase text-sm tracking-wider">
                            <th class="py-2 px-4">Program ID</th>
                            <th class="py-2 px-4">Program Name</th>
                            <th class="py-2 px-4">Batch ID</th>
                            <th class="py-2 px-4">Entry Level</th>
                            <th class="py-2 px-4">Intake Count</th>
                        </tr>
                    </thead>
                    <tbody class="bg-gray-800 text-gray-400">
                        @foreach($programEntryLevels as $entryLevel)
                            <tr class="border-b border-gray-600 hover:bg-gray-700">
                                <td class="py-2 px-4">{{ optional($entryLevel->program)->programID }}</td>
                                <td class="py-2 px-4">{{ optional($entryLevel->program)->programName }}</td>
                                <td class="py-2 px-4">{{ optional($entryLevel->batch)->batchID }}</td>
                                <td class="py-2 px-4">{{ optional($entryLevel->entryLevel)->entryLevelName }}</td>
                                <td class="py-2 px-4">{{ $entryLevel->intake_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
