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
							<a href="{{ route('chair.dashboard', ['course_id' => $course->id]) }}"
							   class="@if($course->id === $selectedCourse->id) border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
								{{ $course->code }}
							</a>
						@endforeach
					</nav>
				</div>
			</div>
			@endif

			{{-- Statistics Cards --}}
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
				{{-- Total Subjects --}}
				<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
					<div class="flex items-center">
						<div class="flex-shrink-0">
							<svg class="h-12 w-12 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
							</svg>
						</div>
						<div class="ml-5 w-0 flex-1">
							<dl>
								<dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Subjects</dt>
								<dd class="text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $totalSubjects }}</dd>
							</dl>
						</div>
					</div>
				</div>

				{{-- Total Class Offerings --}}
				<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
					<div class="flex items-center">
						<div class="flex-shrink-0">
							<svg class="h-12 w-12 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
							</svg>
						</div>
						<div class="ml-5 w-0 flex-1">
							<dl>
								<dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Class Offerings</dt>
								<dd class="text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $totalOfferings }}</dd>
							</dl>
						</div>
					</div>
				</div>

				{{-- Portfolios Created --}}
				<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
					<div class="flex items-center">
						<div class="flex-shrink-0">
							<svg class="h-12 w-12 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
							</svg>
						</div>
						<div class="ml-5 w-0 flex-1">
							<dl>
								<dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Portfolios Created</dt>
								<dd class="text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $portfoliosCreated }}</dd>
							</dl>
						</div>
					</div>
				</div>

				{{-- Portfolios Approved --}}
				<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
					<div class="flex items-center">
						<div class="flex-shrink-0">
							<svg class="h-12 w-12 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
							</svg>
						</div>
						<div class="ml-5 w-0 flex-1">
							<dl>
								<dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Approved</dt>
								<dd class="text-3xl font-semibold text-purple-600 dark:text-purple-400">{{ $portfoliosApproved }}</dd>
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
									<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Faculty</th>
									<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Class Offerings</th>
									<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Documents</th>
									<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Completion</th>
									<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
								</tr>
							</thead>
							<tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
								@foreach($facultyStats as $stat)
									<tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
										<td class="px-4 py-3 whitespace-nowrap">
											<div class="font-medium text-gray-900 dark:text-gray-100">{{ $stat['faculty']->name }}</div>
											<div class="text-sm text-gray-500 dark:text-gray-400">{{ $stat['faculty']->email }}</div>
										</td>
										<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
											{{ $stat['offerings_count'] }}
										</td>
										<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
											{{ $stat['documents_completed'] }}/{{ $stat['documents_total'] }}
										</td>
										<td class="px-4 py-3">
											<div class="flex items-center gap-2">
												<div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
													<div class="bg-indigo-600 h-2 rounded-full" style="width: {{ min($stat['completion_percentage'], 100) }}%"></div>
												</div>
												<span class="text-sm font-medium text-gray-900 dark:text-gray-100">
													{{ number_format($stat['completion_percentage'], 0) }}%
												</span>
											</div>
										</td>
										<td class="px-4 py-3">
											<div class="flex gap-1 text-xs">
												@if($stat['statuses']['none'] > 0)
													<span class="inline-flex items-center px-2 py-0.5 rounded bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
														{{ $stat['statuses']['none'] }} None
													</span>
												@endif
												@if($stat['statuses']['draft'] > 0)
													<span class="inline-flex items-center px-2 py-0.5 rounded bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
														{{ $stat['statuses']['draft'] }} Draft
													</span>
												@endif
												@if($stat['statuses']['submitted'] > 0)
													<span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
														{{ $stat['statuses']['submitted'] }} Sub
													</span>
												@endif
												@if($stat['statuses']['approved'] > 0)
													<span class="inline-flex items-center px-2 py-0.5 rounded bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
														{{ $stat['statuses']['approved'] }} App
													</span>
												@endif
												@if($stat['statuses']['rejected'] > 0)
													<span class="inline-flex items-center px-2 py-0.5 rounded bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
														{{ $stat['statuses']['rejected'] }} Rej
													</span>
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
