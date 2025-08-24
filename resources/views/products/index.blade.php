@extends('layouts.app')

@section('title', 'All Products')

@section('content')
<div x-data="productIndex()" class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Products</h1>
            <p class="mt-2 text-gray-600">Fresh groceries for your family</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-2">
            <span class="text-sm text-gray-500">{{ $products->total() }} products</span>
        </div>
    </div>

    <!-- Filters and Controls -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('products.index') }}" x-ref="filterForm">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
                <!-- Category Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Brand Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Brand</label>
                    <select name="brand" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">All Brands</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>
                                {{ $brand }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Price Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Min Price</label>
                    <input type="number" name="min_price" value="{{ request('min_price') }}" 
                           placeholder="$0" step="0.01" min="0"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Price</label>
                    <input type="number" name="max_price" value="{{ request('max_price') }}" 
                           placeholder="$1000" step="0.01" min="0"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>

                <!-- Stock Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock Status</label>
                    <select name="stock_status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">All Stock</option>
                        <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                </div>
            </div>

            <!-- Filter Actions -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-filter mr-1"></i>
                        Apply Filters
                    </button>
                    <a href="{{ route('products.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-times mr-1"></i>
                        Clear
                    </a>
                </div>

                <!-- Sort and View Options -->
                <div class="flex items-center space-x-4">
                    <!-- Sort Options -->
                    <div class="flex items-center space-x-2">
                        <label class="text-sm text-gray-700">Sort by:</label>
                        <select name="sort_by" class="border border-gray-300 rounded-lg px-3 py-1 text-sm focus:ring-2 focus:ring-green-500">
                            <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                            <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Price</option>
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date Added</option>
                            <option value="stock_quantity" {{ request('sort_by') == 'stock_quantity' ? 'selected' : '' }}>Stock</option>
                        </select>
                        <select name="sort_order" class="border border-gray-300 rounded-lg px-3 py-1 text-sm focus:ring-2 focus:ring-green-500">
                            <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                            <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                        </select>
                    </div>

                    <!-- View Toggle -->
                    <div class="flex items-center bg-gray-100 rounded-lg p-1">
                        <a href="{{ request()->fullUrlWithQuery(['view' => 'grid']) }}" 
                           class="px-3 py-1 rounded-md text-sm {{ $view === 'grid' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                            <i class="fas fa-th-large"></i>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}" 
                           class="px-3 py-1 rounded-md text-sm {{ $view === 'list' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                            <i class="fas fa-list"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Products Grid/List -->
    @if($products->count() > 0)
        @if($view === 'grid')
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($products as $product)
                    @include('products.partials.product-card', ['product' => $product])
                @endforeach
            </div>
        @else
            <div class="space-y-4">
                @foreach($products as $product)
                    @include('products.partials.product-list-item', ['product' => $product])
                @endforeach
            </div>
        @endif

        <!-- Pagination -->
        <div class="mt-8">
            {{ $products->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <div class="w-24 h-24 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-search text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No products found</h3>
            <p class="text-gray-600 mb-6">Try adjusting your filters or search terms</p>
            <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                View All Products
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
function productIndex() {
    return {
        init() {
            // Auto-submit form when filters change
            const form = this.$refs.filterForm;
            const selects = form.querySelectorAll('select');
            const inputs = form.querySelectorAll('input[type="number"]');
            
            [...selects, ...inputs].forEach(element => {
                element.addEventListener('change', () => {
                    form.submit();
                });
            });
        }
    }
}
</script>
@endpush
@endsection
