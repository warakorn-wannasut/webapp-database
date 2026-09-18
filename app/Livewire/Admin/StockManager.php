<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Stock & Product Management')]
class StockManager extends Component
{
    public bool $showCreateModal = false;
    public string $name = '';
    public ?int $category_id = null;
    public string $description = '';
    public float $price = 50.00;
    public int $stock_quantity = 50;

    public function adjustStock(int $productId, int $delta)
    {
        $product = Product::findOrFail($productId);
        $newStock = max(0, $product->stock_quantity + $delta);
        $product->update(['stock_quantity' => $newStock]);
        session()->flash('success', "อัปเดตสต็อก {$product->name} เป็น {$newStock} ชิ้น");
    }

    public function createProduct()
    {
        $this->validate([
            'name' => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        Product::create([
            'name' => $this->name,
            'category_id' => $this->category_id,
            'description' => $this->description,
            'price' => $this->price,
            'stock_quantity' => $this->stock_quantity,
        ]);

        $this->reset(['name', 'category_id', 'description', 'price', 'stock_quantity', 'showCreateModal']);
        session()->flash('success', 'เพิ่มเมนูสินค้าใหม่สำเร็จเรียบร้อยแล้ว');
    }

    public function render()
    {
        $products = Product::with('category')->orderBy('category_id')->get();
        $categories = Category::all();

        return view('livewire.admin.stock-manager', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
