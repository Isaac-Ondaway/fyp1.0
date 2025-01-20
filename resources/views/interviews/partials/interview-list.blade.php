@if($interviews->isEmpty())
    <p class="text-gray-200">No interviews found for the selected batch or program.</p>
@else
    @foreach($interviews->groupBy('programID') as $programID => $programInterviews)
        <div class="bg-gray-800 border border-gray-700 rounded-lg shadow-lg p-6 mb-6">
            <h4 class="text-lg font-bold text-gray-200 mb-4">{{ $programInterviews->first()->program->programName ?? 'Unknown Program' }}</h4>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left table-fixed border-collapse">
                    <thead>
                        <tr class="text-gray-300 uppercase text-sm tracking-wider border-b border-gray-700">
                            <th class="w-1/5 py-2 px-4">Name</th>
                            <th class="w-1/5 py-2 px-4">Contact Number</th>
                            <th class="w-1/4 py-2 px-4">Email</th>
                            <th class="w-1/5 py-2 px-4">Interview Date</th>
                            <th class="w-1/6 py-2 px-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-400 divide-y divide-gray-700">
                        @foreach($programInterviews as $interview)
                        <tr class="hover:bg-gray-700" onclick="openInterviewModal({{ json_encode([
    'id' => $interview->interviewID,
    'name' => $interview->intervieweeName,
    'contact_number' => $interview->contactNumber,
    'email' => $interview->email
]) }})">
                                <td class="py-3 px-4 truncate whitespace-normal">{{ $interview->intervieweeName }}</td>
                                <td class="py-3 px-4 whitespace-nowrap">{{ $interview->contactNumber }}</td>
                                <td class="py-3 px-4 truncate whitespace-normal">{{ $interview->email ?? 'No Email' }}</td>
                                <td class="py-3 px-4 whitespace-nowrap">{{ $interview->interviewSchedule->scheduled_date ?? 'Not Scheduled' }}</td>
                                <td class="py-3 px-4 flex space-x-2">
                                <a href="{{ route('interviews-schedule.create', ['interviewee_id' => $interview->interviewID]) }}" 
                                       class="bg-blue-600 text-white py-1 px-4 rounded-lg hover:bg-blue-700 transition duration-200 shadow-sm " onclick="event.stopPropagation()">
                                        Schedule
                                    </a>
                                    <form action="{{ route('interviews.destroy', ['interview' => $interview->interviewID]) }}" method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this interview?');"
                                          onclick="event.stopPropagation()">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="bg-red-600 text-white py-1 px-4 rounded-lg hover:bg-red-700 transition duration-200 shadow-sm">
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
    @endforeach
@endif


