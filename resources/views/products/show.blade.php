@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            <li><a href="{{ route('products.index') }}" class="hover:text-green-600">Products</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('products.index', ['category' => $product->category]) }}" class="hover:text-green-600">{{ $product->category }}</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900">{{ $product->name }}</li>
        </ol>
    </nav>

    <!-- Product Details -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-8">
            <!-- Product Image -->
            <div class="space-y-4">
                <div class="aspect-square rounded-lg overflow-hidden bg-gray-100">
                    <img src="{{ $product->image_url }}" 
                         alt="{{ $product->name }}" 
                         class="w-full h-full object-cover">
                </div>
                
                <!-- Image Gallery Placeholder -->
                @if($product->gallery && count($product->gallery) > 0)
                    <div class="grid grid-cols-4 gap-2">
                        @foreach($product->gallery as $image)
                            <div class="aspect-square rounded-lg overflow-hidden bg-gray-100">
                                <img src="{{ asset('storage/' . $image) }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-full object-cover cursor-pointer hover:opacity-80 transition-opacity">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Product Info -->
            <div class="space-y-6">
                <!-- Header -->
                <div>
                    <!-- Badges -->
                    <div class="flex items-center space-x-2 mb-3">
                        <span class="bg-gray-100 text-gray-800 text-xs px-3 py-1 rounded-full font-medium uppercase tracking-wide">
                            {{ $product->category }}
                        </span>
                        @if($product->is_featured)
                            <span class="bg-orange-100 text-orange-800 text-xs px-3 py-1 rounded-full font-medium">
                                <i class="fas fa-star mr-1"></i>Featured
                            </span>
                        @endif
                        @if($product->is_on_sale)
                            <span class="bg-red-100 text-red-800 text-xs px-3 py-1 rounded-full font-medium">
                                -{{ $product->discount_percentage }}% OFF
                            </span>
                        @endif
                    </div>

                    <!-- Product Name -->
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>
                    
                    <!-- Brand -->
                    @if($product->brand)
                        <p class="text-lg text-gray-600">by {{ $product->brand }}</p>
                    @endif
                </div>

                <!-- Price -->
                <div class="border-t border-b border-gray-200 py-6">
                    <div class="flex items-center space-x-4">
                        <span class="text-4xl font-bold text-gray-900">{{ $product->formatted_price }}</span>
                        @if($product->formatted_original_price)
                            <span class="text-xl text-gray-500 line-through">{{ $product->formatted_original_price }}</span>
                        @endif
                        <span class="text-sm text-gray-500">per {{ $product->unit }}</span>
                    </div>
                </div>

                <!-- Stock Status -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        @switch($product->stock_status)
                            @case('out_of_stock')
                                <i class="fas fa-times-circle text-red-500"></i>
                                <span class="text-red-700 font-medium">Out of Stock</span>
                                @break
                            @case('low_stock')
                                <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                                <span class="text-yellow-700 font-medium">Low Stock ({{ $product->stock_quantity }} left)</span>
                                @break
                            @default
                                <i class="fas fa-check-circle text-green-500"></i>
                                <span class="text-green-700 font-medium">In Stock ({{ $product->stock_quantity }} available)</span>
                        @endswitch
                    </div>
                    <span class="text-sm text-gray-500">SKU: {{ $product->sku }}</span>
                </div>

                <!-- Product Details -->
                <div class="grid grid-cols-2 gap-4 text-sm">
                    @if($product->weight)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Weight:</span>
                            <span class="font-medium">{{ $product->weight }}kg</span>
                        </div>
                    @endif
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">Unit:</span>
                        <span class="font-medium">{{ $product->unit }}</span>
                    </div>

                    @if($product->barcode)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Barcode:</span>
                            <span class="font-medium">{{ $product->barcode }}</span>
                        </div>
                    @endif

                    @if($product->expiry_date)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Expiry Date:</span>
                            <span class="font-medium">{{ $product->expiry_date->format('M d, Y') }}</span>
                        </div>
                    @endif
                </div>

                <!-- Actions -->
                <div class="flex space-x-3">
                    <a href="{{ route('products.edit', $product) }}" 
                       class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-3 px-6 rounded-lg font-medium transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Product
                    </a>
                    <button onclick="confirmDelete('{{ route('products.destroy', $product) }}')" 
                            class="bg-red-600 hover:bg-red-700 text-white py-3 px-6 rounded-lg font-medium transition-colors">
                        <i class="fas fa-trash mr-2"></i>
                        Delete
                    </button>
                </div>
            </div>
        </div>

        <!-- Description and Additional Info -->
        <div class="border-t border-gray-200 p-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Description -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Description</h3>
                    <div class="prose prose-sm text-gray-600">
                        {!! nl2br(e($product->description)) !!}
                    </div>

                    @if($product->tags)
                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Tags</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach(explode(',', $product->tags) as $tag)
                                    <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">
                                        {{ trim($tag) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Nutritional Info -->
                @if($product->nutritional_info)
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Nutritional Information</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                @foreach($product->nutritional_info as $key => $value)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                        <span class="font-medium">{{ $value }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Related Products</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $relatedProduct)
                    @include('products.partials.product-card', ['product' => $relatedProduct])
                @endforeach
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
function confirmDelete(url) {
    if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        
        const method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'DELETE';
        
        form.appendChild(csrf);
        form.appendChild(method);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection
