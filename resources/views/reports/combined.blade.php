<!-- Include Necessary Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="{{ asset('js/printReport.js') }}"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">

<x-app-layout>
    <!-- Container -->
    <div class="container mx-auto p-6 bg-gray-900 rounded-lg shadow-lg">
        <h1 class="text-3xl font-bold text-white mb-6">Combined Reports</h1>

        <!-- Batch Intake Report -->
        <div class="mb-10">
            <h2 class="text-2xl font-semibold text-white mb-4">Batch Intake Report</h2>
            <p class="text-gray-300">Current Batch: {{ $currentBatch->batchName ?? 'N/A' }}</p>

            <!-- Faculty Filter -->
            <div class="mt-4">
                <label for="facultyFilter" class="text-white">Filter by Faculty:</label>
                <select id="facultyFilter" class="bg-gray-800 text-white p-2 rounded-md">
                    <option value="all">All Faculties</option>
                    @foreach ($faculties as $faculty)
                        <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Batch Intake Bar Chart -->
            <div class="mt-6">
                <h3 class="text-lg font-medium text-white mb-2">Batch Intake Chart</h3>
                <div style="max-width: 700px; margin: 0 auto;">
                    <canvas id="batchIntakeChart" width="700" height="400"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="container mx-auto flex flex-wrap gap-6 items-center bg-gray-900 p-6 rounded-lg shadow-md">
    <!-- Batch Filter -->
    <div class="flex flex-col">
        <label for="batchID" class="text-sm font-medium text-gray-300 mb-1">Filter by Batch:</label>
        <select name="batchID" id="batchID" class="bg-gray-700 text-white rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">All Batches</option>
            @foreach ($batches as $batch)
                <option value="{{ $batch->batchID }}" data-batch-id="{{ $batch->batchID }}">{{ $batch->batchName }}</option>
            @endforeach
        </select>
    </div>


    <!-- Faculty Filter -->
    @if(auth()->check() && auth()->user()->hasRole('admin'))
    <div class="flex flex-col">
        <label for="facultyID" class="text-sm font-medium text-gray-300 mb-1">Filter by Faculty:</label>
        <select name="facultyID" id="facultyID" class="bg-gray-700 text-white rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">All Faculties</option>
            @foreach ($faculties as $faculty)
                <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
            @endforeach
        </select>
    </div>
    @endif

    <!-- Print and PDF Buttons -->
    <div class="flex flex-col mt-6">
    <button onclick="printReport()" class="btn btn-secondary px-4 py-2 bg-green-700 text-white rounded-md ml-4">
            Print Report
        </button>
    </div>
</div>


<!-- Placeholder for Filtered Programs -->
<div id="reportSection" class="container mx-auto p-6 bg-gray-900 rounded-lg shadow-lg">
    <div id="programList">
        @include('reports.partials.report-list', ['programs' => $programs, 'intakeCounts' => $intakeCounts, 'categories' => $categories, 'programEntryLevels' => $programEntryLevels])
    </div>
</div>

    <!-- Print Function Script -->
    <script>
