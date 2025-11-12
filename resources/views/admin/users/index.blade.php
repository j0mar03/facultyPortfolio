<x-app-layout>
	<x-slot name="header">
		<div class="flex justify-between items-center">
			<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
				{{ __('User Management') }}
			</h2>
			<a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
				+ Add User
			</a>
		</div>
	</x-slot>

	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
				@if(session('status'))
					<div class="mb-4 bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-800 text-green-600 dark:text-green-200 px-4 py-3 rounded">
						{{ session('status') }}
					</div>
				@endif

				<div class="overflow-x-auto">
					<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
						<thead class="bg-gray-50 dark:bg-gray-700">
							<tr>
								<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
								<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
								<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Role</th>
								<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created</th>
								<th class="px-4 py-3"></th>
							</tr>
						</thead>
						<tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
							@foreach($users as $user)
								<tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
									<td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
										{{ $user->name }}
									</td>
									<td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
										{{ $user->email }}
									</td>
									<td class="px-4 py-4 whitespace-nowrap">
										<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
											{{ $user->role === 'admin' ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' :
											   ($user->role === 'chair' ? 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100' :
											   ($user->role === 'faculty' ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' :
											   'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200')) }}">
											{{ ucfirst($user->role) }}
										</span>
									</td>
									<td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
										{{ $user->created_at->format('M d, Y') }}
									</td>
									<td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
										<a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900">
											Edit
										</a>
										@if($user->id !== Auth::id())
											<form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline"
												  onsubmit="return confirm('Are you sure you want to delete this user?');">
												@csrf
												@method('DELETE')
												<button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900">
													Delete
												</button>
											</form>
										@endif
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>

				<div class="mt-4">
					{{ $users->links() }}
				</div>
			</div>
		</div>
	</div>
</x-app-layout>
