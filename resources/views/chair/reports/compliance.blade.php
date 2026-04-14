<x-app-layout>
	<x-slot name="header">
		<div class="flex justify-between items-center">
			<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
				{{ __('Audit Compliance Matrix') }} - {{ $selectedCourse->name }}
			</h2>
			<button onclick="window.print()" class="bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-bold py-1 px-3 rounded inline-flex items-center gap-1 print:hidden">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
				</svg>
				Print for Audit
			</button>
		</div>
	</x-slot>

	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
			{{-- Course Tabs (Hidden on Print) --}}
			@if($managedCourses->count() > 1)
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg print:hidden">
				<div class="border-b border-gray-200 dark:border-gray-700">
					<nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
						@foreach($managedCourses as $course)
							<a href="{{ route('reports.compliance', ['course_id' => $course->id, 'academic_year' => $selectedYear, 'term' => $selectedTerm]) }}"
							   class="@if($course->id === $selectedCourse->id) border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
								{{ $course->code }}
							</a>
						@endforeach
					</nav>
				</div>
			</div>
			@endif

			{{-- Compliance Monitoring Summary --}}
			<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
				<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-green-500">
					<div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['approved'] }}</div>
					<div class="text-sm text-gray-600 dark:text-gray-400 uppercase tracking-wider font-semibold">Approved Subjects</div>
				</div>
				<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-blue-500">
					<div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['in_progress'] }}</div>
					<div class="text-sm text-gray-600 dark:text-gray-400 uppercase tracking-wider font-semibold">In Progress</div>
				</div>
				<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-red-500">
					<div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['not_started'] }}</div>
					<div class="text-sm text-gray-600 dark:text-gray-400 uppercase tracking-wider font-semibold">Not Started</div>
				</div>
				<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-indigo-500">
					<div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
						{{ number_format(($stats['total'] > 0 ? ($stats['approved'] / $stats['total']) * 100 : 0), 1) }}%
					</div>
					<div class="text-sm text-gray-600 dark:text-gray-400 uppercase tracking-wider font-semibold">Overall Compliance</div>
				</div>
			</div>

			{{-- Filters --}}
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 print:hidden">
				<form method="GET" action="{{ route('reports.compliance') }}" class="flex items-center gap-6 flex-wrap">
					<input type="hidden" name="course_id" value="{{ $selectedCourse->id }}">
					
					<div class="flex items-center gap-2">
						<label for="academic_year" class="text-sm font-medium text-gray-700 dark:text-gray-300">AY:</label>
						<select name="academic_year" id="academic_year" class="text-sm rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" onchange="this.form.submit()">
							@foreach($availableYears as $year)
								<option value="{{ $year }}" {{ $selectedYear === $year ? 'selected' : '' }}>{{ $year }}</option>
							@endforeach
						</select>
					</div>

					<div class="flex items-center gap-2">
						<label for="term" class="text-sm font-medium text-gray-700 dark:text-gray-300">Term:</label>
						<select name="term" id="term" class="text-sm rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" onchange="this.form.submit()">
							@foreach($availableTerms as $term)
								<option value="{{ $term }}" {{ $selectedTerm == $term ? 'selected' : '' }}>Term {{ $term }}</option>
							@endforeach
						</select>
					</div>

					<div class="text-xs text-gray-500">
						Excludes GEED, NSTP, PATHFIT, CHEM, and MATH.
					</div>
				</form>
			</div>

			{{-- Detailed Tables grouped by Year/Term --}}
			@foreach($subjects as $groupName => $groupSubjects)
				<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
					<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ $groupName }}</h3>

					<div class="overflow-x-auto">
						<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
							<thead class="bg-gray-50 dark:bg-gray-700">
								<tr>
									<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Subject</th>
									<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Faculty Assigned</th>
									<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider text-center">IM</th>
									<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider text-center">Teaching Load</th>
									<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider text-center">Syllabus</th>
									<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Docs Matrix</th>
									<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
								</tr>
							</thead>
							<tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
								@foreach($groupSubjects as $subject)
									@php
										$offerings = $subject->classOfferings;
									@endphp
									
									@if($offerings->isEmpty())
										<tr class="hover:bg-gray-50 dark:hover:bg-gray-900/40">
											<td class="px-4 py-4 whitespace-nowrap">
												<div class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $subject->code }}</div>
												<div class="text-xs text-gray-500 truncate max-w-[200px]">{{ $subject->title }}</div>
											</td>
											<td colspan="5" class="px-4 py-4 text-center text-xs text-red-500 italic">No faculty assigned for this term.</td>
											<td class="px-4 py-4">
												<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
													Not Started
												</span>
											</td>
										</tr>
									@else
										@foreach($offerings as $offering)
											@php
												$portfolio = $offering->portfolio;
												$stats = $portfolio ? $portfolio->completionStats() : ['completed' => 0, 'total' => count($requiredItems), 'percentage' => 0, 'uploaded_types' => []];
												$uploadedTypes = $stats['uploaded_types'];
												
												// Backward compatibility for view icons if portfolio doesn't exist yet but offering has docs
												if (!$portfolio) {
													if ($offering->instructional_material) $uploadedTypes[] = 'sample_ims';
													if ($offering->syllabus) $uploadedTypes[] = 'syllabus';
													if ($offering->assignment_document) $uploadedTypes[] = 'faculty_assignment';
												}

												$completion = $stats;
											@endphp
											<tr class="hover:bg-gray-50 dark:hover:bg-gray-900/40">
												<td class="px-4 py-4 whitespace-nowrap border-r dark:border-gray-700">
													<div class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $subject->code }}</div>
													<div class="text-[10px] text-gray-500 truncate max-w-[150px]">{{ $subject->title }}</div>
												</td>
												<td class="px-4 py-4 whitespace-nowrap border-r dark:border-gray-700">
													<div class="text-sm text-gray-900 dark:text-gray-100 font-medium">{{ $offering->faculty->name ?? 'N/A' }}</div>
													<div class="text-[10px] text-gray-500">Section {{ $offering->section }}</div>
												</td>
												{{-- IM Column --}}
												<td class="px-4 py-4 whitespace-nowrap text-center border-r dark:border-gray-700">
													@if(in_array('sample_ims', $uploadedTypes))
														<span class="text-green-600 dark:text-green-400 inline-block">
															<svg class="w-5 h-5 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
														</span>
													@else
														<span class="text-red-500 inline-block"><svg class="w-5 h-5 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg></span>
													@endif
												</td>
												{{-- Teaching Load Column --}}
												<td class="px-4 py-4 whitespace-nowrap text-center border-r dark:border-gray-700">
													@if(in_array('faculty_assignment', $uploadedTypes))
														<span class="text-green-600 dark:text-green-400 inline-block">
															<svg class="w-5 h-5 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
														</span>
													@else
														<span class="text-red-500 inline-block"><svg class="w-5 h-5 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg></span>
													@endif
												</td>
												{{-- Syllabus Column --}}
												<td class="px-4 py-4 whitespace-nowrap text-center border-r dark:border-gray-700">
													@if(in_array('syllabus', $uploadedTypes))
														<span class="text-green-600 dark:text-green-400 inline-block">
															<svg class="w-5 h-5 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
														</span>
													@else
														<span class="text-red-500 inline-block"><svg class="w-5 h-5 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg></span>
													@endif
												</td>
												{{-- Matrix Dots Column --}}
												<td class="px-4 py-4 whitespace-nowrap border-r dark:border-gray-700">
													<div class="flex gap-0.5 items-center">
														@foreach($requiredItems as $itemType)
															@php
																$hasDoc = in_array($itemType, $uploadedTypes);
																$dotColor = 'bg-gray-200 dark:bg-gray-700';
																if ($hasDoc) {
																	if ($portfolio) {
																		if ($portfolio->status === 'approved') $dotColor = 'bg-green-500';
																		elseif ($portfolio->status === 'submitted') $dotColor = 'bg-blue-500';
																		else $dotColor = 'bg-yellow-500';
																	} else { $dotColor = 'bg-yellow-500'; }
																} else { $dotColor = 'bg-red-500'; }
															@endphp
															<div class="w-2 h-2 {{ $dotColor }} rounded-full" title="{{ $itemTypes[$itemType] ?? $itemType }}"></div>
														@endforeach
														<span class="ml-2 text-[10px] font-bold {{ $completion['percentage'] >= 100 ? 'text-green-600' : 'text-gray-500' }}">
															{{ number_format($completion['percentage'], 0) }}%
														</span>
													</div>
												</td>
												{{-- Final Status --}}
												<td class="px-4 py-4 whitespace-nowrap">
													@if($portfolio)
														<div class="flex items-center gap-2">
															<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase
																{{ $portfolio->status === 'approved' ? 'bg-green-100 text-green-800' :
																   ($portfolio->status === 'submitted' ? 'bg-blue-100 text-blue-800' :
																   ($portfolio->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) }}">
																{{ $portfolio->status }}
															</span>
															<a href="{{ route('reviews.show', $portfolio) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-xs font-semibold print:hidden">
																View Docs
															</a>
														</div>
													@else
														<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
															Not Started
														</span>
													@endif
												</td>
											</tr>
										@endforeach
									@endif
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			@endforeach
		</div>
	</div>

	<style>
		@media print {
			@page { size: landscape; margin: 0.5cm; }
			body { background: white; color: black; }
			.py-12 { padding-top: 0; padding-bottom: 0; }
			.max-w-7xl { max-width: 100%; width: 100%; margin: 0; }
			.shadow-xl { shadow: none; }
			.dark\:bg-gray-800 { background-color: white !important; }
			.dark\:text-gray-100 { color: black !important; }
			.dark\:border-gray-700 { border-color: #ddd !important; }
		}
	</style>
</x-app-layout>
