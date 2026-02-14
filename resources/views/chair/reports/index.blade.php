<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Approved Portfolios Report') }} - {{ $selectedCourse->name }}
		</h2>
	</x-slot>

	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
			{{-- Course Tabs --}}
			@if($managedCourses->count() > 1)
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
				<div class="border-b border-gray-200 dark:border-gray-700">
					<nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
						@foreach($managedCourses as $course)
							<a href="{{ route('chair.reports.index', ['course_id' => $course->id, 'academic_year' => $selectedYear]) }}"
							   class="@if($course->id === $selectedCourse->id) border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
								{{ $course->code }}
							</a>
						@endforeach
					</nav>
				</div>
			</div>
			@endif

			{{-- Filters and Actions --}}
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
				<div class="flex items-center justify-between flex-wrap gap-4">
					{{-- Academic Year Filter --}}
					<form method="GET" action="{{ route('chair.reports.index') }}" class="flex items-center gap-4">
						<input type="hidden" name="course_id" value="{{ $selectedCourse->id }}">
						<label for="academic_year" class="text-sm font-medium text-gray-700 dark:text-gray-300">
							Academic Year:
						</label>
						<select name="academic_year" id="academic_year"
								class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
								onchange="this.form.submit()">
							@foreach($availableYears as $year)
								<option value="{{ $year }}" {{ $selectedYear === $year ? 'selected' : '' }}>
									{{ $year }}
								</option>
							@endforeach
						</select>
					</form>

					{{-- Download All Button --}}
					@if($portfolios->total() > 0)
						<a href="{{ route('chair.reports.download-all', ['course_id' => $selectedCourse->id, 'academic_year' => $selectedYear]) }}"
						   class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
							</svg>
							Download All Portfolios ({{ $portfolios->total() }})
						</a>
					@endif
				</div>
			</div>

			{{-- Success/Error Messages --}}
			@if(session('status'))
				<div class="bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-800 text-green-600 dark:text-green-200 px-4 py-3 rounded">
					{{ session('status') }}
				</div>
			@endif
			@if(session('error'))
				<div class="bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-200 px-4 py-3 rounded">
					{{ session('error') }}
				</div>
			@endif

			{{-- Statistics --}}
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
				<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
					<div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
						<div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $portfolios->total() }}</div>
						<div class="text-sm text-gray-600 dark:text-gray-400">Approved Portfolios</div>
					</div>
					<div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
						<div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $portfolios->sum(fn($p) => $p->items->count()) }}</div>
						<div class="text-sm text-gray-600 dark:text-gray-400">Total Documents</div>
					</div>
					<div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4">
						<div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $portfolios->unique('user_id')->count() }}</div>
						<div class="text-sm text-gray-600 dark:text-gray-400">Faculty Members</div>
					</div>
					<div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
						<div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($portfolios->avg(fn($p) => $p->items->count()), 1) }}</div>
						<div class="text-sm text-gray-600 dark:text-gray-400">Avg. Documents per Portfolio</div>
					</div>
				</div>
			</div>

			{{-- Approved Portfolios List --}}
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
				<div class="p-6">
					<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
						Approved Portfolios ({{ $portfolios->total() }})
					</h3>

					@if($portfolios->isEmpty())
						<div class="text-center py-12">
							<svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
							</svg>
							<h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No approved portfolios</h3>
							<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No portfolios have been approved for {{ $selectedYear }} yet.</p>
						</div>
					@else
						<div class="overflow-x-auto">
							<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
								<thead class="bg-gray-50 dark:bg-gray-700">
									<tr>
										<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Faculty</th>
										<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Subject</th>
										<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Section</th>
										<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Documents</th>
										<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Approved</th>
										<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Reviewed By</th>
										<th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
									</tr>
								</thead>
								<tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
									@foreach($portfolios as $portfolio)
										<tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
											<td class="px-4 py-4 whitespace-nowrap">
												<div class="flex items-center">
													<div>
														<div class="text-sm font-medium text-gray-900 dark:text-gray-100">
															{{ $portfolio->user->name }}
														</div>
														<div class="text-sm text-gray-500 dark:text-gray-400">
															{{ $portfolio->user->email }}
														</div>
													</div>
												</div>
											</td>
											<td class="px-4 py-4">
												<div class="text-sm font-medium text-gray-900 dark:text-gray-100">
													{{ $portfolio->classOffering->subject->code }}
												</div>
												<div class="text-sm text-gray-500 dark:text-gray-400">
													{{ $portfolio->classOffering->subject->title }}
												</div>
											</td>
											<td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
												<div>T{{ $portfolio->classOffering->term }}, Sec {{ $portfolio->classOffering->section }}</div>
												<div class="text-xs">{{ $portfolio->classOffering->academic_year }}</div>
											</td>
											<td class="px-4 py-4 whitespace-nowrap">
												@php
													$completion = $portfolio->completionStats();
												@endphp
												<div class="flex items-center gap-2">
													<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
														{{ $completion['completed'] }}/{{ $completion['total'] }} docs
													</span>
													<div class="w-20 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
														<div class="bg-green-600 h-2 rounded-full" style="width: {{ min($completion['percentage'], 100) }}%"></div>
													</div>
												</div>
											</td>
											<td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
												{{ $portfolio->approved_at?->format('M d, Y') ?? 'N/A' }}
											</td>
											<td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
												@php
													$latestReview = $portfolio->reviews->last();
												@endphp
												{{ $latestReview?->reviewer->name ?? 'N/A' }}
											</td>
											<td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
												<a href="{{ route('reviews.show', $portfolio) }}"
												   class="inline-flex items-center gap-1 text-indigo-600 dark:text-indigo-400 hover:underline">
													<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
													</svg>
													View Details
												</a>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>

						{{-- Pagination --}}
						<div class="mt-6">
							{{ $portfolios->appends(['course_id' => $selectedCourse->id, 'academic_year' => $selectedYear])->links() }}
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</x-app-layout>
