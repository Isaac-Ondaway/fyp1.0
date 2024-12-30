<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-100 leading-tight">
            {{ __('Users') }}
        </h2>
    </x-slot>
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-extrabold mb-8 text-gray-200">User List</h1>

<!-- Filter Section -->
<form method="GET" action="{{ route('admin.user.list') }}">
    <div class="flex items-center justify-between mb-4">
        <!-- Filter Dropdown -->
        <div class="flex items-center space-x-4">
            <div class="w-48">
                <label for="roleFilter" class="block text-gray-300 font-semibold mb-2">Filter by Role:</label>
                <select id="roleFilter" name="roleID" class="form-select w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->rolesID }}" {{ $roleID == $role->rolesID ? 'selected' : '' }}>{{ $role->type }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg mt-6">Apply Filters</button>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="w-1/6">
            <label for="search-name" class="block text-gray-300 font-semibold mb-2">Search by Name:</label>
            <input
                type="text"
                id="search-name"
                class="form-input w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                placeholder="Search by Name"
                onkeyup="filterUsers()"
            />
        </div>
    </div>
</form>



        <!-- User List -->
        <div class="overflow-hidden shadow-lg rounded-lg bg-gray-900">
        <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-100 bg-gray-600 p-3 rounded-t-lg">
                    {{ $roleID ? $roles->firstWhere('rolesID', $roleID)->type . ' ' : 'All Users' }}
                    </h2>
            <div class="bg-gray-800 p-4 rounded-lg mb-3">
                <div class="overflow-x-auto">
                    <table class="min-w-full mt-2 leading-normal text-left">
                        <thead>
                            <tr class="bg-gray-700 text-gray-300 uppercase text-sm tracking-wider">
                                <th class="py-2 px-4">User ID</th>
                                <th class="py-2 px-4">Name</th>
                                <th class="py-2 px-4">Email</th>
                                <th class="py-2 px-4">Faculty</th>
                                <th class="py-2 px-4">Roles</th>
                                <th class="py-2 px-4 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-800 text-gray-400">
                            @foreach($users as $user)
                                <tr class="border-b border-gray-600 hover:bg-gray-700">
                                    <td class="py-2 px-4">{{ $user->id }}</td>
                                    <td class="py-2 px-4">{{ $user->name }}</td>
                                    <td class="py-2 px-4">{{ $user->email }}</td>
                                    <td class="py-2 px-4">
                                        {{ $user->faculty ? $user->faculty->name : 'Not Assigned' }}

                                    </td>
                                    <td class="py-2 px-4">
                                        @foreach($user->roles as $role)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $role->type == 'Admin' ? 'bg-blue-100 text-blue-900' : ($role->type == 'Faculty' ? 'bg-green-100 text-green-900' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ $role->type }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td class="py-2 px-4 text-center">
                                        <a href="{{ route('admin.editUser', ['id' => $user->id]) }}" class="text-white bg-blue-500 hover:bg-blue-700 font-bold py-1 px-3 rounded-lg">Edit</a>
                                        <form action="{{ route('admin.deleteUser', ['id' => $user->id]) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-white bg-red-500 hover:bg-red-700 font-bold py-1 px-3 rounded-lg">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="text-center mb-4">
    <form action="{{ route('admin.generate.standaloneInvite') }}" method="POST">
        @csrf
        <button type="submit" class="text-white bg-purple-500 hover:bg-purple-700 font-bold py-2 px-4 rounded-lg">
            Generate Registration Token for Outsider
        </button>
    </form>
</div>



    </div>

<script>
function filterUsers() {
    const input = document.getElementById("search-name");
    const filter = input.value.toLowerCase();
    const table = document.querySelector("table");
    const rows = table.getElementsByTagName("tr");

    for (let i = 1; i < rows.length; i++) {
        const nameCell = rows[i].getElementsByTagName("td")[1]; // Assuming Name is the 2nd column
        if (nameCell) {
            const name = nameCell.textContent || nameCell.innerText;
            if (name.toLowerCase().indexOf(filter) > -1) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }
        }
    }
}

</script>

</x-app-layout>
