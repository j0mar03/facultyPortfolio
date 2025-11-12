<x-app-layout>
	<x-slot name="header">
		<div class="flex justify-between items-center">
			<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
				{{ __('Review Portfolio') }}
			</h2>
			<a href="{{ route('reviews.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">← Back to Queue</a>
		</div>
	</x-slot>

	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
			{{-- Portfolio Info --}}
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
				<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
					<div>
						<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Portfolio Information</h3>
						<dl class="space-y-2">
							<div>
								<dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Faculty</dt>
								<dd class="text-sm text-gray-900 dark:text-gray-100">{{ $portfolio->user->name }}</dd>
							</div>
							<div>
								<dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
								<dd class="text-sm text-gray-900 dark:text-gray-100">{{ $portfolio->user->email }}</dd>
							</div>
							<div>
								<dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Course</dt>
								<dd class="text-sm text-gray-900 dark:text-gray-100">{{ $portfolio->classOffering->subject->course->code }} - {{ $portfolio->classOffering->subject->course->name }}</dd>
							</div>
							<div>
								<dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Subject</dt>
								<dd class="text-sm text-gray-900 dark:text-gray-100">{{ $portfolio->classOffering->subject->code }} - {{ $portfolio->classOffering->subject->title }}</dd>
							</div>
						</dl>
					</div>
					<div>
						<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Submission Details</h3>
						<dl class="space-y-2">
							<div>
								<dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Academic Year</dt>
								<dd class="text-sm text-gray-900 dark:text-gray-100">{{ $portfolio->classOffering->academic_year }}</dd>
							</div>
							<div>
								<dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Term</dt>
								<dd class="text-sm text-gray-900 dark:text-gray-100">Term {{ $portfolio->classOffering->term }}</dd>
							</div>
							<div>
								<dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Section</dt>
								<dd class="text-sm text-gray-900 dark:text-gray-100">{{ $portfolio->classOffering->section }}</dd>
							</div>
							<div>
								<dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Submitted</dt>
								<dd class="text-sm text-gray-900 dark:text-gray-100">{{ $portfolio->submitted_at->format('M d, Y h:i A') }}</dd>
							</div>
							<div>
								<dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
								<dd>
									<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium
										{{ $portfolio->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' :
										   ($portfolio->status === 'submitted' ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' :
										   'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100') }}">
										{{ ucfirst($portfolio->status) }}
									</span>
								</dd>
							</div>
						</dl>
					</div>
				</div>
			</div>

			{{-- Portfolio Items --}}
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Uploaded Documents</h3>

				<div class="space-y-3">
					@php
						$itemTypes = config('portfolio.item_types');
						$requiredTypes = config('portfolio.required_items');
						$uploadedItems = $portfolio->items->groupBy('type');
					@endphp

					@foreach($itemTypes as $type => $label)
						@php
							$isRequired = in_array($type, $requiredTypes);
							$items = $uploadedItems->get($type, collect());
							$hasUpload = $items->isNotEmpty();
						@endphp

						<div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
							<div class="flex items-center gap-2 mb-2">
								@if($hasUpload)
									<svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
										<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
									</svg>
								@else
									<svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
										<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd"/>
									</svg>
								@endif
								<h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">
									{{ $label }}
									@if($isRequired)
										<span class="text-red-500">*</span>
									@endif
								</h4>
							</div>

							@if($hasUpload)
								<div class="ml-7 space-y-1">
									@foreach($items as $item)
										<div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 rounded px-3 py-2">
											<div class="flex items-center gap-2">
												<svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
													<path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
												</svg>
												<span class="text-sm text-gray-700 dark:text-gray-300">{{ $item->title }}</span>
												<span class="text-xs text-gray-500 dark:text-gray-400">
													({{ number_format(($item->metadata_json['size'] ?? 0) / 1024, 2) }} KB)
												</span>
											</div>
											<a href="{{ route('portfolio-items.download', [$portfolio, $item]) }}"
											   class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm">
												Download
											</a>
										</div>
									@endforeach
								</div>
							@else
								<p class="ml-7 text-sm text-gray-500 dark:text-gray-400">No file uploaded</p>
							@endif
						</div>
					@endforeach
				</div>
			</div>

			{{-- Review History --}}
			@if($portfolio->reviews->isNotEmpty())
				<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
					<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Review History</h3>
					<div class="space-y-4">
						@foreach($portfolio->reviews as $review)
							<div class="border-l-4 border-gray-300 dark:border-gray-600 pl-4">
								<div class="flex items-center gap-2 mb-1">
									<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
										{{ $review->decision === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' :
										   'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
										{{ ucfirst($review->decision) }}
									</span>
									<span class="text-sm text-gray-500 dark:text-gray-400">by {{ $review->reviewer->name }}</span>
									<span class="text-sm text-gray-500 dark:text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
								</div>
								@if($review->remarks)
									<p class="text-sm text-gray-700 dark:text-gray-300">{{ $review->remarks }}</p>
								@endif
							</div>
						@endforeach
					</div>
				</div>
			@endif

			{{-- Decision Form --}}
			@if($portfolio->status === 'submitted')
				<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
					<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Make Decision</h3>

					<form method="POST" action="{{ route('reviews.decision', $portfolio) }}">
						@csrf
						<div class="space-y-4">
							<div>
								<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
									Decision <span class="text-red-500">*</span>
								</label>
								<div class="space-y-2">
									<label class="inline-flex items-center">
										<input type="radio" name="decision" value="approved" required
											   class="rounded-full border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
										<span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Approve</span>
									</label>
									<label class="inline-flex items-center ml-6">
										<input type="radio" name="decision" value="rejected" required
											   class="rounded-full border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
										<span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Reject</span>
									</label>
									<label class="inline-flex items-center ml-6">
										<input type="radio" name="decision" value="changes_requested" required
											   class="rounded-full border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
										<span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Request Changes</span>
									</label>
								</div>
								@error('decision')
									<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
								@enderror
							</div>

							<div>
								<label for="remarks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
									Remarks/Comments
								</label>
								<textarea id="remarks" name="remarks" rows="4"
										  class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
										  placeholder="Provide feedback or reasons for your decision..."></textarea>
								@error('remarks')
									<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
								@enderror
							</div>

							<div class="flex gap-4">
								<x-button type="submit">
									Submit Decision
								</x-button>
								<a href="{{ route('reviews.index') }}"
								   class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-300 active:bg-gray-500 disabled:opacity-25 transition">
									Cancel
								</a>
							</div>
						</div>
					</form>
				</div>
			@endif
		</div>
	</div>
</x-app-layout>
