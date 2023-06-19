@props(['products'])

<div class="lg:grid lg:grid-cols-6">
    @foreach ($products as $product)
        <x-product.card
            :product="$product"
            class="{{ $loop->iteration < 3 ? 'col-span-3' : 'col-span-2' }}"
        />
    @endforeach
</div>
