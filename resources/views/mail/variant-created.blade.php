<x-mail::message>
# New variant created

**Product:** {{ $variant->product->name }}
**SKU:** {{ $variant->sku }}
**Price:** {{ $variant->priceMoney()->format() }}
**Stock:** {{ $variant->stock }}

{{ config('app.name') }}
</x-mail::message>
