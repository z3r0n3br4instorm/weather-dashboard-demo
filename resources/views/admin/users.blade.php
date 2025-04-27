@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-2xl font-bold mb-6">User Management</h1>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Users List -->
                    <div class="md:col-span-2">
                        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                            <h2 class="text-lg font-semibold mb-4">Admin Users</h2>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white">
                                    <thead>
                                        <tr>
                                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-sm font-semibold text-gray-600">Name</th>
                                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-sm font-semibold text-gray-600">Email</th>
                                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-sm font-semibold text-gray-600">Role</th>
                                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-sm font-semibold text-gray-600">Created</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($users as $user)
                                            <tr>
                                                <td class="py-2 px-4 border-b border-gray-200">{{ $user->name }}</td>
                                                <td class="py-2 px-4 border-b border-gray-200">{{ $user->email }}</td>
                                                <td class="py-2 px-4 border-b border-gray-200">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->role === 'system_admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                                        {{ $user->role === 'system_admin' ? 'System Admin' : 'Admin' }}
                                                    </span>
                                                </td>
                                                <td class="py-2 px-4 border-b border-gray-200">{{ $user->created_at->format('Y-m-d') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="py-4 px-4 border-b border-gray-200 text-center text-gray-500">
                                                    No users found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Add New User Form -->
                    <div class="md:col-span-1">
                        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                            <h2 class="text-lg font-semibold mb-4">Add New User</h2>
                            
                            <form action="{{ route('admin.users.create') }}" method="POST">
                                @csrf
                                
                                <div class="mb-4">
                                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">
                                        Name *
                                    </label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror" required>
                                    @error('name')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="mb-4">
                                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
                                        Email *
                                    </label>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('email') border-red-500 @enderror" required>
                                    @error('email')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="mb-4">
                                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                                        Password *
                                    </label>
                                    <input type="password" name="password" id="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('password') border-red-500 @enderror" required>
                                    @error('password')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="mb-4">
                                    <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">
                                        Confirm Password *
                                    </label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                </div>
                                
                                <div class="mb-6">
                                    <label for="role" class="block text-gray-700 text-sm font-bold mb-2">
                                        Role *
                                    </label>
                                    <select name="role" id="role" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('role') border-red-500 @enderror" required>
                                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="system_admin" {{ old('role') == 'system_admin' ? 'selected' : '' }}>System Admin</option>
                                    </select>
                                    @error('role')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                                        Create User
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Role Information -->
                <div class="mt-6 bg-white p-4 rounded-lg shadow border border-gray-200">
                    <h2 class="text-lg font-semibold mb-4">User Roles</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-blue-800">Admin</h3>
                            <p class="text-sm text-gray-600 mt-2">
                                Admins can manage sensors, view data, adjust alert thresholds, and run data simulations. They cannot manage other admin users.
                            </p>
                            <ul class="list-disc pl-5 mt-2 text-sm text-gray-600">
                                <li>Manage sensors</li>
                                <li>View dashboards and reports</li>
                                <li>Configure alert thresholds</li>
                                <li>Run data simulations</li>
                            </ul>
                        </div>
                        
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-purple-800">System Admin</h3>
                            <p class="text-sm text-gray-600 mt-2">
                                System Admins have all the capabilities of regular admins, plus the ability to manage users.
                            </p>
                            <ul class="list-disc pl-5 mt-2 text-sm text-gray-600">
                                <li>All Admin capabilities</li>
                                <li>Create and manage user accounts</li>
                                <li>Advanced system configuration</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 