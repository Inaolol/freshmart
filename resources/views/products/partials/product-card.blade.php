<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-all duration-300 group">
    <!-- Product Image -->
    <div class="relative overflow-hidden">
        <a href="{{ route('products.show', $product) }}">
            <img src="{{ $product->image_url }}" 
                 alt="{{ $product->name }}" 
                 class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
        </a>
        
        <!-- Badges -->
        <div class="absolute top-3 left-3 flex flex-col space-y-1">
            @if($product->is_featured)
                <span class="bg-orange-500 text-white text-xs px-2 py-1 rounded-full font-medium">
                    <i class="fas fa-star mr-1"></i>Featured
                </span>
            @endif
            
            @if($product->is_on_sale)
                <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full font-medium">
                    -{{ $product->discount_percentage }}%
                </span>
            @endif
        </div>

        <!-- Stock Status -->
        <div class="absolute top-3 right-3">
            @switch($product->stock_status)
                @case('out_of_stock')
                    <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full font-medium">
                        Out of Stock
                    </span>
                    @break
                @case('low_stock')
                    <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full font-medium">
                        Low Stock
                    </span>
                    @break
                @default
                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full font-medium">
                        In Stock
                    </span>
            @endswitch
        </div>

        <!-- Quick Actions -->
        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
            <div class="flex space-x-2">
                <a href="{{ route('products.show', $product) }}" 
                   class="bg-white text-gray-900 p-2 rounded-full shadow-md hover:bg-green-50 hover:text-green-600 transition-colors">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="{{ route('products.edit', $product) }}" 
                   class="bg-white text-gray-900 p-2 rounded-full shadow-md hover:bg-blue-50 hover:text-blue-600 transition-colors">
                    <i class="fas fa-edit"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Product Info -->
    <div class="p-4">
        <!-- Category -->
        <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">
            {{ $product->category }}
        </div>

        <!-- Product Name -->
        <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">
            <a href="{{ route('products.show', $product) }}" class="hover:text-green-600 transition-colors">
                {{ $product->name }}
            </a>
        </h3>

        <!-- Brand -->
        @if($product->brand)
            <p class="text-sm text-gray-600 mb-2">{{ $product->brand }}</p>
        @endif

        <!-- Price -->
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center space-x-2">
                <span class="text-lg font-bold text-gray-900">{{ $product->formatted_price }}</span>
                @if($product->formatted_original_price)
                    <span class="text-sm text-gray-500 line-through">{{ $product->formatted_original_price }}</span>
                @endif
            </div>
            <span class="text-xs text-gray-500">per {{ $product->unit }}</span>
        </div>

        <!-- Stock Quantity -->
        <div class="flex items-center justify-between text-sm text-gray-600 mb-3">
            <span>Stock: {{ $product->stock_quantity }} {{ $product->unit }}</span>
            @if($product->weight)
                <span>{{ $product->weight }}kg</span>
            @endif
        </div>

        <!-- Actions -->
        <div class="flex space-x-2">
            <a href="{{ route('products.show', $product) }}" 
               class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center py-2 px-3 rounded-lg text-sm font-medium transition-colors">
                View Details
            </a>
            <a href="{{ route('products.edit', $product) }}" 
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 p-2 rounded-lg transition-colors">
                <i class="fas fa-edit"></i>
            </a>
        </div>
    </div>
</div>
