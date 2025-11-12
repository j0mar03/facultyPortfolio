<div class="p-6">
	<div class="mb-4">
		<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">My Class Offerings</h3>
		<p class="text-sm text-gray-600 dark:text-gray-400">Create or open your portfolio for each assigned class.</p>
	</div>
	<div class="overflow-x-auto">
		<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
			<thead class="bg-gray-50 dark:bg-gray-700">
				<tr>
					<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Course</th>
					<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Subject</th>
					<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">AY/Term</th>
					<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Section</th>
					<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Portfolio</th>
					<th class="px-4 py-2"></th>
				</tr>
			</thead>
			<tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
				@forelse ($offerings as $offering)
					<tr>
						<td class="px-4 py-2 whitespace-nowrap">{{ $offering->subject->course->code }}</td>
						<td class="px-4 py-2 whitespace-nowrap">
							<div class="font-medium">{{ $offering->subject->code }}</div>
							<div class="text-sm text-gray-500 dark:text-gray-400">{{ $offering->subject->title }}</div>
						</td>
						<td class="px-4 py-2 whitespace-nowrap">{{ $offering->academic_year }} / T{{ $offering->term }}</td>
						<td class="px-4 py-2 whitespace-nowrap">{{ $offering->section }}</td>
						<td class="px-4 py-2 whitespace-nowrap">
							@if ($offering->portfolio)
								<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
									{{ $offering->portfolio->status === 'approved' ? 'bg-green-100 text-green-800' : ($offering->portfolio->status === 'submitted' ? 'bg-blue-100 text-blue-800' : ($offering->portfolio->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
									{{ ucfirst($offering->portfolio->status) }}
								</span>
							@else
								<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">None</span>
							@endif
						</td>
						<td class="px-4 py-2 whitespace-nowrap text-right">
							@if ($offering->portfolio)
								<a href="{{ route('portfolios.show', $offering->portfolio) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Open</a>
							@else
								<form method="POST" action="{{ route('portfolios.store') }}">
									@csrf
									<input type="hidden" name="class_offering_id" value="{{ $offering->id }}">
									<x-button type="submit">Create</x-button>
								</form>
							@endif
						</td>
					</tr>
				@empty
					<tr>
						<td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No class offerings assigned.</td>
					</tr>
				@endforelse
			</tbody>
		</table>
	</div>
</div>


