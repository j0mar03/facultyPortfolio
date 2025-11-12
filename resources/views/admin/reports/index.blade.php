<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Portfolio Reports') }}
		</h2>
	</x-slot>

	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
			{{-- Statistics Summary --}}
			<div class="grid grid-cols-2 md:grid-cols-5 gap-4">
				<div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
					<p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['total'] }}</p>
					<p class="text-sm text-gray-500 dark:text-gray-400">Total</p>
				</div>
				<div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow">
					<p class="text-2xl font-bold text-gray-600 dark:text-gray-300">{{ $stats['draft'] }}</p>
					<p class="text-sm text-gray-500 dark:text-gray-400">Draft</p>
				</div>
				<div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg shadow">
					<p class="text-2xl font-bold text-blue-600 dark:text-blue-300">{{ $stats['submitted'] }}</p>
					<p class="text-sm text-blue-500 dark:text-blue-400">Submitted</p>
				</div>
				<div class="bg-green-50 dark:bg-green-900/30 p-4 rounded-lg shadow">
					<p class="text-2xl font-bold text-green-600 dark:text-green-300">{{ $stats['approved'] }}</p>
					<p class="text-sm text-green-500 dark:text-green-400">Approved</p>
				</div>
				<div class="bg-red-50 dark:bg-red-900/30 p-4 rounded-lg shadow">
					<p class="text-2xl font-bold text-red-600 dark:text-red-300">{{ $stats['rejected'] }}</p>
					<p class="text-sm text-red-500 dark:text-red-400">Rejected</p>
				</div>
			</div>

			{{-- Filters --}}
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Filters</h3>
				<form method="GET" action="{{ route('admin.reports.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
					<div>
						<label for="course_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Course</label>
						<select name="course_id" id="course_id"
								class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
							<option value="">All Courses</option>
							@foreach($courses as $course)
								<option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
									{{ $course->code }}
								</option>
							@endforeach
						</select>
					</div>

					<div>
						<label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
						<select name="status" id="status"
								class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
							<option value="">All Statuses</option>
							<option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
							<option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
							<option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
							<option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
						</select>
					</div>

					<div>
						<label for="academic_year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Academic Year</label>
						<select name="academic_year" id="academic_year"
								class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
							<option value="">All Years</option>
							@foreach($academicYears as $year)
								<option value="{{ $year }}" {{ request('academic_year') === $year ? 'selected' : '' }}>
									{{ $year }}
								</option>
							@endforeach
						</select>
					</div>

					<div>
						<label for="faculty_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Faculty</label>
						<select name="faculty_id" id="faculty_id"
								class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
							<option value="">All Faculty</option>
							@foreach($faculty as $f)
								<option value="{{ $f->id }}" {{ request('faculty_id') == $f->id ? 'selected' : '' }}>
									{{ $f->name }}
								</option>
							@endforeach
						</select>
					</div>

					<div class="md:col-span-4 flex gap-2">
						<x-button type="submit">Apply Filters</x-button>
						<a href="{{ route('admin.reports.index') }}"
						   class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500">
							Clear
						</a>
					</div>
				</form>
			</div>

			{{-- Portfolio List --}}
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Portfolios</h3>

				@if($portfolios->isEmpty())
					<p class="text-center text-gray-500 dark:text-gray-400 py-8">No portfolios found.</p>
				@else
					<div class="overflow-x-auto">
						<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
							<thead class="bg-gray-50 dark:bg-gray-700">
								<tr>
									<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Faculty</th>
									<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Course</th>
									<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Subject</th>
									<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">AY/Term</th>
									<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
									<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Files</th>
									<th class="px-4 py-2"></th>
								</tr>
							</thead>
							<tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
								@foreach($portfolios as $portfolio)
									<tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
										<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
											{{ $portfolio->user->name }}
										</td>
										<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
											{{ $portfolio->classOffering->subject->course->code }}
										</td>
										<td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
											{{ $portfolio->classOffering->subject->code }}
										</td>
										<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
											{{ $portfolio->classOffering->academic_year }} / T{{ $portfolio->classOffering->term }}
										</td>
										<td class="px-4 py-3 whitespace-nowrap">
											<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
												{{ $portfolio->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' :
												   ($portfolio->status === 'submitted' ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' :
												   ($portfolio->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' :
												   'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200')) }}">
												{{ ucfirst($portfolio->status) }}
											</span>
										</td>
										<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
											{{ $portfolio->items->count() }} files
										</td>
										<td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium space-x-2">
											<a href="{{ route('reviews.show', $portfolio) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
												View
											</a>
											<a href="{{ route('admin.reports.export', $portfolio) }}" class="text-green-600 dark:text-green-400 hover:underline">
												Export
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
