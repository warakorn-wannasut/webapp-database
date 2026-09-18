<div class="space-y-6">
    <!-- Notifications -->
    @if (session()->has('success'))
        <div class="p-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-zinc-800 dark:text-green-400 border border-green-200 dark:border-green-800">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-zinc-800 dark:text-red-400 border border-red-200 dark:border-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl">
        <div>
            <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">จัดการสต็อกสินค้าและเมนู (Stock Management)</h2>
            <p class="text-xs text-zinc-500 mt-1">เพิ่ม ปรับปรุงจำนวนสต็อก และแก้ไขราคาอาหาร/เครื่องดื่มในร้าน</p>
        </div>

        <button
            wire:click="$set('showCreateModal', true)"
            class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition shadow"
        >
            + เพิ่มสินค้าใหม่
        </button>
    </div>

    <!-- Products Table -->
    <div class="p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-zinc-600 dark:text-zinc-400">
                <thead class="bg-zinc-50 dark:bg-zinc-800 text-xs uppercase text-zinc-500">
                    <tr>
                        <th class="py-3 px-4">หมวดหมู่</th>
                        <th class="py-3 px-4">ชื่อสินค้า</th>
                        <th class="py-3 px-4">ราคาขาย</th>
                        <th class="py-3 px-4">สต็อกคงเหลือ</th>
                        <th class="py-3 px-4 text-right">ปรับสต็อก</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @foreach ($products as $prod)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/40 transition">
                            <td class="py-3 px-4">
                                <span class="text-xs font-semibold px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">
                                    {{ $prod->category->name }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-bold text-zinc-900 dark:text-white">
                                {{ $prod->name }}
                                @if($prod->description)
                                    <span class="block text-xs font-normal text-zinc-400 truncate max-w-xs">{{ $prod->description }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-extrabold text-zinc-900 dark:text-white">
                                ฿{{ number_format($prod->price, 2) }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="text-sm font-bold {{ $prod->stock_quantity > 10 ? 'text-emerald-600 dark:text-emerald-400' : ($prod->stock_quantity > 0 ? 'text-amber-500' : 'text-red-500') }}">
                                    {{ $prod->stock_quantity }} ชิ้น
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <button
                                        wire:click="adjustStock({{ $prod->id }}, -10)"
                                        class="px-2 py-1 text-xs font-bold rounded bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 text-zinc-700 dark:text-zinc-300"
                                        title="ลด 10 ชิ้น"
                                    >
                                        -10
                                    </button>
                                    <button
                                        wire:click="adjustStock({{ $prod->id }}, -1)"
                                        class="px-2 py-1 text-xs font-bold rounded bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 text-zinc-700 dark:text-zinc-300"
                                        title="ลด 1 ชิ้น"
                                    >
                                        -1
                                    </button>
                                    <button
                                        wire:click="adjustStock({{ $prod->id }}, 1)"
                                        class="px-2 py-1 text-xs font-bold rounded bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 text-zinc-700 dark:text-zinc-300"
                                        title="เพิ่ม 1 ชิ้น"
                                    >
                                        +1
                                    </button>
                                    <button
                                        wire:click="adjustStock({{ $prod->id }}, 10)"
                                        class="px-2 py-1 text-xs font-bold rounded bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 text-zinc-700 dark:text-zinc-300"
                                        title="เพิ่ม 10 ชิ้น"
                                    >
                                        +10
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Product Modal -->
    @if ($showCreateModal)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
                <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-3">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">เพิ่มสินค้าใหม่</h3>
                    <button wire:click="$set('showCreateModal', false)" class="text-zinc-400 hover:text-zinc-600">&times;</button>
                </div>

                <form wire:submit="createProduct" class="space-y-3 text-sm">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-600 dark:text-zinc-300 mb-1">หมวดหมู่:</label>
                        <select wire:model="category_id" class="w-full text-sm rounded-xl border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 p-2.5">
                            <option value="">-- เลือกหมวดหมู่ --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-600 dark:text-zinc-300 mb-1">ชื่อสินค้า:</label>
                        <input type="text" wire:model="name" class="w-full text-sm rounded-xl border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 p-2.5" />
                        @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-600 dark:text-zinc-300 mb-1">คำอธิบาย:</label>
                        <textarea wire:model="description" rows="2" class="w-full text-sm rounded-xl border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 p-2.5"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-600 dark:text-zinc-300 mb-1">ราคา (บาท):</label>
                            <input type="number" step="0.5" wire:model="price" class="w-full text-sm rounded-xl border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 p-2.5 font-bold" />
                            @error('price') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-600 dark:text-zinc-300 mb-1">สต็อกเริ่มต้น:</label>
                            <input type="number" wire:model="stock_quantity" class="w-full text-sm rounded-xl border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 p-2.5 font-bold" />
                            @error('stock_quantity') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-zinc-200 dark:border-zinc-800">
                        <button type="button" wire:click="$set('showCreateModal', false)" class="px-4 py-2 text-sm text-zinc-500 hover:bg-zinc-100 rounded-xl">ยกเลิก</button>
                        <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow">บันทึกสินค้า</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
