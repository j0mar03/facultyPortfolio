<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Faculty Activity Tracking') }}
		</h2>
	</x-slot>

	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
			{{-- Filtering and Summary --}}
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
				<div class="flex items-center justify-between flex-wrap gap-4">
					<form method="GET" action="{{ route('reports.activity') }}" class="flex items-center gap-4 flex-wrap">
						<div class="flex items-center gap-2">
							<label for="course_id" class="text-sm font-medium text-gray-700 dark:text-gray-300">Dept:</label>
							<select name="course_id" id="course_id" class="text-sm rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200" onchange="this.form.submit()">
								<option value="">All Departments</option>
								@foreach($managedCourses as $course)
									<option value="{{ $course->id }}" {{ $selectedCourseId == $course->id ? 'selected' : '' }}>{{ $course->code }}</option>
								@endforeach
							</select>
						</div>

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
								@foreach($availableTerms as $term)
									<option value="{{ $term }}" {{ $selectedTerm == $term ? 'selected' : '' }}>Term {{ $term }}</option>
								@endforeach
							</select>
						</div>

						<div class="flex items-center gap-2">
							<label for="sort_by" class="text-sm font-medium text-gray-700 dark:text-gray-300">Sort By:</label>
							<select name="sort_by" id="sort_by" class="text-sm rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200" onchange="this.form.submit()">
								<option value="last_activity" {{ $sortBy === 'last_activity' ? 'selected' : '' }}>Last Activity</option>
								<option value="portfolios" {{ $sortBy === 'portfolios' ? 'selected' : '' }}>Portfolios Created</option>
								<option value="completion" {{ $sortBy === 'completion' ? 'selected' : '' }}>Completion %</option>
								<option value="name" {{ $sortBy === 'name' ? 'selected' : '' }}>Faculty Name</option>
							</select>
						</div>
					</form>
                    
                    <div class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                        Period: {{ $selectedYear }} | Term {{ $selectedTerm }}
                    </div>
				</div>
			</div>

			{{-- Activity Table --}}
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
				<div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/10 flex items-center justify-between">
					<div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200">Faculty Portfolio Progress</h3>
					    <p class="text-sm text-gray-600 dark:text-gray-400">Detailed tracking of faculty portfolio submissions and document completion.</p>
                    </div>
				</div>
				<div class="p-0">
					@if(empty($facultyActivity))
						<div class="p-6 text-center text-gray-500 dark:text-gray-400">No faculty members found for this criteria.</div>
					@else
						<div class="overflow-x-auto">
							<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
								<thead class="bg-gray-50 dark:bg-gray-700">
									<tr>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Faculty Name</th>
										<th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
										<th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Portfolios (Created/Total)</th>
										<th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Documents</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status Breakdown</th>
									</tr>
								</thead>
								<tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
									@foreach($facultyActivity as $data)
										<tr class="hover:bg-gray-50 dark:hover:bg-gray-900/40 transition">
											<td class="px-6 py-4 whitespace-nowrap">
												<div class="flex items-center">
													<div class="flex-shrink-0 h-10 w-10">
														<img class="h-10 w-10 rounded-full object-cover border-2 border-gray-200" src="{{ $data['faculty']->profile_photo_url }}" alt="{{ $data['faculty']->name }}">
													</div>
													<div class="ml-4">
														<div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $data['faculty']->name }}</div>
														<div class="text-xs text-gray-500 dark:text-gray-400 font-semibold">{{ $data['faculty']->course->code ?? 'N/A' }}</div>
													</div>
												</div>
											</td>
											<td class="px-6 py-4 whitespace-nowrap text-center">
												<span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $data['status'] === 'Complete' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
													{{ $data['status'] }}
												</span>
											</td>
											<td class="px-6 py-4 whitespace-nowrap text-center">
                                                <div class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                                    {{ $data['portfolios_created'] }} / {{ $data['total_subjects'] }}
                                                </div>
                                                <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 mt-1 mx-auto">
                                                    <div class="bg-indigo-500 h-1.5 rounded-full" style="width: {{ $data['total_subjects'] > 0 ? ($data['portfolios_created'] / $data['total_subjects']) * 100 : 0 }}%"></div>
                                                </div>
											</td>
											<td class="px-6 py-4 whitespace-nowrap text-center">
                                                <div class="text-sm font-bold {{ $data['percentage'] >= 100 ? 'text-green-600' : 'text-gray-900 dark:text-gray-100' }}">
                                                    {{ number_format($data['percentage'], 0) }}%
                                                </div>
                                                <div class="text-[10px] text-gray-500 dark:text-gray-400">
                                                    {{ $data['completed_docs'] }} / {{ $data['total_docs'] }} documents
                                                </div>
											</td>
											<td class="px-6 py-4 text-sm">
                                                <div class="flex flex-wrap gap-1">
                                                    @if($data['approved_count'] > 0)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-green-100 text-green-800">
                                                            {{ $data['approved_count'] }} App
                                                        </span>
                                                    @endif
                                                    @if($data['submitted_count'] > 0)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-100 text-blue-800">
                                                            {{ $data['submitted_count'] }} Sub
                                                        </span>
                                                    @endif
                                                    @if($data['draft_count'] > 0)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-gray-100 text-gray-600">
                                                            {{ $data['draft_count'] }} Draft
                                                        </span>
                                                    @endif
                                                    @if($data['rejected_count'] > 0)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-red-100 text-red-800">
                                                            {{ $data['rejected_count'] }} Rej
                                                        </span>
                                                    @endif
                                                    
                                                    @php $notStarted = $data['total_subjects'] - $data['portfolios_created']; @endphp
                                                    @if($notStarted > 0)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-gray-200 text-gray-400">
                                                            {{ $notStarted }} None
                                                        </span>
                                                    @endif
                                                </div>
                                                
                                                @if($data['last_update'])
                                                    <div class="mt-2 text-[10px] text-gray-400">
                                                        Last activity: {{ \Carbon\Carbon::parse($data['last_update'])->diffForHumans() }}
                                                    </div>
                                                @endif
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
	</div>
</x-app-layout>
