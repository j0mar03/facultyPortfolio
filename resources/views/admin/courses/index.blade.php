<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Course Management') }}
		</h2>
	</x-slot>

	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
				<div class="overflow-x-auto">
					<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
						<thead class="bg-gray-50 dark:bg-gray-700">
							<tr>
								<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Code</th>
								<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
								<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Subjects</th>
								<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created</th>
							</tr>
						</thead>
						<tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
							@foreach($courses as $course)
								<tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
									<td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
										{{ $course->code }}
									</td>
									<td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">
										{{ $course->name }}
									</td>
									<td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
										{{ $course->subjects_count }} subjects
									</td>
									<td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
										{{ $course->created_at->format('M d, Y') }}
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>

				<div class="mt-4">
					{{ $courses->links() }}
				</div>
			</div>
		</div>
	</div>
</x-app-layout>
