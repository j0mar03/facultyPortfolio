<x-app-layout>
	<x-slot name="header">
		<div class="flex flex-wrap items-center justify-between gap-3">
			<div>
				<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
					Approved Documents - {{ $subject->code }}
				</h2>
				<p class="text-sm text-gray-500 dark:text-gray-400">{{ $subject->title }}</p>
			</div>
			<div class="flex items-center gap-3">
				<a href="{{ route('chair.subjects.show', ['subject' => $subject, 'academic_year' => $selectedYear]) }}"
				   class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Back to Subject</a>
			</div>
		</div>
	</x-slot>

	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
				<div class="flex flex-wrap items-center justify-between gap-4">
					<div>
						<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Organized View by Required Document</h3>
						<p class="text-sm text-gray-500 dark:text-gray-400">
							Shows only approved portfolios. Syllabus and Sample IMs are chair-managed links.
						</p>
					</div>
					<form method="GET" action="{{ route('chair.subjects.approved-documents', $subject) }}" class="flex items-center gap-2">
						<label for="academic_year" class="text-sm text-gray-600 dark:text-gray-300">Academic Year</label>
						<select id="academic_year" name="academic_year" onchange="this.form.submit()"
							class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm">
							@forelse($availableYears as $year)
								<option value="{{ $year }}" {{ $selectedYear === $year ? 'selected' : '' }}>{{ $year }}</option>
							@empty
								<option value="{{ $selectedYear }}">{{ $selectedYear }}</option>
							@endforelse
						</select>
					</form>
				</div>
			</div>

			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
				@if($approvedOfferings->isEmpty())
					<div class="text-center py-8">
						<h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">No approved portfolios</h3>
						<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
							No approved portfolios found for {{ $selectedYear }}.
						</p>
					</div>
				@else
					<div class="overflow-x-auto">
						<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
							<thead class="bg-gray-50 dark:bg-gray-700">
								<tr>
									<th class="px-3 py-3 text-left font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Faculty</th>
									<th class="px-3 py-3 text-left font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Section</th>
									<th class="px-3 py-3 text-left font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Completion</th>
									@foreach($requiredTypes as $type)
										<th class="px-3 py-3 text-left font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
											{{ $itemTypes[$type] ?? $type }}
										</th>
									@endforeach
									<th class="px-3 py-3 text-left font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
								</tr>
							</thead>
							<tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
								@foreach($approvedOfferings as $offering)
									@php
										$portfolio = $offering->portfolio;
										$completion = $portfolio->completionStats();
										$itemsByType = $portfolio->items->groupBy('type');
									@endphp
									<tr class="align-top">
										<td class="px-3 py-3 whitespace-nowrap">
											<div class="font-medium text-gray-900 dark:text-gray-100">{{ $offering->faculty?->name ?? 'Unassigned' }}</div>
											<div class="text-xs text-gray-500 dark:text-gray-400">{{ $offering->faculty?->email }}</div>
										</td>
										<td class="px-3 py-3 whitespace-nowrap text-gray-700 dark:text-gray-300">
											T{{ $offering->term }} - {{ $offering->section }}
										</td>
										<td class="px-3 py-3 whitespace-nowrap">
											<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
												{{ $completion['completed'] }}/{{ $completion['total'] }}
											</span>
										</td>

										@foreach($requiredTypes as $type)
											<td class="px-3 py-3 min-w-[170px]">
												@php
													$isSyllabus = $type === 'syllabus';
													$isSampleIMs = $type === 'sample_ims';
												@endphp

												@if($isSyllabus || $isSampleIMs)
													@php
														$routeType = $isSyllabus ? 'syllabus' : 'im';
														$linkValue = $isSyllabus ? $offering->syllabus : $offering->instructional_material;
													@endphp
													@if($linkValue)
														<a href="{{ route('chair.subjects.download-document', ['classOffering' => $offering, 'type' => $routeType]) }}"
														   target="_blank"
														   class="text-indigo-600 dark:text-indigo-400 hover:underline">
															View Link
														</a>
														<div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Chair-managed</div>
													@else
														<span class="text-xs text-yellow-700 dark:text-yellow-300">Missing chair link</span>
													@endif
												@else
													@php $typeItems = $itemsByType->get($type, collect()); @endphp
													@if($typeItems->isEmpty())
														<span class="text-xs text-gray-500 dark:text-gray-400">Missing</span>
													@else
														<div class="space-y-1">
															@foreach($typeItems->take(2) as $item)
																<a href="{{ route('portfolio-items.preview', [$portfolio, $item]) }}"
																   target="_blank"
																   class="block text-indigo-600 dark:text-indigo-400 hover:underline truncate">
																	{{ $item->title }}
																</a>
															@endforeach
															@if($typeItems->count() > 2)
																<span class="text-xs text-gray-500 dark:text-gray-400">+{{ $typeItems->count() - 2 }} more</span>
															@endif
														</div>
													@endif
												@endif
											</td>
										@endforeach

										<td class="px-3 py-3 whitespace-nowrap">
											<a href="{{ route('reviews.show', $portfolio) }}"
											   class="text-indigo-600 dark:text-indigo-400 hover:underline">
												Open Review
											</a>
										</td>
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