function printReport() {
    const reportSection = document.getElementById('reportSection');
    if (!reportSection) {
        console.error('Error: reportSection element not found.');
        alert('Error: Unable to find the content to print.');
        return;
    }

    const printContents = `
    <style>
        @media print {
            body {
                font-size: 12px;
            }
            .program-section {
                padding: 5px;
                font-size: 12px;
            }
            .container {
                width: 100%;
                padding: 0;
            }
            .btn, .btn-secondary, select, #facultyFilter, #batchID {
                display: none;
            }
            .bg-gray-900 {
                background-color: white !important;
            }
            h1, h2 {
                margin: 0;
                color: #000 !important; /* Ensure all text is black */
            }
            #intake-summary-title {
                color: #000 !important; /* Specifically for "Intake Count Summary" */
                font-weight: bold;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th, td {
                border: 1px solid #000;
                padding: 8px;
                text-align: center;
                color: #000 !important;
            }
            th {
                background-color: #ddd;
                font-weight: bold;
                color: #000 !important;
            }
            td {
                background-color: #fff;
                color: #000 !important;
            }

             /* Rotate only the Entry Levels section to landscape */
            #entryLevelsSection {
                page-break-before: always;
                page: entry-levels-page;
            }

            @page entry-levels-page {
                size: landscape;
            }

            /* Ensure table adjusts properly */
            #entryLevelsSection table {
                width: 100%; /* Ensure full width in landscape */
                font-size: 10px; /* Adjust font size for better fit */
                table-layout: fixed; /* Ensure table columns adjust properly */
            }

            #entryLevelsSection th,
            #entryLevelsSection td {
                word-wrap: break-word; /* Wrap text in columns */
                padding: 8px;
            }

            
        }
    </style>
    ${reportSection.innerHTML}
    `;
    const originalContents = document.body.innerHTML;

    // Temporarily replace the body content for printing
    document.body.innerHTML = printContents;
    window.print();

    // Restore the original content
    document.body.innerHTML = originalContents;

    // Reload the page to ensure everything is restored
    location.reload();
}
</script>


    <!-- Chart Initialization Script -->
    <script>
    // Original Data
    const batchLabels = @json($batchEntryLevelData->pluck('batchName'));
    const originalBatchData = @json($batchEntryLevelData->pluck('entryLevels'));
    const facultyData = @json($facultyBatchData);

    let batchChart;

    // Helper function to sort data by batchStartDate
    function sortDataByBatchStartDate(data, batchNames) {
        const sortedData = data
            .map((entry, index) => ({
                batchName: batchNames[index],
                entryLevels: entry,
                batchStartDate: new Date(entry.batchStartDate), // Ensure you have batchStartDate in your data
            }))
            .sort((a, b) => a.batchStartDate - b.batchStartDate); // Sort ascending by batchStartDate

        return {
            labels: sortedData.map(item => item.batchName),
            data: sortedData.map(item => item.entryLevels),
        };
    }

    // Update chart with sorted and limited data
    function updateBatchChart(data, labels) {
        const chartData = {
            labels: labels,
            datasets: [
                { label: 'STPM', data: data.map(batch => batch.STPM || 0), backgroundColor: 'rgba(54, 162, 235, 0.6)', borderColor: 'rgba(54, 162, 235, 1)', borderWidth: 2 },
                { label: 'STAM', data: data.map(batch => batch.STAM || 0), backgroundColor: 'rgba(255, 206, 86, 0.6)', borderColor: 'rgba(255, 206, 86, 1)', borderWidth: 2 },
                { label: 'Diploma', data: data.map(batch => batch.Diploma || 0), backgroundColor: 'rgba(75, 192, 192, 0.6)', borderColor: 'rgba(75, 192, 192, 1)', borderWidth: 2 }
            ]
        };

        if (batchChart) {
            batchChart.data.labels = labels;
            batchChart.data.datasets = chartData.datasets;
            batchChart.update();
        } else {
            const batchCtx = document.getElementById('batchIntakeChart')?.getContext('2d');
            batchChart = new Chart(batchCtx, {
                type: 'bar',
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { labels: { color: '#FFFFFF', font: { size: 14 } } },
                        datalabels: {
                            color: '#FFFFFF',
                            anchor: 'end',
                            align: 'start',
                            offset: 5,
                            font: { weight: 'bold', size: 12 },
                            formatter: value => (value > 0 ? value : '')
                        }
                    },
                    scales: {
                        x: { grid: { color: '#444444' }, ticks: { color: '#FFFFFF', maxRotation: 45, minRotation: 0, padding: 10 } },
                        y: { grid: { color: '#444444' }, ticks: { color: '#FFFFFF', padding: 10 } }
                    }
                },
                plugins: [ChartDataLabels]
            });
        }
    }

    // Sorting the original data by batchStartDate and limiting to the latest 2 batches
    const { labels: sortedLabels, data: sortedData } = sortDataByBatchStartDate(originalBatchData, batchLabels);

    // Get the latest 2 batches
    const latestBatches = {
        labels: sortedLabels.slice(-2), // Last 2 batches
        data: sortedData.slice(-2) // Corresponding entryLevels
    };

    // Update chart on page load with the latest 2 batches
    updateBatchChart(latestBatches.data, latestBatches.labels);

    document.getElementById('facultyFilter').addEventListener('change', (event) => {
        const facultyID = event.target.value;

        if (facultyID === 'all') {
        // Show all batches with correct order
        updateBatchChart(latestBatches.data, latestBatches.labels);
    } else {
        const filteredData = facultyData[facultyID] || [];

        // Match the batch labels with the filtered data
        const alignedData = latestBatches.labels.map(label => {
            const batchData = filteredData.find(batch => batch.batchName === label);
            return batchData ? batchData.entryLevels : { STPM: 0, STAM: 0, Diploma: 0 };
        });

        updateBatchChart(alignedData, latestBatches.labels);
    }
});
</script>


    <!-- AJAX Filtering Script -->
    <script>
document.addEventListener('DOMContentLoaded', function () {
    const batchFilter = document.getElementById('batchID');
    const facultyFilter = document.getElementById('facultyID');

    function fetchFilteredPrograms() {
        const batchID = batchFilter.value;
        const facultyID = facultyFilter ? facultyFilter.value : '';

        // Send AJAX request
        fetch(`{{ route('reports.combined') }}?batchID=${batchID}&facultyID=${facultyID}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
            },
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch data');
                }
                return response.json();
            })
            .then(data => {
                if (data.html) {
                    // Update the program list with the rendered HTML
                    document.getElementById('programList').innerHTML = data.html;
                } else {
                    console.error('Invalid response data:', data);
                }
            })
            .catch(error => console.error('Error fetching programs:', error));
    }

    // Event listeners for filters
    batchFilter.addEventListener('change', fetchFilteredPrograms);
    if (facultyFilter) {
        facultyFilter.addEventListener('change', fetchFilteredPrograms);
    }
});

    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
    const batchSelect = document.getElementById('batchID');
    const options = Array.from(batchSelect.options); // Convert options to an array

    // Sort options in descending order based on data-batch-id
    const sortedOptions = options.slice(1).sort((a, b) => {
        return b.getAttribute('data-batch-id') - a.getAttribute('data-batch-id');
    });

    // Clear and re-add the options
    batchSelect.innerHTML = '';
    batchSelect.appendChild(options[0]); // Append the "All Batches" option
    sortedOptions.forEach(option => batchSelect.appendChild(option));
});
</script>

</x-app-layout>
