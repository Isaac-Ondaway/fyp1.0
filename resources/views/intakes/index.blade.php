<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-100 leading-tight">
            {{ __('Manage Entry Levels and Intakes') }}
        </h2>
    </x-slot>

    <div class="container mx-auto py-8">
        <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
            <h3 class="text-3xl font-bold mb-8 text-white mb-4">Manage Intakes</h3>
            
            <!-- Batch Selection -->
            <div class="flex items-center mb-4 space-x-4">
                <div class="w-48">
                    <label for="batch" class="block text-gray-300 font-semibold mb-2">Select Batch:</label>
                    <form id="batchForm" action="{{ route('intakes.index') }}" method="GET">
                        <select name="batch_id" id="batch" class="form-select w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500" onchange="document.getElementById('batchForm').submit()">
                            <option value="" disabled {{ $selectedBatchID ? '' : 'selected' }}>Choose a Batch</option>
                            @foreach($batches as $batch)
                                <option value="{{ $batch->batchID }}" {{ $selectedBatchID == $batch->batchID ? 'selected' : '' }}>{{ $batch->batchName }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>

            @if(!$selectedBatchID)
                <p class="text-gray-400">Select a batch to view programs.</p>
            @elseif($programs->isEmpty())
                <p class="text-gray-400">No programs found for the selected batch.</p>
            @else
                <form action="{{ route('intakes.storeAll') }}" method="POST">
                    @csrf
                    <input type="hidden" name="batch_id" value="{{ $selectedBatchID }}">
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full leading-normal text-left bg-gray-800 rounded-lg overflow-hidden">
                            <thead>
                                <tr class="bg-gray-700 text-gray-300 uppercase text-sm tracking-wider">
                                    <th class="py-2 px-4 text-center">Program ID</th>
                                    <th class="py-2 px-4 text-center">Program Name</th>
                                    <th class="py-2 px-4 text-center">STPM / Matrik</th>
                                    <th class="py-2 px-4 text-center">STAM</th>
                                    <th class="py-2 px-4 text-center">Diploma Setaraf</th>
                                    <th class="py-2 px-4 text-center">Total</th>
                                    <th class="py-2 px-4 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-800 text-gray-400">
                                @php
                                    $totalStpm = 0;
                                    $totalStam = 0;
                                    $totalDiploma = 0;
                                    $grandTotal = 0;
                                @endphp

                                @foreach($programs as $program)
                                    @php
                                        $stpmCount = $existingIntakes[$program->programID][1] ?? 0;
                                        $stamCount = $existingIntakes[$program->programID][2] ?? 0;
                                        $diplomaCount = $existingIntakes[$program->programID][3] ?? 0;

                                        $programTotal = $stpmCount + $stamCount + $diplomaCount;
                                        $totalStpm += $stpmCount;
                                        $totalStam += $stamCount;
                                        $totalDiploma += $diplomaCount;
                                        $grandTotal += $programTotal;
                                    @endphp
                                    <tr class="border-b border-gray-600 hover:bg-gray-700">
                                        <input type="hidden" name="intake[{{ $program->programID }}][program_id]" value="{{ $program->programID }}">
                                        
                                        <td class="py-2 px-4 text-center">{{ $program->programID }}</td>
                                        <td class="py-2 px-4">{{ $program->programName }}</td>
                                        <td class="py-2 px-4 text-center">
                                            <input type="number" name="intake[{{ $program->programID }}][stpm]" class="form-input bg-gray-700 text-gray-100 rounded-md w-full focus:border-blue-500 focus:ring-blue-500 text-center" placeholder="STPM Count" value="{{ $stpmCount }}" required>
                                        </td>
                                        <td class="py-2 px-4 text-center">
                                            <input type="number" name="intake[{{ $program->programID }}][stam]" class="form-input bg-gray-700 text-gray-100 rounded-md w-full focus:border-blue-500 focus:ring-blue-500 text-center" placeholder="STAM Count" value="{{ $stamCount }}" required>
                                        </td>
                                        <td class="py-2 px-4 text-center">
                                            <input type="number" name="intake[{{ $program->programID }}][diploma]" class="form-input bg-gray-700 text-gray-100 rounded-md w-full focus:border-blue-500 focus:ring-blue-500 text-center" placeholder="Diploma Count" value="{{ $diplomaCount }}" required>
                                        </td>
                                        <td class="py-2 px-4 text-center">
                                            <span class="text-gray-100 font-semibold">{{ $programTotal }}</span>
                                        </td>
                                        <td class="py-2 px-4 text-center">
                                            <button type="submit" formaction="{{ route('intakes.store') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1 px-4 rounded-lg">
                                                Save
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-700 text-gray-300 uppercase text-sm tracking-wider">
                                    <td colspan="2" class="py-2 px-4 font-bold text-right">Total</td>
                                    <td class="py-2 px-4 text-center">{{ $totalStpm }}</td>
                                    <td class="py-2 px-4 text-center">{{ $totalStam }}</td>
                                    <td class="py-2 px-4 text-center">{{ $totalDiploma }}</td>
                                    <td class="py-2 px-4 text-center">{{ $grandTotal }}</td>
                                    <td class="py-2 px-4 text-center">
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                                            Save All Intakes
                                        </button>
                                    </td>
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
        const calculateTotals = () => {
            let totalStpm = 0;
            let totalStam = 0;
            let totalDiploma = 0;
            let grandTotal = 0;

            // Loop through each row in the table body
            document.querySelectorAll('tbody tr').forEach(row => {
                const stpm = parseInt(row.querySelector('input[name*="[stpm]"]').value) || 0;
                const stam = parseInt(row.querySelector('input[name*="[stam]"]').value) || 0;
                const diploma = parseInt(row.querySelector('input[name*="[diploma]"]').value) || 0;

                const programTotal = stpm + stam + diploma;

                // Accumulate totals for the footer
                totalStpm += stpm;
                totalStam += stam;
                totalDiploma += diploma;
                grandTotal += programTotal;

                // Update the total span in each row
                row.querySelector('span').textContent = programTotal;
            });

            // Update footer totals
            document.getElementById('total-stpm').textContent = totalStpm;
            document.getElementById('total-stam').textContent = totalStam;
            document.getElementById('total-diploma').textContent = totalDiploma;
            document.getElementById('grand-total').textContent = grandTotal;
        };

        // Add event listeners to all intake input fields
        document.querySelectorAll('input[type="number"]').forEach(input => {
            input.addEventListener('input', calculateTotals);
        });

        // Initial calculation on page load
        calculateTotals();
    });
</script>

