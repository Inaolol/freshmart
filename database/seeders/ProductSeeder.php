<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only seed if no products exist (avoid duplicates)
        if (Product::count() > 0) {
            $this->command->info('Products already exist. Skipping seeder.');
            return;
        }
        
        $products = [
            [
                'name' => 'Organic Bananas',
                'description' => 'Fresh organic bananas, perfect for snacking or baking. Rich in potassium and vitamins.',
                'price' => 2.99,
                'category' => 'Fruits & Vegetables',
                'brand' => 'Nature\'s Best',
                'stock_quantity' => 50,
                'min_stock_level' => 10,
                'unit' => 'lbs',
                'weight' => 0.5,
                'tags' => 'organic, fresh, potassium, healthy',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Fresh Spinach',
                'description' => 'Crisp, fresh spinach leaves perfect for salads, smoothies, or cooking.',
                'price' => 3.49,
                'original_price' => 3.99,
                'category' => 'Fruits & Vegetables',
                'brand' => 'Green Valley',
                'stock_quantity' => 25,
                'min_stock_level' => 5,
                'unit' => 'packs',
                'weight' => 0.3,
                'tags' => 'leafy greens, iron, vitamins, fresh',
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Organic Whole Milk',
                'description' => 'Fresh organic whole milk from grass-fed cows. Rich, creamy taste.',
                'price' => 5.49,
                'category' => 'Dairy & Eggs',
                'brand' => 'Farm Fresh',
                'stock_quantity' => 30,
                'min_stock_level' => 6,
                'unit' => 'bottles',
                'weight' => 1.0,
                'tags' => 'organic, whole milk, grass-fed, calcium',
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Free-Range Eggs',
                'description' => 'Farm-fresh free-range eggs from happy hens. Perfect for breakfast.',
                'price' => 6.99,
                'original_price' => 7.49,
                'category' => 'Dairy & Eggs',
                'brand' => 'Happy Hens',
                'stock_quantity' => 45,
                'min_stock_level' => 10,
                'unit' => 'dozen',
                'weight' => 0.7,
                'tags' => 'free-range, farm fresh, protein, breakfast',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Grass-Fed Ground Beef',
                'description' => 'Premium grass-fed ground beef, 85% lean. Perfect for burgers and tacos.',
                'price' => 12.99,
                'category' => 'Meat & Seafood',
                'brand' => 'Premium Meats',
                'stock_quantity' => 20,
                'min_stock_level' => 5,
                'unit' => 'lbs',
                'weight' => 1.0,
                'tags' => 'grass-fed, lean, premium, protein',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Artisan Sourdough Bread',
                'description' => 'Handcrafted sourdough bread with a crispy crust and tangy flavor.',
                'price' => 5.99,
                'category' => 'Bakery & Bread',
                'brand' => 'Artisan Bakery',
                'stock_quantity' => 12,
                'min_stock_level' => 3,
                'unit' => 'loaves',
                'weight' => 0.6,
                'tags' => 'artisan, sourdough, handcrafted, traditional',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Organic Quinoa',
                'description' => 'Premium organic quinoa, a complete protein superfood.',
                'price' => 8.99,
                'category' => 'Pantry & Dry Goods',
                'brand' => 'Superfood Co',
                'stock_quantity' => 25,
                'min_stock_level' => 5,
                'unit' => 'lbs',
                'weight' => 1.0,
                'tags' => 'organic, quinoa, superfood, protein, gluten-free',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Cold-Pressed Orange Juice',
                'description' => 'Fresh cold-pressed orange juice with no added sugars.',
                'price' => 6.99,
                'category' => 'Beverages',
                'brand' => 'Pure Juice Co',
                'stock_quantity' => 22,
                'min_stock_level' => 5,
                'unit' => 'bottles',
                'weight' => 1.0,
                'tags' => 'cold-pressed, orange juice, vitamin c, fresh',
                'is_featured' => false,
                'is_active' => true,
            ],
        ];

        foreach ($products as $productData) {
            // Generate unique SKU
            $category = $productData['category'];
            $name = $productData['name'];
            $prefix = strtoupper(substr($category, 0, 3));
            $namePrefix = strtoupper(substr(str_replace(' ', '', $name), 0, 3));
            $timestamp = now()->format('ymd');
            $random = strtoupper(\Str::random(3));
            $sku = "{$prefix}{$namePrefix}{$timestamp}{$random}";

            // Ensure uniqueness
            while (Product::where('sku', $sku)->exists()) {
                $random = strtoupper(\Str::random(3));
                $sku = "{$prefix}{$namePrefix}{$timestamp}{$random}";
            }

            $productData['sku'] = $sku;

            Product::create($productData);
        }
    }
}
