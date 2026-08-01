@include('partials.header')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">{{ __('quickad.listing') }} ({{ $total ?? 0 }})</h1>
    @isset($items)
        @if($items->count())
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($items as $post)
                    <a href="{{ route('ad.detail', ['id' => $post->id, 'slug' => $post->slug]) }}"
                       class="border rounded p-4 hover:shadow">
                        <div class="font-semibold">{{ $post->product_name }}</div>
                        <div class="text-sm text-gray-500">{{ $post->city }} • ${{ $post->price }}</div>
                    </a>
                @endforeach
            </div>
            <div class="mt-6">{{ $items->links() }}</div>
        @else
            <p class="text-gray-500">No ads found.</p>
        @endif
    @endisset
</div>
@include('partials.footer')
