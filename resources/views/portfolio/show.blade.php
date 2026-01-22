<x-app-layout>
	<x-slot name="header">
		<div class="flex justify-between items-center">
			<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
				{{ __('Portfolio') }}
				@if($portfolio->classOffering && $portfolio->classOffering->subject)
					— {{ $portfolio->classOffering->subject->code }} ({{ $portfolio->classOffering->academic_year }} / T{{ $portfolio->classOffering->term }}, Sec {{ $portfolio->classOffering->section }})
				@endif
			</h2>
			<a href="{{ route('dashboard') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">← Back to Dashboard</a>
		</div>
	</x-slot>

	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
			{{-- Status and Submit Section --}}
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
				<div class="flex justify-between items-center">
					<div>
						<span class="inline-flex items-center px-3 py-1 rounded text-sm font-medium
							{{ $portfolio->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' :
							   ($portfolio->status === 'submitted' ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' :
							   ($portfolio->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' :
							   'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200')) }}">
							Status: {{ ucfirst($portfolio->status) }}
						</span>
						@if($portfolio->submitted_at)
							<p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
								Submitted: {{ $portfolio->submitted_at->format('M d, Y h:i A') }}
							</p>
						@endif
					</div>
					@if(in_array($portfolio->status, ['draft', 'rejected']))
						@php
							$requiredTypes = config('portfolio.required_items');
							$itemTypes = config('portfolio.item_types');
							$uploadedTypes = $portfolio->items->pluck('type')->unique()->toArray();
							
							// Check for Syllabus and Sample IMs from class offering
							$hasSyllabus = false;
							$hasIM = false;
							
							if ($portfolio->classOffering) {
								$hasSyllabus = !empty($portfolio->classOffering->syllabus) && filter_var($portfolio->classOffering->syllabus, FILTER_VALIDATE_URL);
								$hasIM = !empty($portfolio->classOffering->instructional_material) && filter_var($portfolio->classOffering->instructional_material, FILTER_VALIDATE_URL);
							}
							
							if ($hasSyllabus && in_array('syllabus', $requiredTypes)) {
								$uploadedTypes[] = 'syllabus';
							}
							if ($hasIM && in_array('sample_ims', $requiredTypes)) {
								$uploadedTypes[] = 'sample_ims';
							}
							
							$missingTypes = array_diff($requiredTypes, $uploadedTypes);
							$isComplete = empty($missingTypes);
							$uploadedCount = count(array_intersect($requiredTypes, $uploadedTypes));
							$totalRequired = count($requiredTypes);
						@endphp
						
						<div class="text-right">
							@if(!$isComplete)
								<div class="mb-2">
									<p class="text-sm text-red-600 dark:text-red-400 font-medium">
										{{ $uploadedCount }}/{{ $totalRequired }} documents uploaded
									</p>
									<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
										Missing: {{ implode(', ', array_map(function($type) use ($itemTypes) { return $itemTypes[$type] ?? $type; }, $missingTypes)) }}
									</p>
								</div>
							@else
								<p class="text-sm text-green-600 dark:text-green-400 font-medium mb-2">
									All required documents uploaded ✓
								</p>
							@endif
							@php
								$submitAction = $portfolio->status === 'rejected' ? 're' : '';
								$confirmMessage = "Are you sure you want to {$submitAction}submit this portfolio? You cannot edit it after submission.";
								if ($isComplete) {
									$onsubmitAttr = "return confirm('" . addslashes($confirmMessage) . "');";
								} else {
									$onsubmitAttr = "alert('Please upload all required documents before submitting.'); return false;";
								}
							@endphp
							<form method="POST" action="{{ route('portfolios.submit', $portfolio) }}" 
								  id="submit-form"
								  onsubmit="{{ $onsubmitAttr }}">
								@csrf
								@if($isComplete)
									<x-button type="submit" class="bg-green-600 hover:bg-green-700">
										{{ $portfolio->status === 'rejected' ? 'Resubmit for Review' : 'Submit for Review' }}
									</x-button>
								@else
									<x-button type="submit" class="bg-green-600 hover:bg-green-700 opacity-50 cursor-not-allowed" disabled>
										{{ $portfolio->status === 'rejected' ? 'Resubmit for Review' : 'Submit for Review' }}
									</x-button>
								@endif
							</form>
						</div>
					@endif
				</div>

				@if($errors->has('submit'))
					<div class="mt-4 bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-200 px-4 py-3 rounded">
						{{ $errors->first('submit') }}
					</div>
				@endif

				@if(session('status'))
					<div class="mt-4 bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-800 text-green-600 dark:text-green-200 px-4 py-3 rounded">
						{{ session('status') }}
					</div>
				@endif

				{{-- Review Feedback --}}
				@php
					$latestReview = $portfolio->reviews()->latest()->first();
				@endphp
				@if($latestReview)
					<div class="mt-4 border-l-4 {{ $latestReview->decision === 'approved' ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-red-500 bg-red-50 dark:bg-red-900/20' }} p-4">
						<div class="flex items-center mb-2">
							<span class="font-semibold text-gray-900 dark:text-gray-100">
								{{ $latestReview->decision === 'approved' ? 'Approved' : 'Rejected' }}
							</span>
							<span class="text-sm text-gray-600 dark:text-gray-400 ml-2">
								by {{ $latestReview->reviewer->name }} · {{ $latestReview->created_at->diffForHumans() }}
							</span>
						</div>
						@if($latestReview->remarks)
							<p class="text-sm text-gray-700 dark:text-gray-300"><strong>Remarks:</strong> {{ $latestReview->remarks }}</p>
						@endif
						@if($portfolio->status === 'rejected')
							<div class="mt-3 pt-3 border-t border-red-200 dark:border-red-800">
								<p class="text-sm text-gray-700 dark:text-gray-300">
									<strong>Action Required:</strong> Please review the feedback above, update your documents as needed, and resubmit your portfolio for review.
								</p>
							</div>
						@endif
					</div>
				@endif
			</div>

			{{-- Portfolio Items Checklist --}}
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Required Documents</h3>
				<p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Upload all required documents to submit your portfolio.</p>

				<div class="space-y-4">
					@php
						$itemTypes = config('portfolio.item_types');
						$requiredTypes = config('portfolio.required_items');
						$uploadedItems = $portfolio->items->groupBy('type');
						$reusableTypes = ['sample_quiz', 'major_exam', 'tos', 'activity_rubrics'];
					@endphp

					@foreach($itemTypes as $type => $label)
						@php
							$isRequired = in_array($type, $requiredTypes);
							$items = $uploadedItems->get($type, collect());
							$hasUpload = $items->isNotEmpty();
							
							// Check if this is Syllabus or Sample IMs - these come from class offering Google Drive links
							$isSyllabus = $type === 'syllabus';
							$isSampleIMs = $type === 'sample_ims';
							$isFromClassOffering = $isSyllabus || $isSampleIMs;
							
							// Get Google Drive link from class offering
							$googleDriveLink = null;
							if ($portfolio->classOffering) {
								if ($isSyllabus && !empty($portfolio->classOffering->syllabus)) {
									$googleDriveLink = filter_var($portfolio->classOffering->syllabus, FILTER_VALIDATE_URL) 
										? $portfolio->classOffering->syllabus 
										: null;
								} elseif ($isSampleIMs && !empty($portfolio->classOffering->instructional_material)) {
									$googleDriveLink = filter_var($portfolio->classOffering->instructional_material, FILTER_VALIDATE_URL) 
										? $portfolio->classOffering->instructional_material 
										: null;
								}
							}
						@endphp

						<div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
							<div class="flex items-start justify-between">
								<div class="flex-1">
									<div class="flex items-center gap-2">
										@if($hasUpload || ($isFromClassOffering && $googleDriveLink))
											<svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
												<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
											</svg>
										@else
											<svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
												<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd"/>
											</svg>
										@endif
										<h4 class="text-md font-medium text-gray-900 dark:text-gray-100">
											{{ $label }}
											@if($isRequired)
												<span class="text-red-500">*</span>
											@endif
											@if($isFromClassOffering)
												<span class="text-xs text-gray-500 dark:text-gray-400 font-normal">(Managed by Chair)</span>
											@endif
										</h4>
									</div>

									{{-- Show Google Drive link for Syllabus and Sample IMs from class offering --}}
									@if($isFromClassOffering && $googleDriveLink)
										<div class="mt-3">
											<div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded px-3 py-2">
												<div class="flex items-center gap-2">
													<svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
													</svg>
													<a href="{{ $googleDriveLink }}" target="_blank" 
													   class="text-sm text-blue-600 dark:text-blue-400 hover:underline break-all">
														{{ $googleDriveLink }}
													</a>
												</div>
												<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
													This link is managed by the Chair. Contact your Chair to update it.
												</p>
											</div>
										</div>
									@elseif($isFromClassOffering && !$googleDriveLink)
										<div class="mt-3">
											<div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded px-3 py-2">
												<p class="text-sm text-yellow-800 dark:text-yellow-200">
													No Google Drive link provided yet. Please contact your Chair to add the link.
												</p>
											</div>
										</div>
									@endif

									{{-- Display uploaded files (for non-class-offering types or if user uploaded additional files) --}}
									@if($hasUpload && !$isFromClassOffering)
										<div class="mt-3 space-y-2">
											@foreach($items as $item)
												@php
													$isFromLibrary = $item->faculty_document_id !== null;
												@endphp
												<div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 rounded px-3 py-2">
													<div class="flex items-center gap-2">
														@if($isFromLibrary)
															<svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20" title="From Library">
																<path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
															</svg>
														@else
															<svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
																<path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
															</svg>
														@endif
														<span class="text-sm text-gray-700 dark:text-gray-300">{{ $item->title }}</span>
														@if($isFromLibrary)
															<span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
																Library
															</span>
														@endif
														<span class="text-xs text-gray-500 dark:text-gray-400">
															({{ number_format((($item->metadata_json ?? [])['size'] ?? 0) / 1024, 2) }} KB)
														</span>
													</div>
													<div class="flex items-center gap-2">
														<a href="{{ route('portfolio-items.download', [$portfolio, $item]) }}"
														   class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm">
															Download
														</a>
														@if(in_array($portfolio->status, ['draft', 'rejected']))
															<form method="POST" action="{{ route('portfolio-items.destroy', [$portfolio, $item]) }}"
																  onsubmit="return confirm('Are you sure you want to remove this file from this portfolio?{{ $isFromLibrary ? ' The file will remain in your library.' : '' }}');">
																@csrf
																@method('DELETE')
																<button type="submit" class="text-red-600 dark:text-red-400 hover:underline text-sm">
																	Remove
																</button>
															</form>
														@endif
													</div>
												</div>
											@endforeach
										</div>
									@endif
								</div>

								{{-- Upload/Select form (only show if draft or rejected, and NOT for Syllabus/Sample IMs) --}}
								@if(in_array($portfolio->status, ['draft', 'rejected']) && !$isFromClassOffering)
									<div class="ml-4">
										@php
											$reusableTypesList = ['sample_quiz', 'major_exam', 'tos', 'activity_rubrics'];
											$isReusable = in_array($type, $reusableTypesList);
											$libraryDocuments = $isReusable ? \App\Models\FacultyDocument::where('user_id', Auth::id())
												->where('type', $type)
												->orderBy('created_at', 'desc')
												->get() : collect();
										@endphp
										
										@if($isReusable && $libraryDocuments->count() > 0)
											<div x-data="{ activeTab: 'upload' }">
												{{-- Tabs for Upload vs Library --}}
												<div class="mb-2 border-b border-gray-200 dark:border-gray-700">
													<nav class="-mb-px flex space-x-4">
														<button type="button" @click="activeTab = 'upload'" 
																:class="activeTab === 'upload' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
																class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
															Upload New
														</button>
														<button type="button" @click="activeTab = 'library'" 
																:class="activeTab === 'library' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
																class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
															Select from Library ({{ $libraryDocuments->count() }})
														</button>
													</nav>
												</div>

												{{-- Upload Form --}}
												<div x-show="activeTab === 'upload'" x-cloak class="flex flex-col gap-2">
													<form method="POST" action="{{ route('portfolio-items.store', $portfolio) }}" enctype="multipart/form-data" class="flex flex-col gap-2">
														@csrf
														<input type="hidden" name="type" value="{{ $type }}">
														<div class="flex items-center gap-2">
															<input type="file" name="files[]" multiple required
																   class="text-sm text-gray-500 dark:text-gray-400
																		  file:mr-4 file:py-2 file:px-4
																		  file:rounded file:border-0
																		  file:text-sm file:font-semibold
																		  file:bg-indigo-50 file:text-indigo-700
																		  hover:file:bg-indigo-100
																		  dark:file:bg-indigo-900 dark:file:text-indigo-200">
															<x-button type="submit" class="whitespace-nowrap">
																Upload
															</x-button>
														</div>
														@error('files')
															<div class="text-xs text-red-600 dark:text-red-400">
																@if(is_array($message))
																	<ul class="list-disc list-inside">
																		@foreach($message as $error)
																			<li>{{ $error }}</li>
																		@endforeach
																	</ul>
																@else
																	{{ $message }}
																@endif
															</div>
														@enderror
													</form>
													@if($isReusable)
														<p class="text-xs text-gray-500 dark:text-gray-400">
															💡 Tip: Add reusable documents to your <a href="{{ route('documents.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Document Library</a> to reuse them across sections.
														</p>
													@endif
												</div>

												{{-- Library Selection Form --}}
												<div x-show="activeTab === 'library'" x-cloak class="flex flex-col gap-2">
													<form method="POST" action="{{ route('portfolio-items.store', $portfolio) }}" class="flex flex-col gap-2">
														@csrf
														<input type="hidden" name="type" value="{{ $type }}">
														<div class="max-h-48 overflow-y-auto border border-gray-200 dark:border-gray-700 rounded-md p-2 space-y-2">
															@foreach($libraryDocuments as $doc)
																<label class="flex items-start gap-2 p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded cursor-pointer">
																	<input type="checkbox" name="library_document_ids[]" value="{{ $doc->id }}"
																		   class="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
																	<div class="flex-1">
																		<div class="text-sm font-medium text-gray-900 dark:text-gray-100">
																			{{ $doc->title }}
																		</div>
																		@if($doc->subject_code)
																			<div class="text-xs text-gray-500 dark:text-gray-400">
																				{{ $doc->subject_code }}
																			</div>
																		@endif
																		<div class="text-xs text-gray-400 dark:text-gray-500">
																			{{ number_format((($doc->metadata_json ?? [])['size'] ?? 0) / 1024, 2) }} KB
																		</div>
																	</div>
																</label>
															@endforeach
														</div>
														<x-button type="submit" class="whitespace-nowrap">
															Add Selected
														</x-button>
													</form>
												</div>
											</div>
										@else
											{{-- Upload Form (when library is not available) --}}
											<div class="flex flex-col gap-2">
												<form method="POST" action="{{ route('portfolio-items.store', $portfolio) }}" enctype="multipart/form-data" class="flex flex-col gap-2">
													@csrf
													<input type="hidden" name="type" value="{{ $type }}">
													<div class="flex items-center gap-2">
														<input type="file" name="files[]" multiple required
															   class="text-sm text-gray-500 dark:text-gray-400
																	  file:mr-4 file:py-2 file:px-4
																	  file:rounded file:border-0
																	  file:text-sm file:font-semibold
																	  file:bg-indigo-50 file:text-indigo-700
																	  hover:file:bg-indigo-100
																	  dark:file:bg-indigo-900 dark:file:text-indigo-200">
														<x-button type="submit" class="whitespace-nowrap">
															Upload
														</x-button>
													</div>
													@error('files')
														<div class="text-xs text-red-600 dark:text-red-400">
															@if(is_array($message))
																<ul class="list-disc list-inside">
																	@foreach($message as $error)
																		<li>{{ $error }}</li>
																	@endforeach
																</ul>
															@else
																{{ $message }}
															@endif
														</div>
													@enderror
												</form>
												@if($isReusable)
													<p class="text-xs text-gray-500 dark:text-gray-400">
														💡 Tip: Add reusable documents to your <a href="{{ route('documents.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Document Library</a> to reuse them across sections.
													</p>
												@endif
											</div>
										@endif
									</div>
								@endif
							</div>
						</div>
					@endforeach
				</div>
			</div>
		</div>
	</div>
</x-app-layout>


