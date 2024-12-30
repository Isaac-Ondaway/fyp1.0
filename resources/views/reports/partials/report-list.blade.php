<!-- Programs Section -->
@foreach ($programs as $program)
    <div id="programsSection" class="program-page bg-white p-6 rounded-lg shadow-lg border border-gray-300 mb-8">
        <!-- Program Title -->
        <h2 class="text-2xl font-bold text-gray-800 mb-4">
            {{ $program->programID }} - {{ $program->programName }}
        </h2>
        <p>Faculty: {{ $program->faculty->name ?? 'N/A' }}</p>
        <p>Batch: {{ $program->batch->batchName ?? 'N/A' }}</p>

        <!-- Basic Information -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-blue-500 mb-2">Basic Information</h3>
            <div class="grid grid-cols-2 gap-4 text-gray-700">
                <p><strong>Program ID:</strong> {{ $program->programID }}</p>
                <p><strong>Program Name:</strong> {{ $program->programName }}</p>
                <p><strong>Total Semesters:</strong> {{ $program->programSem }}</p>
                <p><strong>Level of Education:</strong> {{ $program->levelEdu }}</p>
                <p><strong>NEC:</strong> {{ $program->NEC }}</p>
                <p><strong>Study Program:</strong> {{ $program->studyProgram ?? 'N/A' }}</p>
                <p><strong>Program Status:</strong> {{ $program->programStatus }}</p>
            </div>
        </div>

        <!-- Program Fee and Description -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-blue-500 mb-2">Program Fee and Description</h3>
            <div class="grid grid-cols-2 gap-4 text-gray-700">
                <p><strong>Program Fee:</strong> {{ $program->programFee }}</p>
                <p><strong>Description:</strong> {{ $program->programDesc }}</p>
            </div>
        </div>

        <!-- Additional Criteria -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-blue-500 mb-2">Additional Criteria</h3>
            <div class="grid grid-cols-2 gap-4 text-gray-700">
                <p><strong>Interview Exam:</strong> {{ $program->isInterviewExam ? 'Yes' : 'No' }}</p>
                <p><strong>Rayuan:</strong> {{ $program->isRayuan ? 'Yes' : 'No' }}</p>
                <p><strong>Ujian Medsi:</strong> {{ $program->isUjianMedsi ? 'Yes' : 'No' }}</p>
                <p><strong>D-Degree:</strong> {{ $program->isDDegree ? 'Yes' : 'No' }}</p>
                <p><strong>Learn Mode:</strong> {{ $program->learnMod ? 'Yes' : 'No' }}</p>
            </div>
        </div>

        <!-- Eligibility -->
        <div>
            <h3 class="text-lg font-semibold text-blue-500 mb-2">Eligibility</h3>
            <div class="grid grid-cols-2 gap-4 text-gray-700">
                <p><strong>Bumiputera:</strong> {{ $program->isBumiputera ? 'Yes' : 'No' }}</p>
                <p><strong>TEVT:</strong> {{ $program->isTEVT ? 'Yes' : 'No' }}</p>
                <p><strong>Kompetitif:</strong> {{ $program->isKompetitif ? 'Yes' : 'No' }}</p>
                <p><strong>BTECH:</strong> {{ $program->isBTECH ? 'Yes' : 'No' }}</p>
                <p><strong>OKU:</strong> {{ $program->isOKU ? 'Yes' : 'No' }}</p>
            </div>
        </div>
    </div>
    <!-- Page Break -->
    <div style="page-break-after: always;"></div>
@endforeach

<!-- Intake Count Summary -->
<div id="intakeSummarySection" style="text-align: center; margin-bottom: 20px;">
    <h2 style="padding: 15px 0; font-family: Arial, sans-serif; font-size: 24px; color: #ffffff;">Intake Count Summary</h2>


