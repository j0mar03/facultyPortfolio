<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('My Portfolios') }}
		</h2>
	</x-slot>
	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
				<ul class="divide-y divide-gray-200 dark:divide-gray-700">
					@forelse($portfolios as $p)
						@if($p->classOffering && $p->classOffering->subject && $p->classOffering->subject->course)
							<li class="py-3 flex items-center justify-between">
								<div>
									<div class="font-medium text-gray-900 dark:text-gray-100">
										{{ $p->classOffering->subject->course->code }} - {{ $p->classOffering->subject->code }}
									</div>
									<div class="text-sm text-gray-500 dark:text-gray-400">
										{{ $p->classOffering->subject->title }} • {{ $p->classOffering->academic_year }} / T{{ $p->classOffering->term }} • Sec {{ $p->classOffering->section }}
									</div>
								</div>
								<div>
									<a href="{{ route('portfolios.show', $p) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Open</a>
								</div>
							</li>
						@endif
					@empty
						<li class="py-6 text-center text-gray-500 dark:text-gray-400">No portfolios yet.</li>
					@endforelse
				</ul>
			</div>
		</div>
	</div>
</x-app-layout>


