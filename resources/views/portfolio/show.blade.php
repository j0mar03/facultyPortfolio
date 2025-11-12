<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Portfolio') }} — {{ $portfolio->classOffering->subject->code }} ({{ $portfolio->classOffering->academic_year }} / T{{ $portfolio->classOffering->term }}, Sec {{ $portfolio->classOffering->section }})
		</h2>
	</x-slot>
	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
				<div class="mb-4">
					<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
						{{ $portfolio->status === 'approved' ? 'bg-green-100 text-green-800' : ($portfolio->status === 'submitted' ? 'bg-blue-100 text-blue-800' : ($portfolio->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
						Status: {{ ucfirst($portfolio->status) }}
					</span>
				</div>
				<p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Checklist and uploads will appear here in the next step.</p>
				<a href="{{ route('portfolios.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Back to list</a>
			</div>
		</div>
	</div>
</x-app-layout>


