@if($programs->isNotEmpty())
    @foreach($programs->groupBy('facultyID') as $facultyID => $programsInFaculty)
        @php
            $faculty = $programsInFaculty->first()?->faculty;
        @endphp
        <div class="mb-6">
            <!-- Faculty Header -->
            <h2 class="text-lg font-semibold text-gray-100 bg-gray-600 p-3 rounded-t-lg">
                {{ $faculty ? $faculty->name : 'Faculty Not Found' }}
            </h2>

            <!-- Group Programs by Batch -->
            @foreach($programsInFaculty->groupBy('batchID') as $batchID => $programsInBatch)
                <div class="bg-gray-800 p-4 rounded-lg mb-3">
                    <!-- Batch Header -->
                    <h3 class="text-md font-medium text-gray-200 bg-gray-700 p-2 rounded">
                        Batch: {{ $batches->firstWhere('batchID', $batchID)?->batchName ?? 'Batch Not Found' }}
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full mt-2 leading-normal text-left">
                            <thead>
                                <tr class="bg-gray-700 text-gray-300 uppercase text-sm tracking-wider">
                                    <th class="py-2 px-4">Program ID</th>
                                    <th class="py-2 px-4">Program Name</th>
                                    <th class="py-2 px-4">Level of Education</th>
                                    <th class="py-2 px-4">Program Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-800 text-gray-400">
                                @foreach($programsInBatch as $program)
                                    <tr class="border-b border-gray-600 hover:bg-gray-700 cursor-pointer" onclick="showProgramModal({{ json_encode($program) }})">
                                        <td class="py-2 px-4">{{ $program->programID }}</td>
                                        <td class="py-2 px-4">{{ $program->programName }}</td>
                                        <td class="py-2 px-4">{{ $program->levelEdu }}</td>
                                        <td class="py-2 px-4">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $program->programStatus === 'Approved' ? 'bg-green-100 text-green-800' : ($program->programStatus === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                {{ ucfirst($program->programStatus) }}
                                            </span>
                                        </td>                        
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
@else
    <!-- No Programs Found -->
    <p class="text-gray-300 p-4">No programs found for the selected batch.</p>
@endif
