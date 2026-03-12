<x-app-layout>
	<x-slot name="header">
		<div class="flex justify-between items-center">
			<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
				{{ __('My Compliance Tracker') }}
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
			{{-- Personal Compliance Summary --}}
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-indigo-500">
				<div class="flex items-center justify-between">
					<div>
						<h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">My Overall Audit Readiness</h3>
						<p class="text-sm text-gray-500 mt-1">Based on professional subjects requiring documentation.</p>
					</div>
					<div class="text-right">
						<div class="text-3xl font-black text-indigo-600 dark:text-indigo-400">{{ number_format($overallCompliance, 1) }}%</div>
						<div class="text-xs text-gray-400 uppercase tracking-widest font-bold">Total Compliance</div>
					</div>
				</div>
				<div class="mt-4 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4 overflow-hidden">
					<div class="bg-indigo-600 h-4 rounded-full transition-all duration-1000 shadow-inner" style="width: {{ $overallCompliance }}%"></div>
				</div>
			</div>

			{{-- Compliance Legend --}}
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-4">
				<div class="flex items-center justify-center gap-8 text-xs flex-wrap font-bold uppercase tracking-tighter">
					<div class="flex items-center gap-2">
						<div class="w-3 h-3 bg-green-500 rounded-full shadow-sm"></div>
						<span class="dark:text-gray-300">Approved</span>
					</div>
					<div class="flex items-center gap-2">
						<div class="w-3 h-3 bg-blue-500 rounded-full shadow-sm"></div>
						<span class="dark:text-gray-300">Submitted</span>
					</div>
					<div class="flex items-center gap-2">
						<div class="w-3 h-3 bg-yellow-500 rounded-full shadow-sm"></div>
						<span class="dark:text-gray-300">Draft</span>
					</div>
					<div class="flex items-center gap-2">
						<div class="w-3 h-3 bg-red-500 rounded-full shadow-sm animate-pulse"></div>
						<span class="dark:text-gray-300">Missing</span>
					</div>
				</div>
			</div>

			{{-- Personal Compliance Matrix --}}
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
				<div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/20">
					<h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Professional Subjects Matrix</h3>
					<p class="text-xs text-gray-500 mt-1">Excludes non-audit subjects (GEED, NSTP, etc.).</p>
				</div>
				<div class="overflow-x-auto">
					<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
						<thead class="bg-gray-50 dark:bg-gray-700">
							<tr>
								<th class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Subject & Section</th>
								<th class="px-4 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">IM</th>
								<th class="px-4 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Load</th>
								<th class="px-4 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Syllabus</th>
								<th class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Docs Matrix</th>
								<th class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Progress</th>
								<th class="px-4 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
							@forelse($requiredOfferings as $offering)
								@php
									$portfolio = $offering->portfolio;
									$uploadedTypes = $portfolio ? $portfolio->items->pluck('type')->toArray() : [];
									if ($offering->instructional_material) $uploadedTypes[] = 'sample_ims';
									if ($offering->syllabus) $uploadedTypes[] = 'syllabus';
									if ($offering->assignment_document) $uploadedTypes[] = 'faculty_assignment';
									$stats = $portfolio ? $portfolio->completionStats() : ['completed' => 0, 'total' => count($requiredItems), 'percentage' => 0];
								@endphp
								<tr class="hover:bg-gray-50 dark:hover:bg-gray-900/40">
									<td class="px-4 py-4 whitespace-nowrap">
										<div class="text-sm font-black text-gray-900 dark:text-gray-100">{{ $offering->subject->code }}</div>
										<div class="text-[10px] text-gray-500 font-bold">Section {{ $offering->section }}</div>
									</td>
									<td class="px-4 py-4 text-center">
										@if($offering->instructional_material)
											<span class="text-green-500 text-lg">●</span>
										@else
											<span class="text-red-500 text-lg animate-pulse">○</span>
										@endif
									</td>
									<td class="px-4 py-4 text-center">
										@if($offering->assignment_document)
											<span class="text-green-500 text-lg">●</span>
										@else
											<span class="text-red-500 text-lg animate-pulse">○</span>
										@endif
									</td>
									<td class="px-4 py-4 text-center">
										@if($offering->syllabus)
											<span class="text-green-500 text-lg">●</span>
										@else
											<span class="text-red-500 text-lg animate-pulse">○</span>
										@endif
									</td>
									<td class="px-4 py-4 whitespace-nowrap">
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
										</div>
									</td>
									<td class="px-4 py-4 whitespace-nowrap">
										<div class="text-[10px] font-black {{ $stats['percentage'] >= 100 ? 'text-green-600' : 'text-indigo-500' }}">
											{{ number_format($stats['percentage'], 0) }}% COMPLETE
										</div>
										<div class="w-20 bg-gray-200 dark:bg-gray-700 rounded-full h-1 mt-1">
											<div class="bg-indigo-600 h-1 rounded-full" style="width: {{ $stats['percentage'] }}%"></div>
										</div>
									</td>
									<td class="px-4 py-4 whitespace-nowrap text-right">
										@if($portfolio)
											<a href="{{ route('portfolios.show', $portfolio) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline font-bold text-xs uppercase tracking-widest">
												Open Portfolio
											</a>
										@else
											<span class="text-gray-400 text-xs font-bold uppercase tracking-widest italic">Not Created</span>
										@endif
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="7" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">No professional subjects requiring audit documentation assigned to you.</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>
			</div>
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
