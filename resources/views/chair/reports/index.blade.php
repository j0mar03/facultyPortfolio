<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Portfolio Reports & Monitoring') }} - {{ $selectedCourse->name }}
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
							<a href="{{ route('chair.reports.index', ['course_id' => $course->id, 'academic_year' => $selectedYear, 'term' => $selectedTerm]) }}"
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
					<form method="GET" action="{{ route('chair.reports.index') }}" class="flex items-center gap-4 flex-wrap">
						<input type="hidden" name="course_id" value="{{ $selectedCourse->id }}">
						
						<div class="flex items-center gap-2">
							<label for="academic_year" class="text-sm font-medium text-gray-700 dark:text-gray-300">AY:</label>
							<select name="academic_year" id="academic_year" class="text-sm rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200" onchange="this.form.submit()">
								@foreach($availableYears as $year)
									<option value="{{ $year }}" {{ $selectedYear === $year ? 'selected' : '' }}>{{ $year }}</option>
								@endforeach
							</select>
						</div>

						<div class="flex items-center gap-2">
							<label for="term" class="text-sm font-medium text-gray-700 dark:text-gray-300">Term:</label>
							<select name="term" id="term" class="text-sm rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200" onchange="this.form.submit()">
								<option value="">All Terms</option>
								@foreach($availableTerms as $term)
									<option value="{{ $term }}" {{ $selectedTerm !== null && $selectedTerm == $term ? 'selected' : '' }}>Term {{ $term }}</option>
								@endforeach
							</select>
						</div>
					</form>

					@if($totalApproved > 0)
						<a href="{{ route('chair.reports.download-all', ['course_id' => $selectedCourse->id, 'academic_year' => $selectedYear, 'term' => $selectedTerm]) }}"
						   class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition">
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
							</svg>
							Download Approved ({{ $totalApproved }})
						</a>
					@endif
				</div>
			</div>

			{{-- Statistics --}}
			<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
				<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-green-500">
					<div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $totalApproved }}</div>
					<div class="text-sm text-gray-600 dark:text-gray-400 uppercase tracking-wider font-semibold">Approved Portfolios</div>
				</div>
				<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-yellow-500">
					<div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $totalInProgress }}</div>
					<div class="text-sm text-gray-600 dark:text-gray-400 uppercase tracking-wider font-semibold">In Progress / Pending</div>
				</div>
				<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-indigo-500">
					<div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
						{{ $approvedPortfolios->total() + $inProgressPortfolios->total() }}
					</div>
					<div class="text-sm text-gray-600 dark:text-gray-400 uppercase tracking-wider font-semibold">Total Portfolios Managed</div>
				</div>
			</div>

			{{-- In Progress Monitoring Section --}}
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
				<div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-yellow-50/50 dark:bg-yellow-900/10">
					<h3 class="text-lg font-bold text-yellow-800 dark:text-yellow-200 flex items-center gap-2">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
						In-Progress & Monitoring
					</h3>
					<p class="text-sm text-yellow-600 dark:text-yellow-400">View and finalize faculty documents even before they are submitted.</p>
				</div>
				<div class="p-0">
					@if($inProgressPortfolios->isEmpty())
						<div class="p-6 text-center text-gray-500 dark:text-gray-400">No in-progress portfolios found for this period.</div>
					@else
						<div class="overflow-x-auto">
							<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
								<thead class="bg-gray-50 dark:bg-gray-700">
									<tr>
										<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Faculty</th>
										<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Subject</th>
										<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Progress</th>
										<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
										<th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
									</tr>
								</thead>
								<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
									@foreach($inProgressPortfolios as $portfolio)
										<tr class="hover:bg-gray-50 dark:hover:bg-gray-900/40">
											<td class="px-4 py-4 whitespace-nowrap">
												<div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $portfolio->user->name }}</div>
												<div class="text-xs text-gray-500">{{ $portfolio->user->email }}</div>
											</td>
											<td class="px-4 py-4 whitespace-nowrap text-sm">
												<div class="font-bold">{{ $portfolio->classOffering->subject->code }}</div>
												<div class="text-xs text-gray-500">Sec {{ $portfolio->classOffering->section }}</div>
											</td>
											<td class="px-4 py-4 whitespace-nowrap">
												@php $completion = $portfolio->completionStats(); @endphp
												<div class="flex items-center gap-2">
													<div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
														<div class="bg-yellow-500 h-1.5 rounded-full" style="width: {{ $completion['percentage'] }}%"></div>
													</div>
													<span class="text-xs font-medium">{{ $completion['completed'] }}/{{ $completion['total'] }}</span>
												</div>
											</td>
											<td class="px-4 py-4 whitespace-nowrap">
												<span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
													{{ $portfolio->status === 'submitted' ? 'bg-blue-100 text-blue-800' : ($portfolio->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
													{{ $portfolio->status }}
												</span>
											</td>
											<td class="px-4 py-4 whitespace-nowrap text-right text-sm">
												<div class="flex flex-col items-end gap-1">
													<a href="{{ route('reviews.show', $portfolio) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline font-semibold flex items-center justify-end gap-1">
														<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
														</svg>
														Monitor Docs
													</a>
													<form method="POST" action="{{ route('chair.reminders.store') }}" class="inline">
														@csrf
														<input type="hidden" name="recipient_id" value="{{ $portfolio->user_id }}">
														<input type="hidden" name="class_offering_id" value="{{ $portfolio->class_offering_id }}">
														<button type="submit" class="text-red-600 dark:text-red-400 hover:underline font-semibold flex items-center justify-end gap-1">
															<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
																<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
															</svg>
															Send Nudge
														</button>
													</form>
												</div>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
						<div class="p-4 bg-gray-50 dark:bg-gray-800/50">
							{{ $inProgressPortfolios->appends(request()->all())->links() }}
						</div>
					@endif
				</div>
			</div>

			{{-- Approved Finalized Section --}}
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
				<div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-green-50/50 dark:bg-green-900/10">
					<h3 class="text-lg font-bold text-green-800 dark:text-green-200 flex items-center gap-2">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
						Approved & Finalized
					</h3>
					<p class="text-sm text-green-600 dark:text-green-400">Complete portfolios that have been officially reviewed and archived.</p>
				</div>
				<div class="p-0">
					@if($approvedPortfolios->isEmpty())
						<div class="p-6 text-center text-gray-500 dark:text-gray-400">No approved portfolios found for this period.</div>
					@else
						<div class="overflow-x-auto">
							<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
								<thead class="bg-gray-50 dark:bg-gray-700">
									<tr>
										<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Faculty</th>
										<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Subject</th>
										<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Approved Date</th>
										<th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
									</tr>
								</thead>
								<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
									@foreach($approvedPortfolios as $portfolio)
										<tr class="hover:bg-gray-50 dark:hover:bg-gray-900/40">
											<td class="px-4 py-4 whitespace-nowrap">
												<div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $portfolio->user->name }}</div>
											</td>
											<td class="px-4 py-4 whitespace-nowrap text-sm">
												<div class="font-bold">{{ $portfolio->classOffering->subject->code }}</div>
											</td>
											<td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
												{{ $portfolio->approved_at?->format('M d, Y') }}
											</td>
											<td class="px-4 py-4 whitespace-nowrap text-right text-sm">
												<a href="{{ route('reviews.show', $portfolio) }}" class="text-green-600 dark:text-green-400 hover:underline font-semibold flex items-center justify-end gap-1">
													<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
													</svg>
													Archive View
												</a>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
						<div class="p-4 bg-gray-50 dark:bg-gray-800/50">
							{{ $approvedPortfolios->appends(request()->all())->links() }}
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</x-app-layout>
