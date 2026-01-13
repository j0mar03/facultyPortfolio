<x-app-layout>
	<x-slot name="header">
		<div class="flex justify-between items-center">
			<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
				{{ __('My Document Library') }}
			</h2>
			<a href="{{ route('dashboard') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">← Back to Dashboard</a>
		</div>
	</x-slot>

	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
			{{-- Info Banner --}}
			<div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
				<div class="flex items-start gap-3">
					<svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
						<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
					</svg>
					<div class="flex-1">
						<h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Document Library</h3>
						<p class="mt-1 text-sm text-blue-700 dark:text-blue-300">
							Upload reusable documents (quizzes, exams, TOS, rubrics) here. These can be reused across multiple portfolio sections without re-uploading.
						</p>
					</div>
				</div>
			</div>

			{{-- Upload Form --}}
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Add Document to Library</h3>
				
				<form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" class="space-y-4">
					@csrf
					
					<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						<div>
							<label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
								Document Type <span class="text-red-500">*</span>
							</label>
							<select name="type" id="type" required
									class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
								<option value="">Select type...</option>
								@foreach($reusableTypes as $type)
									<option value="{{ $type }}" {{ old('type') === $type ? 'selected' : '' }}>
										{{ $itemTypes[$type] ?? $type }}
									</option>
								@endforeach
							</select>
							@error('type')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<div>
							<label for="subject_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
								Subject Code (Optional)
							</label>
							<input type="text" name="subject_code" id="subject_code" value="{{ old('subject_code') }}"
								   placeholder="e.g., CPET 103"
								   class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
							@error('subject_code')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>
					</div>

					<div>
						<label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
							Document Title <span class="text-red-500">*</span>
						</label>
						<input type="text" name="title" id="title" value="{{ old('title') }}" required
							   placeholder="e.g., Midterm Exam Set A"
							   class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
						@error('title')
							<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
						@enderror
					</div>

					<div>
						<label for="file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
							File <span class="text-red-500">*</span>
						</label>
						<input type="file" name="file" id="file" required
							   class="w-full text-sm text-gray-500 dark:text-gray-400
									  file:mr-4 file:py-2 file:px-4
									  file:rounded file:border-0
									  file:text-sm file:font-semibold
									  file:bg-indigo-50 file:text-indigo-700
									  hover:file:bg-indigo-100
									  dark:file:bg-indigo-900 dark:file:text-indigo-200">
						<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Max file size: 10MB</p>
						@error('file')
							<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
						@enderror
					</div>

					<div class="flex gap-4">
						<x-button type="submit">
							Add to Library
						</x-button>
					</div>
				</form>

				@if(session('status'))
					<div class="mt-4 bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-800 text-green-600 dark:text-green-200 px-4 py-3 rounded">
						{{ session('status') }}
					</div>
				@endif
			</div>

			{{-- Document List --}}
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
				<div class="flex justify-between items-center mb-4">
					<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">My Library Documents</h3>
					
					{{-- Filter by Type --}}
					<form method="GET" action="{{ route('documents.index') }}" class="flex items-center gap-2">
						<select name="type" onchange="this.form.submit()"
								class="text-sm rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
							<option value="">All Types</option>
							@foreach($reusableTypes as $docType)
								<option value="{{ $docType }}" {{ $type === $docType ? 'selected' : '' }}>
									{{ $itemTypes[$docType] ?? $docType }}
								</option>
							@endforeach
						</select>
					</form>
				</div>

				@if($documents->count() > 0)
					<div class="space-y-3">
						@foreach($documents as $document)
							<div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
								<div class="flex items-start justify-between">
									<div class="flex-1">
										<div class="flex items-center gap-2 mb-2">
											<span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
												{{ $itemTypes[$document->type] ?? $document->type }}
											</span>
											@if($document->subject_code)
												<span class="text-xs text-gray-500 dark:text-gray-400">
													{{ $document->subject_code }}
												</span>
											@endif
										</div>
										<h4 class="text-md font-medium text-gray-900 dark:text-gray-100">
											{{ $document->title }}
										</h4>
										<div class="mt-2 flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
											<span>
												{{ number_format(($document->metadata_json['size'] ?? 0) / 1024, 2) }} KB
											</span>
											<span>
												Used in {{ $document->portfolioItems->count() }} portfolio(s)
											</span>
											<span>
												Added {{ $document->created_at->diffForHumans() }}
											</span>
										</div>
									</div>
									<div class="flex items-center gap-2">
										<a href="{{ route('documents.download', $document) }}" 
										   class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm">
											Download
										</a>
										<form method="POST" action="{{ route('documents.destroy', $document) }}"
											  onsubmit="return confirm('Are you sure you want to delete this document? It will be removed from all portfolios using it.');">
											@csrf
											@method('DELETE')
											<button type="submit" class="text-red-600 dark:text-red-400 hover:underline text-sm">
												Delete
											</button>
										</form>
									</div>
								</div>
							</div>
						@endforeach
					</div>

					<div class="mt-4">
						{{ $documents->links() }}
					</div>
				@else
					<div class="text-center py-8">
						<svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
						</svg>
						<p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No documents in your library yet.</p>
						<p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Upload reusable documents above to get started.</p>
					</div>
				@endif
			</div>
		</div>
	</div>
</x-app-layout>
