<x-layouts::app :title="__('Stock Manager')">
    <div class="space-y-6">
        <!-- Notifications -->
        @if (session()->has('success'))
            <div class="p-4 rounded-xl bg-emerald-950/60 border border-emerald-500/40 text-emerald-300 text-sm flex items-center gap-2">
                <span class="text-base">✅</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="p-4 rounded-xl bg-red-950/60 border border-red-500/40 text-red-300 text-sm flex items-center gap-2">
                <span class="text-base">⚠️</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="salai-card p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="salai-step-header">
                    <span class="salai-step-bar"></span>
                    <h2 class="text-2xl font-black text-white font-sans">จัดการสต็อกสินค้าและเมนู (Stock Management)</h2>
                </div>
                <p class="text-xs text-zinc-400 pl-4 mt-1">เพิ่ม ปรับปรุงจำนวนสต็อก และแก้ไขราคาอาหาร/เครื่องดื่มในร้าน</p>
            </div>

            <button
                type="button"
                onclick="openCreateProductModal()"
                class="salai-btn-primary text-xs py-2 px-4 shadow"
            >
                + เพิ่มสินค้าใหม่
            </button>
        </div>

        <!-- Products Table -->
        <div class="salai-card p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-zinc-300">
                    <thead class="bg-[#141824] text-xs uppercase text-zinc-400 border-b border-[#1e2430]">
                        <tr>
                            <th class="py-3 px-4">หมวดหมู่</th>
                            <th class="py-3 px-4">ชื่อสินค้า</th>
                            <th class="py-3 px-4">ราคาขาย</th>
                            <th class="py-3 px-4">สต็อกคงเหลือ</th>
                            <th class="py-3 px-4 text-right">ปรับสต็อก</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#1e2430]">
                        @foreach ($products as $prod)
                            <tr class="hover:bg-[#141824]/60 transition">
                                <td class="py-3 px-4">
                                    <span class="salai-badge-red text-[10px]">
                                        {{ $prod->category ? $prod->category->name : '-' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-bold text-white">
                                    {{ $prod->name }}
                                    @if($prod->description)
                                        <span class="block text-xs font-normal text-zinc-400 truncate max-w-xs">{{ $prod->description }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 font-black text-white font-mono">
                                    ฿{{ number_format($prod->price, 2) }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="text-sm font-bold font-mono {{ $prod->stock_quantity > 10 ? 'text-emerald-400' : ($prod->stock_quantity > 0 ? 'text-amber-400' : 'text-red-400') }}">
                                        {{ $prod->stock_quantity }} ชิ้น
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        @foreach ([-10, -1, 1, 10] as $d)
                                            <form method="POST" action="{{ route('admin.adjust-stock') }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $prod->id }}">
                                                <input type="hidden" name="delta" value="{{ $d }}">
                                                <button
                                                    type="submit"
                                                    class="px-2 py-1 text-xs font-bold rounded bg-[#141824] border border-[#232938] hover:border-red-500/50 text-zinc-300 font-mono transition"
                                                    title="{{ $d > 0 ? 'เพิ่ม ' . $d . ' ชิ้น' : 'ลด ' . abs($d) . ' ชิ้น' }}"
                                                >
                                                    {{ $d > 0 ? '+' . $d : $d }}
                                                </button>
                                            </form>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create Product Modal -->
        <div id="createProductModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
            <div class="salai-card-glow max-w-md w-full p-6 space-y-4 shadow-2xl rounded-2xl">
                <div class="flex items-center justify-between border-b border-[#232938] pb-3">
                    <h3 class="text-lg font-bold text-white font-sans">เพิ่มสินค้าใหม่</h3>
                    <button type="button" onclick="closeCreateProductModal()" class="text-zinc-400 hover:text-white text-xl font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('admin.create-product') }}" class="space-y-3 text-sm">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 mb-1">หมวดหมู่:</label>
                        <select name="category_id" required class="w-full text-xs rounded-xl border-[#232938] bg-[#0e1017] p-2.5 text-zinc-200 focus:border-red-500 focus:ring-red-500">
                            <option value="">-- เลือกหมวดหมู่ --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 mb-1">ชื่อสินค้า:</label>
                        <input type="text" name="name" required class="w-full text-xs rounded-xl border-[#232938] bg-[#0e1017] p-2.5 text-white focus:border-red-500 focus:ring-red-500" placeholder="เช่น ชินราเมียนหมูสับ..." />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 mb-1">คำอธิบาย:</label>
                        <textarea name="description" rows="2" class="w-full text-xs rounded-xl border-[#232938] bg-[#0e1017] p-2.5 text-white focus:border-red-500 focus:ring-red-500" placeholder="รายละเอียดเมนู..."></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-300 mb-1">ราคา (บาท):</label>
                            <input type="number" step="0.5" min="0" name="price" required class="w-full text-xs rounded-xl border-[#232938] bg-[#0e1017] p-2.5 font-bold text-white focus:border-red-500 focus:ring-red-500" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-300 mb-1">สต็อกเริ่มต้น:</label>
                            <input type="number" min="0" name="stock_quantity" required class="w-full text-xs rounded-xl border-[#232938] bg-[#0e1017] p-2.5 font-bold text-white focus:border-red-500 focus:ring-red-500" />
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-[#1e2430]">
                        <button type="button" onclick="closeCreateProductModal()" class="salai-card px-4 py-2 text-xs text-zinc-400 hover:text-white border-[#262d3d]">ยกเลิก</button>
                        <button type="submit" class="salai-btn-primary px-5 py-2 text-xs font-semibold shadow">บันทึกสินค้า</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openCreateProductModal() {
            const modal = document.getElementById('createProductModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeCreateProductModal() {
            const modal = document.getElementById('createProductModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
</x-layouts::app>
