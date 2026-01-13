<x-app-layout>
	<x-slot name="header">
		<div class="flex justify-between items-center">
			<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
				{{ $subject->code }} - {{ $subject->title }}
			</h2>
			<a href="{{ route('chair.subjects.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">← Back to Subjects</a>
		</div>
	</x-slot>

	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
			{{-- Subject Details --}}
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Subject Information</h3>
				<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
					<div>
						<p class="text-sm text-gray-500 dark:text-gray-400">Subject Code</p>
						<p class="text-gray-900 dark:text-gray-100 font-medium">{{ $subject->code }}</p>
					</div>
					<div>
						<p class="text-sm text-gray-500 dark:text-gray-400">Year Level</p>
						<p class="text-gray-900 dark:text-gray-100 font-medium">Year {{ $subject->year_level }}</p>
					</div>
					<div>
						<p class="text-sm text-gray-500 dark:text-gray-400">Term</p>
						<p class="text-gray-900 dark:text-gray-100 font-medium">Term {{ $subject->term }}</p>
					</div>
					<div>
						<p class="text-sm text-gray-500 dark:text-gray-400">Credit Units</p>
						<p class="text-gray-900 dark:text-gray-100 font-medium">{{ $subject->credit_units }}</p>
					</div>
					<div>
						<p class="text-sm text-gray-500 dark:text-gray-400">Lecture Hours</p>
						<p class="text-gray-900 dark:text-gray-100 font-medium">{{ $subject->lec_hours }}</p>
					</div>
					<div>
						<p class="text-sm text-gray-500 dark:text-gray-400">Lab Hours</p>
						<p class="text-gray-900 dark:text-gray-100 font-medium">{{ $subject->lab_hours }}</p>
					</div>
					<div>
						<p class="text-sm text-gray-500 dark:text-gray-400">Prerequisites</p>
						<p class="text-gray-900 dark:text-gray-100 font-medium">{{ $subject->prereq ?: 'None' }}</p>
					</div>
					<div>
						<p class="text-sm text-gray-500 dark:text-gray-400">Co-requisites</p>
						<p class="text-gray-900 dark:text-gray-100 font-medium">{{ $subject->coreq ?: 'None' }}</p>
					</div>
				</div>
			</div>

			{{-- Assign Faculty Form --}}
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Assign Faculty</h3>

				@if(session('status'))
					<div class="mb-4 bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-800 text-green-600 dark:text-green-200 px-4 py-3 rounded">
						{{ session('status') }}
					</div>
				@endif

				@if($errors->any())
					<div class="mb-4 bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-200 px-4 py-3 rounded">
						<ul class="list-disc list-inside">
							@foreach($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif

				<form method="POST" action="{{ route('chair.subjects.assign', $subject) }}" enctype="multipart/form-data">
					@csrf
					<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
						<div>
							<label for="faculty_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
								Faculty <span class="text-red-500">*</span>
							</label>
							<select name="faculty_id" id="faculty_id" required
									class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
								<option value="">Select Faculty...</option>
								@foreach($availableFaculty as $faculty)
									<option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
								@endforeach
							</select>
							@error('faculty_id')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<div>
							<label for="academic_year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
								Academic Year <span class="text-red-500">*</span>
							</label>
							<input type="text" name="academic_year" id="academic_year" value="{{ $defaultAcademicYear ?? '2024-2025' }}" required
								   placeholder="2024-2025"
								   class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
							@error('academic_year')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<div>
							<label for="term" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
								Term <span class="text-red-500">*</span>
							</label>
							<select name="term" id="term" required
									class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
								<option value="1" {{ $subject->term == 1 ? 'selected' : '' }}>Term 1</option>
								<option value="2" {{ $subject->term == 2 ? 'selected' : '' }}>Term 2</option>
								<option value="3" {{ $subject->term == 3 ? 'selected' : '' }}>Summer</option>
							</select>
							@error('term')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<div>
							<label for="section" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
								Section <span class="text-red-500">*</span>
							</label>
							<input type="text" name="section" id="section" placeholder="1A" required
								   class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
							@error('section')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>
					</div>

					<div class="mt-4">
						<label for="assignment_document" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
							Teaching Load Document (Optional)
						</label>
						<input type="file" name="assignment_document" id="assignment_document" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
							   class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
						<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Upload teaching load document (PDF, DOC, DOCX, JPG, PNG - Max 5MB)</p>
						@error('assignment_document')
							<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
						@enderror
					</div>

					<div class="mt-4">
						<x-button type="submit">Assign Faculty</x-button>
					</div>
				</form>
			</div>

			{{-- Current Assignments --}}
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Current Assignments & Portfolio Status</h3>

				@if($subject->classOfferings->isEmpty())
					<p class="text-center text-gray-500 dark:text-gray-400 py-8">No faculty assigned yet.</p>
				@else
					@foreach($subject->classOfferings as $offering)
						<div class="mb-6 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
							<div class="flex justify-between items-start mb-3">
								<div>
									<h4 class="font-semibold text-gray-900 dark:text-gray-100">
										{{ $offering->faculty->name ?? 'Unassigned' }}
									</h4>
									<p class="text-sm text-gray-500 dark:text-gray-400">
										{{ $offering->academic_year }} · Term {{ $offering->term }} · Section {{ $offering->section }}
									</p>
								@if($offering->assignment_document)
									<a href="{{ route('chair.subjects.download-assignment', $offering) }}"
									   class="inline-flex items-center gap-1 text-xs text-indigo-600 dark:text-indigo-400 hover:underline mt-1">
										<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
										</svg>
										Download Teaching Load Document
									</a>
								@endif
								</div>
								<div class="flex items-center gap-2">
									@if($offering->portfolio)
										<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium
											{{ $offering->portfolio->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' :
											   ($offering->portfolio->status === 'submitted' ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' :
											   ($offering->portfolio->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' :
											   'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200')) }}">
											{{ ucfirst($offering->portfolio->status) }}
										</span>
									@else
										<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
											No Portfolio
										</span>
									@endif
									<form method="POST" action="{{ route('chair.subjects.remove-assignment', $offering) }}" class="inline"
										  onsubmit="return confirm('Are you sure you want to remove this assignment?');">
										@csrf
										@method('DELETE')
										<button type="submit" class="text-red-600 dark:text-red-400 hover:underline text-sm">
											Remove
										</button>
									</form>
								</div>
							</div>


						{{-- Upload/Update Teaching Load Document --}}
						<div class="mb-3 bg-gray-50 dark:bg-gray-700/50 rounded p-3">
							<h5 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">
								{{ $offering->assignment_document ? 'Update' : 'Upload' }} Teaching Load Document
							</h5>
							<form method="POST" action="{{ route('chair.subjects.upload-assignment', $offering) }}" enctype="multipart/form-data" class="flex items-end gap-2">
								@csrf
								<div class="flex-1">
									<input type="file" name="assignment_document" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required
										   class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
									<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">PDF, DOC, DOCX - Max 5MB</p>
								</div>
								<x-button type="submit" class="shrink-0">
									{{ $offering->assignment_document ? 'Update' : 'Upload' }}
								</x-button>
							</form>
							@error('assignment_document')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						{{-- Google Drive Link for Instructional Material (IM) --}}
						<div class="mb-3 bg-gray-50 dark:bg-gray-700/50 rounded p-3">
							<h5 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">
								{{ $offering->instructional_material ? 'Update' : 'Add' }} Instructional Material (IM) - Google Drive Link
							</h5>
							@if($offering->instructional_material)
								<div class="mb-2">
									@if(filter_var($offering->instructional_material, FILTER_VALIDATE_URL))
										<a href="{{ route('chair.subjects.download-document', ['classOffering' => $offering, 'type' => 'im']) }}"
										   target="_blank"
										   class="inline-flex items-center gap-1 text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
											<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
											</svg>
											View Current IM on Google Drive
										</a>
									@else
										<a href="{{ route('chair.subjects.download-document', ['classOffering' => $offering, 'type' => 'im']) }}"
										   class="inline-flex items-center gap-1 text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
											<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
											</svg>
											Download Current IM Document
										</a>
									@endif
								</div>
							@endif
							<form method="POST" action="{{ route('chair.subjects.upload-document', ['classOffering' => $offering, 'type' => 'im']) }}" class="flex items-end gap-2">
								@csrf
								<div class="flex-1">
									<input type="url" name="google_drive_link" value="{{ filter_var($offering->instructional_material ?? '', FILTER_VALIDATE_URL) ? $offering->instructional_material : '' }}" 
										   placeholder="https://drive.google.com/..." required
										   class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
									<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Enter Google Drive shareable link</p>
								</div>
								<x-button type="submit" class="shrink-0">
									{{ $offering->instructional_material ? 'Update' : 'Save' }}
								</x-button>
							</form>
							@error('google_drive_link')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						{{-- Google Drive Link for Syllabus --}}
						<div class="mb-3 bg-gray-50 dark:bg-gray-700/50 rounded p-3">
							<h5 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">
								{{ $offering->syllabus ? 'Update' : 'Add' }} Syllabus - Google Drive Link
							</h5>
							@if($offering->syllabus)
								<div class="mb-2">
									@if(filter_var($offering->syllabus, FILTER_VALIDATE_URL))
										<a href="{{ route('chair.subjects.download-document', ['classOffering' => $offering, 'type' => 'syllabus']) }}"
										   target="_blank"
										   class="inline-flex items-center gap-1 text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
											<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
											</svg>
											View Current Syllabus on Google Drive
										</a>
									@else
										<a href="{{ route('chair.subjects.download-document', ['classOffering' => $offering, 'type' => 'syllabus']) }}"
										   class="inline-flex items-center gap-1 text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
											<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
											</svg>
											Download Current Syllabus
										</a>
									@endif
								</div>
							@endif
							<form method="POST" action="{{ route('chair.subjects.upload-document', ['classOffering' => $offering, 'type' => 'syllabus']) }}" class="flex items-end gap-2">
								@csrf
								<div class="flex-1">
									<input type="url" name="google_drive_link" value="{{ filter_var($offering->syllabus ?? '', FILTER_VALIDATE_URL) ? $offering->syllabus : '' }}" 
										   placeholder="https://drive.google.com/..." required
										   class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
									<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Enter Google Drive shareable link</p>
								</div>
								<x-button type="submit" class="shrink-0">
									{{ $offering->syllabus ? 'Update' : 'Save' }}
								</x-button>
							</form>
							@error('google_drive_link')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>
							@if($offering->portfolio)
								@php
									$portfolio = $offering->portfolio;
									$requiredTypes = config('portfolio.required_items');
									$itemTypes = config('portfolio.item_types');
									$uploadedItems = $portfolio->items->groupBy('type');
									$uploadedCount = $uploadedItems->count();
									$totalRequired = count($requiredTypes);
									$percentage = $totalRequired > 0 ? ($uploadedCount / $totalRequired) * 100 : 0;
								@endphp

								<div class="mb-3">
									<div class="flex items-center justify-between mb-1">
										<span class="text-sm text-gray-600 dark:text-gray-400">
											Document Progress: {{ $uploadedCount }}/{{ $totalRequired }} required documents
										</span>
										<span class="text-sm font-medium text-gray-900 dark:text-gray-100">
											{{ number_format($percentage, 0) }}%
										</span>
									</div>
									<div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
										<div class="bg-indigo-600 h-2 rounded-full transition-all" style="width: {{ min($percentage, 100) }}%"></div>
									</div>
								</div>

								<div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3">
									<h5 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">Uploaded Documents:</h5>
									<div class="grid grid-cols-2 md:grid-cols-3 gap-2">
										@foreach($requiredTypes as $type)
											@php
												$hasUpload = $uploadedItems->has($type);
												$label = $itemTypes[$type] ?? $type;
											@endphp
											<div class="flex items-center gap-1 text-xs">
												@if($hasUpload)
													<svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
														<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
													</svg>
													<span class="text-green-700 dark:text-green-300">{{ $label }}</span>
												@else
													<svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
														<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd"/>
													</svg>
													<span class="text-gray-500 dark:text-gray-400">{{ $label }}</span>
												@endif
											</div>
										@endforeach
									</div>

									@if($portfolio->items->count() > 0)
										<div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-600">
											<p class="text-xs text-gray-500 dark:text-gray-400">
												Total files uploaded: {{ $portfolio->items->count() }}
												@if($portfolio->submitted_at)
													· Submitted: {{ $portfolio->submitted_at->format('M d, Y') }}
												@endif
											</p>
										</div>
									@endif
								</div>
							@else
								<div class="bg-yellow-50 dark:bg-yellow-900/20 rounded p-3">
									<p class="text-sm text-yellow-800 dark:text-yellow-200">
										Faculty has not created a portfolio yet.
									</p>
								</div>
							@endif
						</div>
					@endforeach
				@endif
			</div>
		</div>
	</div>
</x-app-layout>
