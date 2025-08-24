<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-300">
    <div class="flex items-center space-x-6">
        <!-- Product Image -->
        <div class="flex-shrink-0">
            <a href="{{ route('products.show', $product) }}">
                <img src="{{ $product->image_url }}" 
                     alt="{{ $product->name }}" 
                     class="w-20 h-20 object-cover rounded-lg">
            </a>
        </div>

        <!-- Product Info -->
        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <!-- Category and Badges -->
                    <div class="flex items-center space-x-2 mb-1">
                        <span class="text-xs text-gray-500 uppercase tracking-wide">{{ $product->category }}</span>
                        @if($product->is_featured)
                            <span class="bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-full font-medium">
                                <i class="fas fa-star mr-1"></i>Featured
                            </span>
                        @endif
                        @if($product->is_on_sale)
                            <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full font-medium">
                                -{{ $product->discount_percentage }}% OFF
                            </span>
                        @endif
                    </div>

                    <!-- Product Name -->
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">
                        <a href="{{ route('products.show', $product) }}" class="hover:text-green-600 transition-colors">
                            {{ $product->name }}
                        </a>
                    </h3>

                    <!-- Brand -->
                    @if($product->brand)
                        <p class="text-sm text-gray-600 mb-2">{{ $product->brand }}</p>
                    @endif

                    <!-- Description -->
                    <p class="text-sm text-gray-600 line-clamp-2 mb-3">
                        {{ Str::limit($product->description, 150) }}
                    </p>

                    <!-- Product Details -->
                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                        <span>SKU: {{ $product->sku }}</span>
                        <span>Stock: {{ $product->stock_quantity }} {{ $product->unit }}</span>
                        @if($product->weight)
                            <span>Weight: {{ $product->weight }}kg</span>
                        @endif
                        @if($product->expiry_date)
                            <span>Expires: {{ $product->expiry_date->format('M d, Y') }}</span>
                        @endif
                    </div>
                </div>

                <!-- Price and Actions -->
                <div class="flex flex-col items-end space-y-3 ml-4">
                    <!-- Price -->
                    <div class="text-right">
                        <div class="flex items-center space-x-2">
                            <span class="text-xl font-bold text-gray-900">{{ $product->formatted_price }}</span>
                            @if($product->formatted_original_price)
                                <span class="text-sm text-gray-500 line-through">{{ $product->formatted_original_price }}</span>
                            @endif
                        </div>
                        <span class="text-xs text-gray-500">per {{ $product->unit }}</span>
                    </div>

                    <!-- Stock Status -->
                    <div>
                        @switch($product->stock_status)
                            @case('out_of_stock')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    Out of Stock
                                </span>
                                @break
                            @case('low_stock')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Low Stock
                                </span>
                                @break
                            @default
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    In Stock
                                </span>
                        @endswitch
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('products.show', $product) }}" 
                           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            <i class="fas fa-eye mr-1"></i>
                            View
                        </a>
                        <a href="{{ route('products.edit', $product) }}" 
                           class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            <i class="fas fa-edit mr-1"></i>
                            Edit
                        </a>
                        <button onclick="confirmDelete('{{ route('products.destroy', $product) }}')" 
                                class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            <i class="fas fa-trash mr-1"></i>
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
