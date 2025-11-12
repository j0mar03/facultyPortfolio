<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Admin Dashboard') }}
		</h2>
	</x-slot>

	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
			{{-- Statistics Cards --}}
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
				{{-- Total Users --}}
				<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
					<div class="flex items-center">
						<div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
							<svg class="w-8 h-8 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
							</svg>
						</div>
						<div class="ml-4">
							<p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Users</p>
							<p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['total_users'] }}</p>
						</div>
					</div>
					<div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
						{{ $stats['total_faculty'] }} Faculty, {{ $stats['total_chairs'] }} Chairs
					</div>
				</div>

				{{-- Total Courses --}}
				<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
					<div class="flex items-center">
						<div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
							<svg class="w-8 h-8 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
							</svg>
						</div>
						<div class="ml-4">
							<p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Courses</p>
							<p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['total_courses'] }}</p>
						</div>
					</div>
				</div>

				{{-- Total Portfolios --}}
				<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
					<div class="flex items-center">
						<div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
							<svg class="w-8 h-8 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
							</svg>
						</div>
						<div class="ml-4">
							<p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Portfolios</p>
							<p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['total_portfolios'] }}</p>
						</div>
					</div>
				</div>

				{{-- Approved Portfolios --}}
				<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
					<div class="flex items-center">
						<div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
							<svg class="w-8 h-8 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
							</svg>
						</div>
						<div class="ml-4">
							<p class="text-sm font-medium text-gray-600 dark:text-gray-400">Approved</p>
							<p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['portfolios_approved'] }}</p>
						</div>
					</div>
				</div>
			</div>

			{{-- Portfolio Status Breakdown --}}
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Portfolio Status</h3>
				<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
					<div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded">
						<p class="text-3xl font-bold text-gray-600 dark:text-gray-300">{{ $stats['portfolios_draft'] }}</p>
						<p class="text-sm text-gray-500 dark:text-gray-400">Draft</p>
					</div>
					<div class="text-center p-4 bg-blue-50 dark:bg-blue-900/30 rounded">
						<p class="text-3xl font-bold text-blue-600 dark:text-blue-300">{{ $stats['portfolios_submitted'] }}</p>
						<p class="text-sm text-blue-500 dark:text-blue-400">Submitted</p>
					</div>
					<div class="text-center p-4 bg-green-50 dark:bg-green-900/30 rounded">
						<p class="text-3xl font-bold text-green-600 dark:text-green-300">{{ $stats['portfolios_approved'] }}</p>
						<p class="text-sm text-green-500 dark:text-green-400">Approved</p>
					</div>
					<div class="text-center p-4 bg-red-50 dark:bg-red-900/30 rounded">
						<p class="text-3xl font-bold text-red-600 dark:text-red-300">{{ $stats['portfolios_rejected'] }}</p>
						<p class="text-sm text-red-500 dark:text-red-400">Rejected</p>
					</div>
				</div>
			</div>

			{{-- Quick Actions --}}
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Quick Actions</h3>
				<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
					<a href="{{ route('admin.users.index') }}" class="flex items-center p-4 bg-blue-50 dark:bg-blue-900/30 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition">
						<svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
						</svg>
						<span class="ml-3 text-blue-700 dark:text-blue-300 font-medium">Manage Users</span>
					</a>
					<a href="{{ route('admin.reports.index') }}" class="flex items-center p-4 bg-purple-50 dark:bg-purple-900/30 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/50 transition">
						<svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
						</svg>
						<span class="ml-3 text-purple-700 dark:text-purple-300 font-medium">View Reports</span>
					</a>
					<a href="{{ route('admin.courses.index') }}" class="flex items-center p-4 bg-green-50 dark:bg-green-900/30 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/50 transition">
						<svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
						</svg>
						<span class="ml-3 text-green-700 dark:text-green-300 font-medium">Manage Courses</span>
					</a>
				</div>
			</div>

			{{-- Recent Activity --}}
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Recent Portfolio Activity</h3>
				@if($recentPortfolios->isEmpty())
					<p class="text-gray-500 dark:text-gray-400 text-center py-8">No portfolio activity yet.</p>
				@else
					<div class="overflow-x-auto">
						<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
							<thead class="bg-gray-50 dark:bg-gray-700">
								<tr>
									<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Faculty</th>
									<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Course</th>
									<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Subject</th>
									<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
									<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Updated</th>
								</tr>
							</thead>
							<tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
								@foreach($recentPortfolios as $portfolio)
									<tr>
										<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $portfolio->user->name }}</td>
										<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $portfolio->classOffering->subject->course->code }}</td>
										<td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $portfolio->classOffering->subject->code }}</td>
										<td class="px-4 py-3 whitespace-nowrap">
											<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
												{{ $portfolio->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' :
												   ($portfolio->status === 'submitted' ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' :
												   ($portfolio->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' :
												   'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200')) }}">
												{{ ucfirst($portfolio->status) }}
											</span>
										</td>
										<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $portfolio->updated_at->diffForHumans() }}</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				@endif
			</div>
		</div>
	</div>
</x-app-layout>
