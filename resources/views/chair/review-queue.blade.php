<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Review Queue') }}
		</h2>
	</x-slot>

	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
				@if(session('status'))
					<div class="mb-4 bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-800 text-green-600 dark:text-green-200 px-4 py-3 rounded">
						{{ session('status') }}
					</div>
				@endif

				<div class="mb-4">
					<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Portfolios Pending Review</h3>
					<p class="text-sm text-gray-600 dark:text-gray-400">Review and approve or reject submitted portfolios.</p>
				</div>

				@if($portfolios->isEmpty())
					<div class="text-center py-12">
						<svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
						</svg>
						<h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No pending portfolios</h3>
						<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">All portfolios have been reviewed.</p>
					</div>
				@else
					<div class="overflow-x-auto">
						<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
							<thead class="bg-gray-50 dark:bg-gray-700">
								<tr>
									<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Faculty</th>
									<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Course</th>
									<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Subject</th>
									<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">AY/Term</th>
									<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Submitted</th>
									<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Items</th>
									<th class="px-4 py-3"></th>
								</tr>
							</thead>
							<tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
								@foreach($portfolios as $portfolio)
									<tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
										<td class="px-4 py-4 whitespace-nowrap">
											<div class="font-medium text-gray-900 dark:text-gray-100">{{ $portfolio->user->name }}</div>
											<div class="text-sm text-gray-500 dark:text-gray-400">{{ $portfolio->user->email }}</div>
										</td>
										<td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
											{{ $portfolio->classOffering->subject->course->code }}
										</td>
										<td class="px-4 py-4">
											<div class="font-medium text-gray-900 dark:text-gray-100">{{ $portfolio->classOffering->subject->code }}</div>
											<div class="text-sm text-gray-500 dark:text-gray-400">{{ $portfolio->classOffering->subject->title }}</div>
										</td>
										<td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
											{{ $portfolio->classOffering->academic_year }} / T{{ $portfolio->classOffering->term }}
										</td>
										<td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
											{{ $portfolio->submitted_at->diffForHumans() }}
										</td>
										<td class="px-4 py-4 whitespace-nowrap">
											<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
												{{ $portfolio->items->count() }} files
											</span>
										</td>
										<td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
											<a href="{{ route('reviews.show', $portfolio) }}"
											   class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
												Review →
											</a>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>

					<div class="mt-4">
						{{ $portfolios->links() }}
					</div>
				@endif
			</div>
		</div>
	</div>
</x-app-layout>
