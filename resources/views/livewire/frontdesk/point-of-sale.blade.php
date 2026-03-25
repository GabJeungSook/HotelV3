<div class="h-full w-full flex bg-gray-100 overflow-hidden">

    <!-- LEFT SIDE -->
    <div class="flex-1 flex flex-col bg-gray-100">

        <!-- HEADER -->
        <div class="bg-white px-6 py-4 border-b flex justify-between items-center">

            <input
                type="text"
                placeholder="Search menu..."
                class="w-96 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none"
            />

            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-gray-300"></div>
                <div>
                    <p class="text-sm font-semibold">Cashier</p>
                    <p class="text-xs text-gray-400">POS User</p>
                </div>
            </div>

        </div>


        <!-- CATEGORY -->
        <div class="bg-white px-6 py-4 border-b">

            <h2 class="font-semibold text-gray-700 mb-3">
                Choose Category
            </h2>

            <div class="flex gap-3 flex-wrap">

                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">
                    Popular
                </button>

                <button class="px-4 py-2 bg-gray-100 rounded-lg text-sm">
                    Rice Bowl
                </button>

                <button class="px-4 py-2 bg-gray-100 rounded-lg text-sm">
                    Coffee
                </button>

                <button class="px-4 py-2 bg-gray-100 rounded-lg text-sm">
                    Dessert
                </button>

                <button class="px-4 py-2 bg-gray-100 rounded-lg text-sm">
                    Snack
                </button>

            </div>

        </div>


        <!-- PRODUCTS (ONLY SCROLLABLE AREA) -->
        <div class="flex-1 overflow-y-auto p-6">

            <div class="grid grid-cols-3 gap-6">

                <div class="bg-white rounded-xl shadow hover:shadow-lg transition p-4">
                    <div class="h-40 bg-gray-200 rounded-lg mb-3"></div>

                    <h3 class="font-semibold text-gray-700">
                        Rice Bowl
                    </h3>

                    <p class="text-blue-600 font-bold mb-3">
                        ₱120
                    </p>

                    <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-sm">
                        Add to Billing
                    </button>
                </div>


                <div class="bg-white rounded-xl shadow hover:shadow-lg transition p-4">
                    <div class="h-40 bg-gray-200 rounded-lg mb-3"></div>

                    <h3 class="font-semibold text-gray-700">
                        Salmon Bowl
                    </h3>

                    <p class="text-blue-600 font-bold mb-3">
                        ₱180
                    </p>

                    <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-sm">
                        Add to Billing
                    </button>
                </div>

            </div>

        </div>

    </div>



    <!-- BILLING PANEL -->
<div class="w-96 bg-white border-l flex flex-col h-full">

    <!-- BILL HEADER -->
    <div class="px-6 py-5 border-b shrink-0">
        <h2 class="text-lg font-bold text-gray-700">
            Bills
        </h2>
    </div>


    <!-- BILL ITEMS (ONLY SCROLLABLE AREA) -->
    <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4">

        <div class="flex justify-between items-center">

            <div>
                <p class="font-semibold text-gray-700">Miso Ramen</p>
                <p class="text-sm text-gray-400">Over Hard, Mild</p>
            </div>

            <div class="flex items-center gap-2">

                <button class="w-7 h-7 bg-gray-200 rounded">-</button>

                <span>1</span>

                <button class="w-7 h-7 bg-blue-600 text-white rounded">+</button>

            </div>

        </div>

    </div>


    <!-- TOTAL (FIXED BOTTOM) -->
    <div class="border-t px-6 py-4 space-y-2 shrink-0">

        <div class="flex justify-between text-gray-500">
            <span>Subtotal</span>
            <span>₱200</span>
        </div>

        <div class="flex justify-between text-gray-500">
            <span>Tax</span>
            <span>₱24</span>
        </div>

        <div class="flex justify-between font-bold text-lg">
            <span>Total</span>
            <span class="text-blue-600">₱224</span>
        </div>

        <button class="w-full mt-3 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold">
            Checkout
        </button>

    </div>

</div>

</div>