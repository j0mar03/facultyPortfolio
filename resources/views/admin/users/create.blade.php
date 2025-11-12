<x-app-layout>
	<x-slot name="header">
		<div class="flex justify-between items-center">
			<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
				{{ __('Create User') }}
			</h2>
			<a href="{{ route('admin.users.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">← Back to Users</a>
		</div>
	</x-slot>

	<div class="py-12">
		<div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
				<form method="POST" action="{{ route('admin.users.store') }}">
					@csrf
					<div class="space-y-6">
						<div>
							<label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
								Name <span class="text-red-500">*</span>
							</label>
							<input type="text" name="name" id="name" value="{{ old('name') }}" required
								   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
							@error('name')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<div>
							<label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
								Email <span class="text-red-500">*</span>
							</label>
							<input type="email" name="email" id="email" value="{{ old('email') }}" required
								   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
							@error('email')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<div>
							<label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
								Password <span class="text-red-500">*</span>
							</label>
							<input type="password" name="password" id="password" required
								   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
							@error('password')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<div>
							<label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
								Role <span class="text-red-500">*</span>
							</label>
							<select name="role" id="role" required
									class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
								<option value="">Select a role...</option>
								<option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
								<option value="chair" {{ old('role') === 'chair' ? 'selected' : '' }}>Program Chair</option>
								<option value="faculty" {{ old('role') === 'faculty' ? 'selected' : '' }}>Faculty</option>
								<option value="auditor" {{ old('role') === 'auditor' ? 'selected' : '' }}>Auditor</option>
							</select>
							@error('role')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<div class="flex gap-4">
							<x-button type="submit">
								Create User
							</x-button>
							<a href="{{ route('admin.users.index') }}"
							   class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500">
								Cancel
							</a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</x-app-layout>
