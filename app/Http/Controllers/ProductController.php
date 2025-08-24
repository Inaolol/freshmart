<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::active();

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Category filter
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Brand filter
        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }

        // Price range filter
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->byPriceRange($request->min_price, $request->max_price);
        }

        // Stock filter
        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'in_stock':
                    $query->inStock();
                    break;
                case 'low_stock':
                    $query->where('stock_quantity', '<=', \DB::raw('min_stock_level'))
                          ->where('stock_quantity', '>', 0);
                    break;
                case 'out_of_stock':
                    $query->where('stock_quantity', '<=', 0);
                    break;
            }
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');

        switch ($sortBy) {
            case 'price':
                $query->orderBy('price', $sortOrder);
                break;
            case 'created_at':
                $query->orderBy('created_at', $sortOrder);
                break;
            case 'stock_quantity':
                $query->orderBy('stock_quantity', $sortOrder);
                break;
            default:
                $query->orderBy('name', $sortOrder);
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        // Get filter options
        $categories = Product::active()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $brands = Product::active()
            ->whereNotNull('brand')
            ->select('brand')
            ->distinct()
            ->orderBy('brand')
            ->pluck('brand');

        $view = $request->get('view', 'grid');

        return view('products.index', compact(
            'products',
            'categories',
            'brands',
            'view'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = $this->getCategories();
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0|gt:price',
            'category' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'weight' => 'nullable|numeric|min:0',
            'barcode' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date|after:today',
            'tags' => 'nullable|string',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Generate SKU
        $validated['sku'] = $this->generateSku($validated['name'], $validated['category']);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($validated);

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Product created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        // Get related products from same category
        $relatedProducts = Product::active()
            ->byCategory($product->category)
            ->where('id', '!=', $product->id)
            ->inStock()
            ->limit(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = $this->getCategories();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'category' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'weight' => 'nullable|numeric|min:0',
            'barcode' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date|after:today',
            'tags' => 'nullable|string',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Delete image
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Product deleted successfully!');
    }

    /**
     * Generate unique SKU for product
     */
    private function generateSku($name, $category)
    {
        $prefix = strtoupper(substr($category, 0, 3));
        $namePrefix = strtoupper(substr(str_replace(' ', '', $name), 0, 3));
        $timestamp = now()->format('ymd');
        $random = strtoupper(Str::random(3));

        $sku = "{$prefix}{$namePrefix}{$timestamp}{$random}";

        // Ensure uniqueness
        while (Product::where('sku', $sku)->exists()) {
            $random = strtoupper(Str::random(3));
            $sku = "{$prefix}{$namePrefix}{$timestamp}{$random}";
        }

        return $sku;
    }

    /**
     * Get product categories
     */
    private function getCategories()
    {
        return [
            'Fruits & Vegetables',
            'Dairy & Eggs',
            'Meat & Seafood',
            'Bakery & Bread',
            'Pantry & Dry Goods',
            'Frozen Foods',
            'Beverages',
            'Snacks & Candy',
            'Health & Beauty',
            'Household & Cleaning',
            'Baby & Kids',
            'Organic & Natural'
        ];
    }
}
