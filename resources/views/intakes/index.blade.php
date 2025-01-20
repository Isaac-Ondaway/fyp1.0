<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-100 leading-tight">
            {{ __('Manage Entry Levels and Intakes') }}
        </h2>
    </x-slot>

    <div class="container mx-auto py-8">
        <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
            <h3 class="text-3xl font-bold mb-8 text-white mb-4">Manage Intakes</h3>

            <!-- Filters -->
            <div class="flex items-center mb-4 space-x-4">
                <!-- Batch Selection -->
                <div class="w-48">
                    <label for="batch" class="block text-gray-300 font-semibold mb-2">Select Batch:</label>
                    <form id="filtersForm" action="{{ route('intakes.index') }}" method="GET">
                        <select name="batch_id" id="batch" class="form-select w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500" onchange="document.getElementById('filtersForm').submit()">
                            <option value="" disabled {{ $selectedBatchID ? '' : 'selected' }}>Choose a Batch</option>
                            @foreach($batches as $batch)
                                <option value="{{ $batch->batchID }}" {{ $selectedBatchID == $batch->batchID ? 'selected' : '' }}>{{ $batch->batchName }}</option>
                            @endforeach
                        </select>
                </div>

                <!-- Faculty Selection -->
                @if(Auth::user()->hasRole('admin'))
                <div class="w-65">
                    <label for="faculty" class="block text-gray-300 font-semibold mb-2">Select Faculty:</label>
                    <select name="faculty_id" id="faculty" class="form-select w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500" onchange="document.getElementById('filtersForm').submit()">
                        <option value="" {{ $selectedFacultyID ? '' : 'selected' }}>All Faculties</option>
                        @foreach($faculties as $faculty)
                            <option value="{{ $faculty->id }}" {{ $selectedFacultyID == $faculty->id ? 'selected' : '' }}>{{ $faculty->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Search Box for Admin -->
                <div class="w-64">
                    <label for="search" class="block text-gray-300 font-semibold mb-2">Search Program:</label>
                    <input type="text" id="search" 
                        class="form-input w-full rounded-md bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500" 
                        placeholder="Search by Program ID or Name..." onkeyup="filterPrograms()">
                </div>
                @endif
            </div>
            </form>

            @if(!$selectedBatchID)
                <p class="text-gray-400">Select a batch to view programs.</p>
            @elseif($programs->isEmpty())
                <p class="text-gray-400">No programs found for the selected batch or faculty.</p>
            @else
                <form action="{{ route('intakes.storeAll') }}" method="POST">
                    @csrf
                    <input type="hidden" name="batch_id" value="{{ $selectedBatchID }}">

                    <div class="overflow-x-auto">
                        <table id="programs-table" class="min-w-full leading-normal text-left bg-gray-800 rounded-lg overflow-hidden">
                            <thead>
                                <tr class="bg-gray-700 text-gray-300 uppercase text-sm tracking-wider">
                                    <th class="py-2 px-4 text-center">Program Code</th>
                                    <th class="py-2 px-4 text-center">Program Name</th>
                                    <th class="py-2 px-4 text-center">STPM / Matrik</th>
                                    <th class="py-2 px-4 text-center">STAM</th>
                                    <th class="py-2 px-4 text-center">Diploma Setaraf</th>
                                    <th class="py-2 px-4 text-center">Total</th>
                                    @if(Auth::user()->hasRole('faculty'))
                                        <th class="py-2 px-4 text-center">Action</th>
                                    @endif
                                </tr>
                            </thead>
                                    @php
                                        $totalStpm = 0;
                                        $totalStam = 0;
                                        $totalDiploma = 0;
                                        $grandTotal = 0;
                                    @endphp

                            <tbody class="bg-gray-800 text-gray-400">
                                @foreach($programs as $program)
                                <tr class="border-b border-gray-600 hover:bg-gray-700">
                                    <input type="hidden" name="intake[{{ $program->programID }}][program_id]" value="{{ $program->programID }}">

                                    <td class="py-2 px-4 text-center">{{ $program->programID }}</td>
                                    <td class="py-2 px-4">{{ $program->programName }}</td>
                                    <td class="py-2 px-4 text-center">
                                        <input type="number" name="intake[{{ $program->programID }}][stpm]" class="form-input bg-gray-700 text-gray-100 rounded-md w-full focus:border-blue-500 focus:ring-blue-500 text-center" placeholder="STPM Count" value="{{ $existingIntakes[$program->programID][1] ?? 0 }}" @if(Auth::user()->hasRole('admin')) readonly @endif required>
                                    </td>
                                    <td class="py-2 px-4 text-center">
                                        <input type="number" name="intake[{{ $program->programID }}][stam]" class="form-input bg-gray-700 text-gray-100 rounded-md w-full focus:border-blue-500 focus:ring-blue-500 text-center" placeholder="STAM Count" value="{{ $existingIntakes[$program->programID][2] ?? 0 }}" @if(Auth::user()->hasRole('admin')) readonly @endif required>
                                    </td>
                                    <td class="py-2 px-4 text-center">
                                        <input type="number" name="intake[{{ $program->programID }}][diploma]" class="form-input bg-gray-700 text-gray-100 rounded-md w-full focus:border-blue-500 focus:ring-blue-500 text-center" placeholder="Diploma Count" value="{{ $existingIntakes[$program->programID][3] ?? 0 }}" @if(Auth::user()->hasRole('admin')) readonly @endif required>
                                    </td>
                                    <td class="py-2 px-4 text-center">
                                        <span class="text-gray-100 font-semibold">{{ ($existingIntakes[$program->programID][1] ?? 0) + ($existingIntakes[$program->programID][2] ?? 0) + ($existingIntakes[$program->programID][3] ?? 0) }}</span>
                                    </td>
                                    @if(Auth::user()->hasRole('faculty'))
                                    <td class="py-2 px-4 text-center">
                                        <form action="{{ route('intakes.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="batch_id" value="{{ $selectedBatchID }}">
                                            <input type="hidden" name="program_id" value="{{ $program->programID }}">
                                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1 px-4 rounded-lg">
                                                Save
                                            </button>
                                        </form>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-700 text-gray-300 uppercase text-sm tracking-wider">
                                    <td colspan="2" class="py-2 px-4 font-bold text-right">Total</td>
                                    <td class="py-2 px-4 text-center" id="total-stpm">0</td>
                                    <td class="py-2 px-4 text-center" id="total-stam">0</td>
                                    <td class="py-2 px-4 text-center" id="total-diploma">0</td>
                                    <td class="py-2 px-4 text-center" id="grand-total">0</td>
                                    @if(Auth::user()->hasRole('faculty'))
                                    <td class="py-2 px-4 text-center">
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                                            Save All Intakes
                                        </button>
                                    </td>
                                    @endif
                                </tr>
                            </tfoot>

                        </table>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>





<script>
document.addEventListener('DOMContentLoaded', function () {
    // Dropdown filtering for faculty
    const facultyDropdown = document.getElementById('faculty_id');
    if (facultyDropdown) {
        facultyDropdown.addEventListener('change', function () {
            const batchDropdown = document.getElementById('batch');
            const batchId = batchDropdown ? batchDropdown.value : '';
            const facultyId = this.value;
            const url = new URL(window.location.href);

            // Update URL parameters
            url.searchParams.set('batch_id', batchId);
            url.searchParams.set('faculty_id', facultyId);

            // Reload page with updated parameters
            window.location.href = url.toString();
        });
    }

    // Totals calculation
    const calculateTotals = () => {
        let totalStpm = 0;
        let totalStam = 0;
        let totalDiploma = 0;
        let grandTotal = 0;

        const rows = document.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const stpm = parseInt(row.querySelector('input[name*="[stpm]"]').value) || 0;
            const stam = parseInt(row.querySelector('input[name*="[stam]"]').value) || 0;
            const diploma = parseInt(row.querySelector('input[name*="[diploma]"]').value) || 0;

            const programTotal = stpm + stam + diploma;
            totalStpm += stpm;
            totalStam += stam;
            totalDiploma += diploma;
            grandTotal += programTotal;

            row.querySelector('span').textContent = programTotal;
        });

        // Update footer totals
        const totalStpmElement = document.getElementById('total-stpm');
        if (totalStpmElement) totalStpmElement.textContent = totalStpm;

        const totalStamElement = document.getElementById('total-stam');
        if (totalStamElement) totalStamElement.textContent = totalStam;

        const totalDiplomaElement = document.getElementById('total-diploma');
        if (totalDiplomaElement) totalDiplomaElement.textContent = totalDiploma;

        const grandTotalElement = document.getElementById('grand-total');
        if (grandTotalElement) grandTotalElement.textContent = grandTotal;
    };

    document.querySelectorAll('input[type="number"]').forEach(input => {
        input.addEventListener('input', calculateTotals);
    });

    // Initial calculation
    calculateTotals();
});

// Search Functionality
function filterPrograms() {
    const input = document.getElementById("search");
    const filter = input.value.toLowerCase();
    const table = document.getElementById("programs-table");
    if (!table) return;

    const rows = table.getElementsByTagName("tr");

    for (let i = 1; i < rows.length; i++) {
        const programIDCell = rows[i].getElementsByTagName("td")[0];
        const programNameCell = rows[i].getElementsByTagName("td")[1];

        if (programIDCell && programNameCell) {
            const programID = programIDCell.textContent || programIDCell.innerText;
            const programName = programNameCell.textContent || programNameCell.innerText;

            if (programID.toLowerCase().includes(filter) || programName.toLowerCase().includes(filter)) {
                rows[i].style.display = ""; // Show row
            } else {
                rows[i].style.display = "none"; // Hide row
            }
        }
    }
}

</script>

