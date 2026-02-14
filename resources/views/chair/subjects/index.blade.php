<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Subjects Management') }} - {{ $selectedCourse->name }}
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
							<a href="{{ route('chair.subjects.index', ['course_id' => $course->id, 'academic_year' => $selectedYear]) }}"
							   class="@if($course->id === $selectedCourse->id) border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
								{{ $course->code }}
							</a>
						@endforeach
					</nav>
				</div>
			</div>
			@endif

			{{-- Academic Year & Term Filter & Document Toggle --}}
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
				<div class="flex items-center justify-between flex-wrap gap-4">
					<form method="GET" action="{{ route('chair.subjects.index') }}" class="flex items-center gap-4">
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
						
						<label for="term" class="text-sm font-medium text-gray-700 dark:text-gray-300 ml-4">
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
						
						<span class="text-sm text-gray-500 dark:text-gray-400">
							Showing {{ $selectedTerm !== null ? 'Term ' . $selectedTerm . ' for ' : '' }}{{ $selectedYear }}
						</span>
					</form>

					{{-- Document Columns Toggle --}}
					<div class="flex items-center gap-2">
						<label class="inline-flex items-center cursor-pointer">
							<input type="checkbox" id="toggleDocuments" class="sr-only peer" checked>
							<div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
							<span class="ms-3 text-sm font-medium text-gray-700 dark:text-gray-300">Show Documents</span>
						</label>
					</div>
				</div>
			</div>

			@foreach($subjects as $groupName => $groupSubjects)
				<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
					<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ $groupName }}</h3>

					<div class="overflow-x-auto">
						<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
							<thead class="bg-gray-50 dark:bg-gray-700">
								<tr>
									<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Code</th>
									<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Title</th>
									<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Units</th>
									<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Assigned Faculty</th>
									<th class="document-column px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">IM</th>
									<th class="document-column px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Teaching Load</th>
									<th class="document-column px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Syllabus</th>
									<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Portfolio Status</th>
									<th class="px-4 py-2"></th>
								</tr>
							</thead>
							<tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
								@foreach($groupSubjects as $subject)
									<tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
										<td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
											{{ $subject->code }}
										</td>
										<td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
											<div>{{ $subject->title }}</div>
											<div class="text-xs text-gray-500 dark:text-gray-400">
												{{ $subject->lec_hours }}L / {{ $subject->lab_hours }}Lab
												@if($subject->prereq)
													· Prereq: {{ $subject->prereq }}
												@endif
											</div>
										</td>
										<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
											{{ $subject->credit_units }}
										</td>
										<td class="px-4 py-3 text-sm">
											@if($subject->classOfferings->isEmpty())
												<span class="text-gray-400 dark:text-gray-500 italic">Not assigned</span>
											@else
												@foreach($subject->classOfferings as $offering)
													<div class="mb-1">
														<span class="text-gray-900 dark:text-gray-100 font-medium">{{ $offering->faculty->name ?? 'N/A' }}</span>
														<span class="text-xs text-gray-500 dark:text-gray-400">
															({{ $offering->academic_year }}, T{{ $offering->term }}, Sec {{ $offering->section }})
														</span>
													</div>
												@endforeach
											@endif
										</td>
										{{-- IM Column --}}
										<td class="document-column px-4 py-3 text-sm">
											@if($subject->classOfferings->isEmpty())
												<span class="text-gray-400 dark:text-gray-500">-</span>
											@else
												@foreach($subject->classOfferings as $offering)
													<div class="mb-2 min-w-[120px]">
														@if($offering->instructional_material)
															<a href="{{ route('chair.subjects.download-document', ['classOffering' => $offering, 'type' => 'im']) }}"
															   target="{{ filter_var($offering->instructional_material, FILTER_VALIDATE_URL) ? '_blank' : '_self' }}"
															   class="inline-flex items-center gap-1 text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
																<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
																	<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ filter_var($offering->instructional_material, FILTER_VALIDATE_URL) ? 'M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14' : 'M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' }}" />
																</svg>
																View
															</a>
														@else
															<span class="text-xs text-gray-400 dark:text-gray-500">Not linked</span>
														@endif
													</div>
												@endforeach
											@endif
										</td>
										{{-- Teaching Load Column --}}
										<td class="document-column px-4 py-3 text-sm">
											@if($subject->classOfferings->isEmpty())
												<span class="text-gray-400 dark:text-gray-500">-</span>
											@else
												@foreach($subject->classOfferings as $offering)
													<div class="mb-2 min-w-[120px]">
														@if($offering->assignment_document)
															<a href="{{ route('chair.subjects.download-assignment', $offering) }}"
															   class="inline-flex items-center gap-1 text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
																<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
																	<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
																</svg>
																View
															</a>
														@else
															<span class="text-xs text-gray-400 dark:text-gray-500">Not uploaded</span>
														@endif
													</div>
												@endforeach
											@endif
										</td>
										{{-- Syllabus Column --}}
										<td class="document-column px-4 py-3 text-sm">
											@if($subject->classOfferings->isEmpty())
												<span class="text-gray-400 dark:text-gray-500">-</span>
											@else
												@foreach($subject->classOfferings as $offering)
													<div class="mb-2 min-w-[120px]">
														@if($offering->syllabus)
															<a href="{{ route('chair.subjects.download-document', ['classOffering' => $offering, 'type' => 'syllabus']) }}"
															   target="{{ filter_var($offering->syllabus, FILTER_VALIDATE_URL) ? '_blank' : '_self' }}"
															   class="inline-flex items-center gap-1 text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
																<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
																	<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ filter_var($offering->syllabus, FILTER_VALIDATE_URL) ? 'M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14' : 'M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' }}" />
																</svg>
																View
															</a>
														@else
															<span class="text-xs text-gray-400 dark:text-gray-500">Not linked</span>
														@endif
													</div>
												@endforeach
											@endif
										</td>
										{{-- Portfolio Status Column --}}
										<td class="px-4 py-3 text-sm">
											@if($subject->classOfferings->isEmpty())
												<span class="text-gray-400 dark:text-gray-500">-</span>
											@else
												@foreach($subject->classOfferings as $offering)
													@if($offering->portfolio)
														@php
															$completion = $offering->portfolio->completionStats();
														@endphp
														<div class="mb-2">
															<div class="flex items-center gap-2">
																<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
																	{{ $offering->portfolio->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' :
																	   ($offering->portfolio->status === 'submitted' ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' :
																	   ($offering->portfolio->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' :
																	   'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200')) }}">
																	{{ ucfirst($offering->portfolio->status) }}
																</span>
																<span class="text-xs text-gray-500 dark:text-gray-400">
																	{{ $completion['completed'] }}/{{ $completion['total'] }} docs
																</span>
															</div>
															<div class="mt-1 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
																<div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ min($completion['percentage'], 100) }}%"></div>
															</div>
														</div>
													@else
														<div class="mb-1">
															<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
																No Portfolio
															</span>
														</div>
													@endif
												@endforeach
											@endif
										</td>
										<td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
											<a href="{{ route('chair.subjects.show', ['subject' => $subject, 'academic_year' => $selectedYear]) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
												Manage
											</a>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			@endforeach
		</div>
	</div>

	{{-- Document Toggle Script --}}
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const toggle = document.getElementById('toggleDocuments');
			const documentColumns = document.querySelectorAll('.document-column');

			// Load saved preference from localStorage
			const showDocuments = localStorage.getItem('showDocuments');
			if (showDocuments === 'false') {
				toggle.checked = false;
				documentColumns.forEach(col => col.style.display = 'none');
			}

			// Handle toggle change
			toggle.addEventListener('change', function() {
				const isChecked = this.checked;

				documentColumns.forEach(col => {
					if (isChecked) {
						col.style.display = '';
					} else {
						col.style.display = 'none';
					}
				});

				// Save preference to localStorage
				localStorage.setItem('showDocuments', isChecked);
			});
		});
	</script>
</x-app-layout>
