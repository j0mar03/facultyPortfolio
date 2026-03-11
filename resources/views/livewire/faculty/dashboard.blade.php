<div class="p-6">
	{{-- Reminder Banner --}}
	@if($unreadReminder)
		<div class="mb-6 bg-indigo-50 dark:bg-indigo-900/30 border-l-4 border-indigo-500 p-4 shadow-md rounded-r-lg flex items-center justify-between animate-pulse">
			<div class="flex items-center">
				<div class="flex-shrink-0">
					<svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
					</svg>
				</div>
				<div class="ml-4">
					<p class="text-sm font-bold text-indigo-900 dark:text-indigo-100">
						🔔 Portfolio Reminder from {{ $unreadReminder->sender->name }}
					</p>
					<p class="text-xs text-indigo-700 dark:text-indigo-300 mt-1">
						{{ $unreadReminder->message }} 
						@if($unreadReminder->classOffering)
							— <strong>{{ $unreadReminder->classOffering->subject->code }} ({{ $unreadReminder->classOffering->section }})</strong>
						@endif
					</p>
				</div>
			</div>
			<button wire:click="dismissReminder({{ $unreadReminder->id }})" class="ml-4 flex-shrink-0 bg-indigo-100 dark:bg-indigo-800 text-indigo-600 dark:text-indigo-200 px-3 py-1 rounded-md text-xs font-bold hover:bg-indigo-200 transition uppercase tracking-wider">
				Dismiss
			</button>
		</div>
	@endif

	<div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4 border-b pb-4 dark:border-gray-700">
		<div>
			<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">My Class Offerings</h3>
			<p class="text-sm text-gray-600 dark:text-gray-400">Create or open your portfolio for each assigned class.</p>
		</div>
		<div class="flex flex-wrap items-center gap-4">
			<div class="flex items-center gap-2">
				<label for="ay_filter" class="text-sm font-medium text-gray-700 dark:text-gray-300">AY:</label>
				<select id="ay_filter" wire:model.live="selectedAY"
						class="text-sm rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 min-w-[140px]">
					<option value="">All Years</option>
					@foreach($availableYears as $year)
						<option value="{{ $year }}">{{ $year }}</option>
					@endforeach
				</select>
			</div>
			<div class="flex items-center gap-2">
				<label for="term_filter" class="text-sm font-medium text-gray-700 dark:text-gray-300">Term:</label>
				<select id="term_filter" wire:model.live="selectedTerm"
						class="text-sm rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 min-w-[120px]">
					<option value="">All Terms</option>
					@foreach($availableTerms as $term)
						<option value="{{ $term }}">Term {{ $term }}</option>
					@endforeach
				</select>
			</div>
			@if($selectedAY || $selectedTerm)
				<button wire:click="$set('selectedAY', ''); $set('selectedTerm', '')" 
						class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
					Clear Filters
				</button>
			@endif
		</div>
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
						<td class="px-4 py-2">
							<div class="font-medium">{{ $offering->subject->code }}</div>
							<div class="text-sm text-gray-500 dark:text-gray-400">{{ $offering->subject->title }}</div>
							<div class="flex flex-wrap gap-2 mt-1">
								@if($offering->assignment_document)
									<a href="{{ route('chair.subjects.download-assignment', $offering) }}"
									   class="inline-flex items-center gap-1 text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
										<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
										</svg>
										Teaching Load
									</a>
								@endif
								@if($offering->instructional_material)
									<a href="{{ route('chair.subjects.download-document', ['classOffering' => $offering, 'type' => 'im']) }}"
									   target="_blank"
									   class="inline-flex items-center gap-1 text-xs text-green-600 dark:text-green-400 hover:underline">
										<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
										</svg>
										IM
									</a>
								@endif
								@if($offering->syllabus)
									<a href="{{ route('chair.subjects.download-document', ['classOffering' => $offering, 'type' => 'syllabus']) }}"
									   target="_blank"
									   class="inline-flex items-center gap-1 text-xs text-purple-600 dark:text-purple-400 hover:underline">
										<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
										</svg>
										Syllabus
									</a>
								@endif
							</div>
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


