@props(['type' => 'submit'])

<button type="{{ $type }}" {{ $attributes->class(['block rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500']) }}>
    {{ $slot }}
</button>
