<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Import Class Offerings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">

                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('errors') && count(session('errors')) > 0)
                    <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                        <p class="font-bold">Errors during import:</p>
                        <ul class="list-disc list-inside mt-2">
                            @foreach(session('errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3 dark:text-gray-200">CSV Format Instructions</h3>
                    <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded">
                        <p class="text-sm dark:text-gray-300 mb-2">Your CSV file should have the following columns (with header row):</p>
                        <code class="block bg-gray-800 text-green-400 p-3 rounded text-xs overflow-x-auto">
course_code,subject_code,subject_title,year_level,term,academic_year,section,faculty_email
DICT,DICT101,Introduction to ICT,1,1,2024-2025,A,faculty1@example.com
DCET,DCET201,Digital Circuits,2,1,2024-2025,B,faculty1@example.com
                        </code>
                        <ul class="mt-3 text-sm dark:text-gray-300 space-y-1">
                            <li><strong>course_code:</strong> Must match existing course (e.g., DICT, DCET, DEET, etc.)</li>
                            <li><strong>subject_code:</strong> Unique identifier for the subject</li>
                            <li><strong>subject_title:</strong> Full name of the subject</li>
                            <li><strong>year_level:</strong> 1, 2, or 3</li>
                            <li><strong>term:</strong> 1 or 2</li>
                            <li><strong>academic_year:</strong> Format: YYYY-YYYY (e.g., 2024-2025)</li>
                            <li><strong>section:</strong> Class section (e.g., A, B, C)</li>
                            <li><strong>faculty_email:</strong> Email of faculty member (must exist in users table)</li>
                        </ul>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.import.class-offerings.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="csv_file">
                            Upload CSV File
                        </label>
                        <input
                            type="file"
                            name="csv_file"
                            id="csv_file"
                            accept=".csv,.txt"
                            required
                            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                        >
                    </div>

                    <div class="flex items-center justify-between">
                        <button
                            type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                        >
                            Import CSV
                        </button>
                        <a href="{{ route('dashboard') }}" class="text-blue-500 hover:text-blue-700">
                            Back to Dashboard
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