<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 14px; background-color: #1f2937; color: #e5e7eb;">
    <thead>
        <tr style="background-color: #111827; color: #ffffff; text-align: left;">
            <th style="padding: 15px; border: 1px solid #4b5563;">Program Code</th>
            <th style="padding: 15px; border: 1px solid #4b5563;">Program Name</th>
            <th style="padding: 15px; border: 1px solid #4b5563;">STPM / MATRIK</th>
            <th style="padding: 15px; border: 1px solid #4b5563;">STAM</th>
            <th style="padding: 15px; border: 1px solid #4b5563;">Diploma Setaraf</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($intakeCounts as $intake)
        <tr style="text-align: left; background-color: {{ $loop->index % 2 == 0 ? '#374151' : '#1f2937' }};">
            <td style="padding: 15px; border: 1px solid #4b5563;">{{ $intake->program_id }}</td>
            <td style="padding: 15px; border: 1px solid #4b5563;">{{ $programs->firstWhere('programID', $intake->program_id)->programName ?? 'N/A' }}</td>
            <td style="padding: 15px; border: 1px solid #4b5563; text-align: center;">{{ $intake->STPM }}</td>
            <td style="padding: 15px; border: 1px solid #4b5563; text-align: center;">{{ $intake->STAM }}</td>
            <td style="padding: 15px; border: 1px solid #4b5563; text-align: center;">{{ $intake->Diploma }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

    <!-- Page Break -->
    <div style="page-break-after: always;"></div>

<!-- Entry Levels Section -->
<div id="entryLevelsSection" class="text-center mb-6">
    <h2 style="padding: 25px 0;" class="text-2xl font-bold text-white">Entry Levels</h2>
<!-- First Table (First 14 Columns) -->
<div class="overflow-x-auto mb-6">
    <table class="w-full border-collapse border border-gray-800 text-sm text-gray-300 bg-gray-900">
        <thead class="bg-gray-800">
            <tr>
                <th class="border border-gray-800 px-6 py-3 text-left">Program ID</th>
                <th class="border border-gray-800 px-6 py-3 text-left">Program Name</th>
                @foreach ($categories->slice(0, 11) as $category)
                    <th class="border border-gray-800 px-6 py-3 text-center">{{ $category->categoryName }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($programs as $program)
                <tr>
                    <td class="border border-gray-800 px-6 py-3">{{ $program->programID }}</td>
                    <td class="border border-gray-800 px-6 py-3">{{ $program->programName }}</td>
                    @foreach ($categories->slice(0, 11) as $category)
                        <td class="border border-gray-800 px-6 py-3 text-center">
                            <input 
                                type="checkbox" 
                                name="entry_levels[{{ $program->programID }}][{{ $category->entryLevelCategoryID }}]"
                                value="1"
                                class="form-checkbox h-5 w-5 text-blue-500 border-gray-700 focus:ring focus:ring-blue-600"
                                {{ 
                                    isset($programEntryLevels[$program->programID]) &&
                                    collect($programEntryLevels[$program->programID])
                                        ->where('entry_level_category_id', $category->entryLevelCategoryID)
                                        ->where('is_offered', 1)
                                        ->isNotEmpty()
                                    ? 'checked' : '' 
                                }}
                                {{ auth()->user()->hasRole('admin') ? 'disabled' : '' }}
                            >
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Second Table (Remaining Columns) -->
<div class="overflow-x-auto">
    <table class="w-full border-collapse border border-gray-800 text-sm text-gray-300 bg-gray-900">
        <thead class="bg-gray-800">
            <tr>
                <th class="border border-gray-800 px-6 py-3 text-left">Program ID</th>
                <th class="border border-gray-800 px-6 py-3 text-left">Program Name</th>
                @foreach ($categories->slice(13) as $category)
                    <th class="border border-gray-800 px-6 py-3 text-center">{{ $category->categoryName }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($programs as $program)
                <tr>
                    <td class="border border-gray-800 px-6 py-3">{{ $program->programID }}</td>
                    <td class="border border-gray-800 px-6 py-3">{{ $program->programName }}</td>
                    @foreach ($categories->slice(13) as $category)
                        <td class="border border-gray-800 px-6 py-3 text-center">
                            <input 
                                type="checkbox" 
                                name="entry_levels[{{ $program->programID }}][{{ $category->entryLevelCategoryID }}]"
                                value="1"
                                class="form-checkbox h-5 w-5 text-blue-500 border-gray-700 focus:ring focus:ring-blue-600"
                                {{ 
                                    isset($programEntryLevels[$program->programID]) &&
                                    collect($programEntryLevels[$program->programID])
                                        ->where('entry_level_category_id', $category->entryLevelCategoryID)
                                        ->where('is_offered', 1)
                                        ->isNotEmpty()
                                    ? 'checked' : '' 
                                }}
                                {{ auth()->user()->hasRole('admin') ? 'disabled' : '' }}
                            >
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>


    <!-- <div id="entryLevelsSection"class="text-center mb-6">
    <h2 class="text-2xl font-bold text-white">Entry Levels</h2>
</div>

<div class="overflow-x-auto">
    <table class="w-full border-collapse border border-gray-800 text-sm text-gray-300 bg-gray-900">
        <thead class="bg-gray-800">
            <tr>
                <th class="border border-gray-800 px-6 py-3 text-left">Program ID</th>
                <th class="border border-gray-800 px-6 py-3 text-left">Program Name</th>
                @foreach ($categories as $category)
                    <th class="border border-gray-800 px-6 py-3 text-center">{{ $category->categoryName }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($programs as $program)
                <tr class="hover:bg-gray-700 transition duration-200">
                    <td class="border border-gray-800 px-6 py-3">{{ $program->programID }}</td>
                    <td class="border border-gray-800 px-6 py-3">{{ $program->programName }}</td>
                    @foreach ($categories as $category)
                        <td class="border border-gray-800 px-6 py-3 text-center">
                            <input 
                                type="checkbox" 
                                name="entry_levels[{{ $program->programID }}][{{ $category->entryLevelCategoryID }}]"
                                value="1"
                                class="form-checkbox h-5 w-5 text-blue-500 border-gray-700 focus:ring focus:ring-blue-600"
                                {{ 
                                    isset($programEntryLevels[$program->programID]) &&
                                    collect($programEntryLevels[$program->programID])
                                        ->where('entry_level_category_id', $category->entryLevelCategoryID)
                                        ->where('is_offered', 1)
                                        ->isNotEmpty()
                                    ? 'checked' : '' 
                                }}
                                {{ auth()->user()->hasRole('admin') ? 'disabled' : '' }}
                            >
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div> -->



</div>