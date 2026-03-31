<div>
    {{-- Header --}}
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-lg font-bold text-[#009EF5]">{{ $departmentLabel }} — Menu Management</h1>
            <p class="text-xs text-gray-400">Manage menu items, categories, and inventory in one place.</p>
        </div>
        <div class="flex items-center gap-2">
            @if($branches->isNotEmpty())
                <select wire:model.live="branchId" class="rounded-md border-gray-300 text-sm focus:ring-[#009EF5] focus:border-[#009EF5]">
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            @endif
            <button wire:click="toggleView"
                class="inline-flex items-center gap-1 rounded-md border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50 transition">
                @if($viewMode === 'table')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                    Grid View
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    Table View
                @endif
            </button>
        </div>
    </div>

    {{-- Table View --}}
    @if($viewMode === 'table')
        {{ $this->table }}
    @endif

    {{-- Grid/Card View --}}
    @if($viewMode === 'grid')
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @forelse($gridItems as $item)
                <div class="group relative bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md hover:border-[#009EF5]/30 transition-all duration-200">
                    {{-- Image --}}
                    <div class="relative aspect-square bg-gray-50 flex items-center justify-center overflow-hidden">
                        @if($item->image)
                            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="flex flex-col items-center justify-center text-gray-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                <span class="text-[10px] mt-1">No image</span>
                            </div>
                        @endif
                        {{-- Price badge --}}
                        <div class="absolute top-2 right-2 bg-[#009EF5] text-white text-xs font-bold px-2 py-0.5 rounded-full shadow">
                            ₱{{ number_format($item->price, 2) }}
                        </div>
                        {{-- Stock badge --}}
                        <div class="absolute top-2 left-2 text-xs font-semibold px-2 py-0.5 rounded-full shadow
                            {{ ($item->inventory?->number_of_serving ?? 0) <= 0 ? 'bg-red-100 text-red-700' : (($item->inventory?->number_of_serving ?? 0) <= 10 ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                            {{ ($item->inventory?->number_of_serving ?? 0) > 0 ? ($item->inventory->number_of_serving . ' pcs') : 'No stock' }}
                        </div>
                    </div>
                    {{-- Info --}}
                    <div class="p-3">
                        <h3 class="text-sm font-semibold text-gray-800 truncate">{{ $item->name }}</h3>
                        <div class="flex items-center gap-1 mt-1">
                            @if($item->category?->parent)
                                <span class="inline-flex items-center rounded-full px-1.5 py-0.5 text-[10px] font-medium {{ $item->category->parent->name === 'Food' ? 'bg-green-50 text-green-700' : 'bg-blue-50 text-blue-700' }}">
                                    {{ $item->category->parent->name }}
                                </span>
                            @endif
                            <span class="text-[10px] text-gray-400">{{ $item->category?->name ?? '—' }}</span>
                        </div>
                        @if($item->item_code)
                            <p class="text-[10px] text-gray-400 mt-0.5">{{ $item->item_code }}</p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                    <p class="text-sm">No menu items yet. Add your first item!</p>
                </div>
            @endforelse
        </div>
    @endif

    {{-- Category Management Modal --}}
    <x-modal wire:model="categoryModal" max-width="2xl">
        <x-card title="Manage Categories" subtitle="Add, edit, or delete subcategories under Food / Drinks.">
            {{-- Add/Edit Form --}}
            <div class="flex gap-2 mb-4">
                <div class="flex-1">
                    <x-input wire:model="categoryName" placeholder="Category name" />
                </div>
                <div class="w-40">
                    <x-native-select wire:model="categoryParentId">
                        <option value="">Select type</option>
                        @foreach($mainCategories as $mc)
                            <option value="{{ $mc->id }}">{{ $mc->name }}</option>
                        @endforeach
                    </x-native-select>
                </div>
                <x-button wire:click="saveCategory" class="bg-[#009EF5] text-white hover:bg-[#0080cc]">
                    {{ $editCategoryId ? 'Update' : 'Add' }}
                </x-button>
                @if($editCategoryId)
                    <x-button wire:click="cancelEditCategory" flat>Cancel</x-button>
                @endif
            </div>

            {{-- Category List --}}
            <div class="border rounded-lg divide-y max-h-72 overflow-y-auto">
                @forelse($subcategories as $cat)
                    <div class="flex items-center justify-between px-3 py-2 hover:bg-gray-50">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $cat->parent?->name === 'Food' ? 'bg-green-50 text-green-700' : 'bg-blue-50 text-blue-700' }}">
                                {{ $cat->parent?->name }}
                            </span>
                            <span class="text-sm text-gray-700">{{ $cat->name }}</span>
                        </div>
                        <div class="flex gap-1">
                            <button wire:click="editCategory({{ $cat->id }})" class="text-gray-400 hover:text-[#009EF5] transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </button>
                            <button wire:click="deleteCategory({{ $cat->id }})" wire:confirm="Delete category '{{ $cat->name }}'?" class="text-gray-400 hover:text-red-500 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="px-3 py-6 text-center text-gray-400 text-sm">No subcategories yet.</div>
                @endforelse
            </div>
        </x-card>
    </x-modal>
</div>
