<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Products') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <ul>
                        <li>id: {{ $product->id }}</li>
                        <li>number: {{ $product->number }}</li>
                        <li>name: {{ $product->name }}</li>
                        <li>category: {{ implode(', ', $product->category) }}</li>
                        <li>price: {{ $product->price }}</li>
                        <li>url: {{ $product->url }}</li>
                        <li>image: {{ $product->image }}</li>
                        <li>description: {{ $product->description }}</li>
                        <li>stock: {{ $product->stock }}</li>
                        <li>status: {{ $product->status }}</li>
                        <li>custom_fields:
                            <ul class="ml-4">
                                @foreach($product->custom_fields as $key => $value)
                                    <li>{{ "$key: $value" }}</li>
                                @endforeach
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
