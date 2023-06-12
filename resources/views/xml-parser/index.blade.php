<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('XML Parser') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-8 text-xl font-semibold">
                        {{ __('Upload your XML file here') }}
                    </div>

                    <form action="{{ route('xml-parser.store') }}" method="post" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div class="">
                            <label for="upload" class="mb-2 block text-sm font-medium text-gray-900">Upload file</label>
                            <input type="file" id="upload" name="upload" class="block">
                            <p class="mt-1 text-sm text-gray-500" id="file_input_help">*.xml</p>
                            <x-input-errors :messages="$errors->get('upload')" class="mt-2" />
                        </div>

                        <x-primary-button>
                            {{ __('Save') }}
                        </x-primary-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
