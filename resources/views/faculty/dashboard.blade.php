<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Dashboard') }}
		</h2>
	</x-slot>

	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
			{{-- Statistics Cards --}}
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
				{{-- Total Class Offerings --}}
				<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
					<div class="flex items-center">
						<div class="flex-shrink-0">
							<svg class="h-12 w-12 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
							</svg>
						</div>
						<div class="ml-5 w-0 flex-1">
							<dl>
								<dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Class Offerings</dt>
								<dd class="text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $totalOfferings }}</dd>
							</dl>
						</div>
					</div>
				</div>

				{{-- Portfolios Created --}}
				<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
					<div class="flex items-center">
						<div class="flex-shrink-0">
							<svg class="h-12 w-12 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

				{{-- Approved Portfolios --}}
				<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
					<div class="flex items-center">
						<div class="flex-shrink-0">
							<svg class="h-12 w-12 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
							</svg>
						</div>
						<div class="ml-5 w-0 flex-1">
							<dl>
								<dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Approved</dt>
								<dd class="text-3xl font-semibold text-green-600 dark:text-green-400">{{ $portfoliosApproved }}</dd>
							</dl>
						</div>
					</div>
				</div>

				{{-- Average Completion --}}
				<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
					<div class="flex items-center">
						<div class="flex-shrink-0">
							<svg class="h-12 w-12 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
							</svg>
						</div>
						<div class="ml-5 w-0 flex-1">
							<dl>
								<dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Avg. Completion</dt>
								<dd class="text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($avgCompletion, 1) }}%</dd>
							</dl>
						</div>
					</div>
				</div>
			</div>

			{{-- Portfolio Status Chart --}}
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Portfolio Status Overview</h3>
				<div class="grid grid-cols-1 md:grid-cols-5 gap-4">
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
						<div class="text-4xl font-bold text-yellow-600 dark:text-yellow-400">{{ $totalOfferings - $portfoliosCreated }}</div>
						<div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Not Created</div>
					</div>
				</div>
			</div>

			{{-- Document Completion Progress --}}
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Document Completion Progress</h3>

				@if(count($documentStats) > 0)
					<div class="space-y-4">
						@foreach($documentStats as $stat)
							<div>
								<div class="flex justify-between items-center mb-2">
									<div>
										<span class="font-medium text-gray-900 dark:text-gray-100">
											{{ $stat['offering']->subject->code }} - {{ $stat['offering']->subject->title }}
										</span>
										<span class="text-sm text-gray-500 dark:text-gray-400 ml-2">
											({{ $stat['offering']->academic_year }}, T{{ $stat['offering']->term }}, Sec {{ $stat['offering']->section }})
										</span>
									</div>
									<div class="flex items-center gap-2">
										<span class="text-sm font-medium text-gray-900 dark:text-gray-100">
											{{ $stat['completed'] }}/{{ $stat['total'] }} documents
										</span>
										<span class="text-sm font-medium text-gray-900 dark:text-gray-100">
											{{ number_format($stat['percentage'], 0) }}%
										</span>
									</div>
								</div>
								<div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
									<div class="bg-indigo-600 h-3 rounded-full transition-all" style="width: {{ min($stat['percentage'], 100) }}%"></div>
								</div>
							</div>
						@endforeach
					</div>
				@else
					<p class="text-center text-gray-500 dark:text-gray-400 py-8">No portfolios created yet.</p>
				@endif
			</div>
		</div>
	</div>
</x-app-layout>
