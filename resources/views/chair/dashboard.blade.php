<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Dashboard') }} - {{ $selectedCourse->name }}
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
							<a href="{{ route('chair.dashboard', ['course_id' => $course->id, 'academic_year' => $selectedYear, 'term' => $selectedTerm]) }}"
							   class="@if($course->id === $selectedCourse->id) border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
								{{ $course->code }}
							</a>
						@endforeach
					</nav>
				</div>
			</div>
			@endif

			{{-- Academic Year & Term Filter --}}
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
				<form method="GET" action="{{ route('chair.dashboard') }}" class="flex flex-wrap items-center gap-6">
					<input type="hidden" name="course_id" value="{{ $selectedCourse->id }}">
					
					<div class="flex items-center gap-2">
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
					</div>
					
					<div class="flex items-center gap-2">
						<label for="term" class="text-sm font-medium text-gray-700 dark:text-gray-300">
							Term:
						</label>
						<select name="term" id="term"
								class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
								onchange="this.form.submit()">
							<option value="">All Terms</option>
							@foreach($availableTerms as $term)
								<option value="{{ $term }}" {{ $selectedTerm !== null && $selectedTerm == $term ? 'selected' : '' }}>
									Term {{ $term }}
								</option>
							@endforeach
						</select>
					</div>

					<div class="text-sm text-gray-500 dark:text-gray-400">
						Filtering data for <strong>{{ $selectedYear }}</strong> {{ $selectedTerm ? '(Term '.$selectedTerm.')' : '(All Terms)' }}
					</div>
				</form>
			</div>

			{{-- Statistics Cards --}}
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
				{{-- Total Subjects --}}
				<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
					<div class="flex items-center">
						<div class="flex-shrink-0">
							<svg class="h-10 w-10 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
							</svg>
						</div>
						<div class="ml-4 w-0 flex-1">
							<dl>
								<dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase truncate">Required Subjects</dt>
								<dd class="text-2xl font-bold text-gray-900 dark:text-gray-100">
									{{ $totalRequiredSubjectsCount }}<span class="text-sm font-normal text-gray-500">/{{ $totalSubjectsCount }}</span>
								</dd>
							</dl>
						</div>
					</div>
				</div>

				{{-- Total Class Offerings --}}
				<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
					<div class="flex items-center">
						<div class="flex-shrink-0">
							<svg class="h-10 w-10 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
							</svg>
						</div>
						<div class="ml-4 w-0 flex-1">
							<dl>
								<dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase truncate">Required Offerings</dt>
								<dd class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalOfferings }}</dd>
								@if($excludedOfferingsCount > 0)
									<dd class="text-[10px] text-gray-400">{{ $excludedOfferingsCount }} excluded (GEED/NSTP/etc)</dd>
								@endif
							</dl>
						</div>
					</div>
				</div>

				{{-- Portfolios Created --}}
				<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-indigo-500">
					<div class="flex items-center">
						<div class="flex-shrink-0">
							<svg class="h-10 w-10 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
							</svg>
						</div>
						<div class="ml-4 w-0 flex-1">
							<dl>
								<dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase truncate">Portfolios</dt>
								<dd class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $portfoliosCreated }}</dd>
							</dl>
						</div>
					</div>
				</div>

				{{-- Portfolios Approved --}}
				<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-green-500">
					<div class="flex items-center">
						<div class="flex-shrink-0">
							<svg class="h-10 w-10 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
							</svg>
						</div>
						<div class="ml-4 w-0 flex-1">
							<dl>
								<dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase truncate">Approved</dt>
								<dd class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $portfoliosApproved }}</dd>
							</dl>
						</div>
					</div>
				</div>

				{{-- Faculty Submission Status --}}
				<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-yellow-500">
					<div class="flex items-center">
						<div class="flex-shrink-0">
							<svg class="h-10 w-10 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
							</svg>
						</div>
						<div class="ml-4 w-0 flex-1">
							<dl>
								<dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase truncate">Faculty Active</dt>
								<dd class="text-2xl font-bold text-gray-900 dark:text-gray-100">
									{{ $facultyWithPortfoliosCount }}<span class="text-sm font-normal text-gray-500 dark:text-gray-400">/{{ $totalFacultyCount }}</span>
								</dd>
							</dl>
						</div>
					</div>
				</div>
			</div>

			{{-- Portfolio Status Overview --}}
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Portfolio Status Overview</h3>
				<div class="grid grid-cols-2 md:grid-cols-6 gap-4">
					<div class="text-center">
						<div class="text-4xl font-bold text-yellow-600 dark:text-yellow-400">{{ $portfoliosPending }}</div>
						<div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Not Created</div>
					</div>
					<div class="text-center">
						<div class="text-4xl font-bold text-gray-600 dark:text-gray-400">{{ $portfoliosDraft }}</div>
						<div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Draft</div>
					</div>
					<div class="text-center">
						<div class="text-4xl font-bold text-blue-600 dark:text-blue-400">{{ $portfoliosSubmitted }}</div>
						<div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Submitted</div>
					</div>
					<div class="text-center">
						<div class="text-4xl font-bold text-green-600 dark:text-green-400">{{ $portfoliosApproved }}</div>
						<div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Approved</div>
					</div>
					<div class="text-center">
						<div class="text-4xl font-bold text-red-600 dark:text-red-400">{{ $portfoliosRejected }}</div>
						<div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Rejected</div>
					</div>
					<div class="text-center">
						<div class="text-4xl font-bold text-indigo-600 dark:text-indigo-400">{{ $portfoliosCreated }}</div>
						<div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Created</div>
					</div>
				</div>
			</div>

			{{-- Faculty Document Completion --}}
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Faculty Document Completion Status</h3>

				@if(count($facultyStats) > 0)
					<div class="overflow-x-auto">
						<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
							<thead class="bg-gray-50 dark:bg-gray-700">
								<tr>
									<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Faculty Member</th>
									<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Submission Status</th>
									<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Portfolios (Started/Total)</th>
									<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Completion</th>
									<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Details</th>
								</tr>
							</thead>
							<tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
								@foreach($facultyStats as $stat)
									<tr class="hover:bg-gray-50 dark:hover:bg-gray-700 {{ !$stat['has_started'] ? 'bg-red-50/50 dark:bg-red-900/10' : '' }}">
										<td class="px-4 py-3 whitespace-nowrap">
											<div class="flex items-center">
												<div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-500 font-bold">
													{{ substr($stat['faculty']->name, 0, 1) }}
												</div>
												<div class="ml-3">
													<div class="font-medium text-gray-900 dark:text-gray-100">{{ $stat['faculty']->name }}</div>
													<div class="text-xs text-gray-500 dark:text-gray-400">{{ $stat['faculty']->email }}</div>
												</div>
											</div>
										</td>
										<td class="px-4 py-3 whitespace-nowrap">
											@if(!$stat['has_started'])
												<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
													<svg class="-ml-0.5 mr-1.5 h-2 w-2 text-red-400" fill="currentColor" viewBox="0 0 8 8">
														<circle cx="4" cy="4" r="3" />
													</svg>
													Not Started
												</span>
											@elseif($stat['completion_percentage'] >= 100)
												<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
													<svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
														<circle cx="4" cy="4" r="3" />
													</svg>
													Complete
												</span>
											@else
												<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
													<svg class="-ml-0.5 mr-1.5 h-2 w-2 text-blue-400" fill="currentColor" viewBox="0 0 8 8">
														<circle cx="4" cy="4" r="3" />
													</svg>
													In Progress
												</span>
											@endif
										</td>
										<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
											<span class="font-semibold">{{ $stat['portfolio_count'] }}</span> / {{ $stat['offerings_count'] }}
										</td>
										<td class="px-4 py-3">
											<div class="flex items-center gap-2">
												<div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
													<div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ min($stat['completion_percentage'], 100) }}%"></div>
												</div>
												<span class="text-xs font-medium text-gray-900 dark:text-gray-100">
													{{ number_format($stat['completion_percentage'], 0) }}%
												</span>
											</div>
											<div class="text-[10px] text-gray-500 mt-0.5">{{ $stat['documents_completed'] }}/{{ $stat['documents_total'] }} documents</div>
										</td>
										<td class="px-4 py-3">
											<div class="flex items-center gap-3">
												<div class="flex flex-wrap gap-1">
													@if($stat['statuses']['none'] > 0)
														<span title="Not started" class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 text-[10px]">
															{{ $stat['statuses']['none'] }} None
														</span>
													@endif
													@if($stat['statuses']['draft'] > 0)
														<span title="Draft" class="px-1.5 py-0.5 rounded bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-500 text-[10px] border border-yellow-100 dark:border-yellow-900">
															{{ $stat['statuses']['draft'] }} Draft
														</span>
													@endif
													@if($stat['statuses']['submitted'] > 0)
														<span title="Submitted" class="px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-500 text-[10px] border border-blue-100 dark:border-blue-900">
															{{ $stat['statuses']['submitted'] }} Sub
														</span>
													@endif
													@if($stat['statuses']['approved'] > 0)
														<span title="Approved" class="px-1.5 py-0.5 rounded bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-500 text-[10px] border border-green-100 dark:border-green-900">
															{{ $stat['statuses']['approved'] }} App
														</span>
													@endif
													@if($stat['statuses']['rejected'] > 0)
														<span title="Rejected" class="px-1.5 py-0.5 rounded bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-500 text-[10px] border border-red-100 dark:border-red-900">
															{{ $stat['statuses']['rejected'] }} Rej
														</span>
													@endif
												</div>
												@if(!$stat['has_started'] || $stat['completion_percentage'] < 100)
													<form method="POST" action="{{ route('chair.reminders.store') }}" class="inline">
														@csrf
														<input type="hidden" name="recipient_id" value="{{ $stat['faculty']->id }}">
														<button type="submit" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300" title="Send Reminder">
															<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
																<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
															</svg>
														</button>
													</form>
												@endif
											</div>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				@else
					<p class="text-center text-gray-500 dark:text-gray-400 py-8">No faculty assigned yet.</p>
				@endif
			</div>
		</div>
	</div>
</x-app-layout>
