<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-100 leading-tight">
            {{ __('Edit User') }}
        </h2>
    </x-slot>

    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-extrabold mb-8 text-gray-200">Edit User Details</h1>

        <div class="bg-gray-900 p-6 rounded-lg shadow-lg">
            <form method="POST" action="{{ route('admin.updateUser', ['id' => $user->id]) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="name" class="block text-gray-300 font-semibold mb-2">Name:</label>
                    <input type="text" id="name" name="name" value="{{ $user->name }}" class="form-input w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500" required>
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-gray-300 font-semibold mb-2">Email:</label>
                    <input type="email" id="email" name="email" value="{{ $user->email }}" class="form-input w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500" required>
                </div>

                <div class="mb-4">
                    <label for="roles" class="block text-gray-300 font-semibold mb-2">Roles:</label>
                    <select id="roles" name="roles" class="form-select w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select a Role</option>
                        @foreach($allRoles as $role)
                        <option value="{{ $role->rolesID }}" {{ $user->roles->first() && $user->roles->first()->rolesID == $role->rolesID ? 'selected' : '' }}>{{ $role->type }}</option>
                        @endforeach
                    </select>
                </div>


                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">Update User</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
