<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<x-app-layout>
    <!-- Container -->
    <div class="container mx-auto p-6 bg-gray-900 rounded-lg shadow-lg">
        <h1 class="text-3xl font-bold text-white mb-6">Combined Reports</h1>

        <!-- Batch Intake Report -->
        <div class="mb-10">
            <h2 class="text-2xl font-semibold text-white mb-4">Batch Intake Report</h2>
            <p class="text-gray-300">Current Batch: {{ $currentBatch->batchName ?? 'N/A' }}</p>
            <p class="text-gray-300">Total Intakes: {{ $currentBatchIntake }}</p>

            <!-- Batch Intake Bar Chart -->
            <div class="mt-6">
                <h3 class="text-lg font-medium text-white mb-2">Batch Intake Chart</h3>
                <div style="max-width: 700px; margin: 0 auto;">
                    <canvas id="batchIntakeChart" width="700" height="400"></canvas>
                </div>
            </div>
        </div>

        <!-- Program Status by Faculty -->
        <div>
            <h2 class="text-2xl font-semibold text-white mb-4">Program Status by Faculty (Newest Batch)</h2>
            <div class="flex flex-wrap gap-6 justify-start">
                @foreach ($programStatusCounts as $facultyID => $statuses)
                    @php
                        $faculty = $faculties->firstWhere('id', $facultyID);
                    @endphp
                    <div style="max-width: 300px; text-align: center;">
                        <h3 class="text-lg font-medium text-gray-300 mb-2">{{ $faculty->name ?? 'Unknown' }}</h3>
                        <canvas id="facultyPieChart-{{ $facultyID }}" width="300" height="300"></canvas>
                    </div>
                    <script>
                        const ctx{{ $facultyID }} = document.getElementById('facultyPieChart-{{ $facultyID }}')?.getContext('2d');
                        if (ctx{{ $facultyID }}) {
                            new Chart(ctx{{ $facultyID }}, {
                                type: 'pie',
                                data: {
                                    labels: ['Pending', 'Approved', 'Rejected'],
                                    datasets: [{
                                        data: [
                                            {{ $statuses->where('programStatus', 'Pending')->sum('count') }},
                                            {{ $statuses->where('programStatus', 'Approved')->sum('count') }},
                                            {{ $statuses->where('programStatus', 'Rejected')->sum('count') }}
                                        ],
                                        backgroundColor: [
                                            'rgba(255, 206, 86, 0.7)', // Yellow for Pending
                                            'rgba(75, 192, 192, 0.7)', // Green for Approved
                                            'rgba(255, 99, 132, 0.7)'  // Red for Rejected
                                        ],
                                        borderColor: [
                                            'rgba(255, 206, 86, 1)',
                                            'rgba(75, 192, 192, 1)',
                                            'rgba(255, 99, 132, 1)'
                                        ],
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: true,
                                    plugins: {
                                        legend: {
                                            labels: {
                                                color: '#FFFFFF', // White text for dark background
                                                font: {
                                                    size: 14 // Adjust font size
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        }
                    </script>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Batch Intake Bar Chart Script -->
    <script>
        const batchLabels = @json($batchEntryLevelData->pluck('batchName'));
        const batchSTPM = @json($batchEntryLevelData->pluck('entryLevels.STPM'));
        const batchSTAM = @json($batchEntryLevelData->pluck('entryLevels.STAM'));
        const batchDiploma = @json($batchEntryLevelData->pluck('entryLevels.Diploma'));

        const batchCtx = document.getElementById('batchIntakeChart')?.getContext('2d');
        if (batchCtx) {
            new Chart(batchCtx, {
                type: 'bar',
                data: {
                    labels: batchLabels,
                    datasets: [
                        {
                            label: 'STPM',
                            data: batchSTPM,
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 2
                        },
                        {
                            label: 'STAM',
                            data: batchSTAM,
                            backgroundColor: 'rgba(255, 206, 86, 0.6)',
                            borderColor: 'rgba(255, 206, 86, 1)',
                            borderWidth: 2
                        },
                        {
                            label: 'Diploma',
                            data: batchDiploma,
                            backgroundColor: 'rgba(75, 192, 192, 0.6)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                color: '#FFFFFF'
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: '#444444'
                            },
                            ticks: {
                                color: '#FFFFFF'
                            }
                        },
                        y: {
                            grid: {
                                color: '#444444'
                            },
                            ticks: {
                                color: '#FFFFFF'
                            }
                        }
                    }
                }
            });
        }
    </script>
</x-app-layout>
