<x-filament-panels::page>

    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- Tag lainnya -->
    </head>
    <div class="kasir-page">
        <!-- Search and Filter Section -->
        <div class="filter-section">
            <input type="text" id="search-input" class="search-input" placeholder="Search products..."
                oninput="filterProducts()">
            <select id="category-filter" class="category-filter" onchange="filterProducts()">
                <option value="">Select Category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->name }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="content-container">
            <!-- Product List Section -->
            <div class="product-list">
                @foreach ($products as $product)
                    <div class="product-card" data-category="{{ $product->category->name }}">
                        <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/250x250' }}"
                            alt="{{ $product->name }}" class="product-image">
                        <div class="product-info">
                            <h3 class="product-name">{{ $product->name }}</h3>
                            <div class="product-details">
                                <p class="product-price">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</p>
                                <p class="product-stock">Stock: {{ $product->stock }}</p>
                            </div>
                            <div class="counter">
                                <button class="add-to-cart"
                                    onclick="addToCart('{{ $product->id }}', '{{ $product->name }}', {{ $product->sell_price }}, '{{ $product->category->name }}')">
                                    <i class="cart-icon">+ Add</i>
                                </button>
                                <button class="add-to-cart"
                                    onclick="removeToCart('{{ $product->id }}', '{{ $product->name }}', {{ $product->sell_price }}, '{{ $product->category->name }}')">
                                    <i class="cart-icon">- Remove</i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Cart Preview Section -->
            <div class="cart-preview" id="cart-preview">
                <div class="cart-header">
                    <h3>Cart Preview</h3>
                    <span id="cart-total-items" class="cart-total-items">0 Items</span>
                </div>
                <ul class="cart-items" id="cart-items">
                    <!-- Cart items will be added dynamically -->
                </ul>
                <div class="cart-summary">
                    <div class="cart-total">
                        <span>Total:</span>
                        <span id="cart-total-price">Rp 0</span>
                    </div>

                    <!-- Notes input -->
                    <textarea id="checkout-notes" placeholder="Add notes (optional)" rows="4"
                        style="width: 100%; border-radius: 10px; padding: 10px;"></textarea>
                    <button id="checkout-btn" class="checkout-btn" onclick="checkout()">Proceed to Checkout</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        body {
            font-family: 'Inter', 'Arial', sans-serif;
            background-color: #f5f7fb;
            color: #333;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        .kasir-page {
            /* margin: 0 auto; */
            padding: 20px;
        }

        .filter-section {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .search-input,
        .category-filter {
            flex-grow: 1;
            padding: 12px;
            border: 2px solid #e1e5eb;
            border-radius: 12px;
            background-color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .search-input:focus,
        .category-filter:focus {
            border-color: #4299e1;
            box-shadow: 0 4px 10px rgba(66, 153, 225, 0.2);
            outline: none;
        }

        .content-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        .product-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .product-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: all 0.4s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
        }

        .product-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .product-info {
            padding: 15px;
        }

        .product-name {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .product-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .product-price {
            font-size: 14px;
            font-weight: 600;
            color: #38a169;
        }

        .product-stock {
            font-size: 12px;
            color: #718096;
        }

        .counter {
            display: flex;

        }

        .add-to-cart {
            width: 100%;
            padding: 10px;
            background-color: #4299e1;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .add-to-cart:hover {
            background-color: #3182ce;
        }

        .cart-preview {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 20px;
            min-height: 50px;
            /* Tinggi minimum */
            max-height: 400px;
            /* Tinggi maksimum untuk scroll */
            overflow-y: auto;
            /* Scroll jika konten melebihi tinggi maksimum */
            display: none;
            /* Sembunyikan secara default */
        }


        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .cart-total-items {
            background-color: #4299e1;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
        }

        .cart-items {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .cart-item {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            /* Kolom untuk nama, kuantitas, dan harga */
            align-items: center;
            /* Sejajarkan elemen secara vertikal */
            gap: 10px;
            /* Jarak antar elemen */
            padding: 10px 0;
            border-bottom: 1px solid #edf2f7;
        }

        .cart-item .item-name {
            font-size: 14px;
            /* Ukuran teks */
            font-weight: 500;
            color: #333;
            overflow: visible;
            /* Pastikan nama produk tidak terpotong */
            word-wrap: break-word;
            /* Bungkus teks jika terlalu panjang */
        }

        .cart-item .item-quantity,
        .cart-item .item-price {
            font-size: 13px;
            /* Ukuran teks lebih kecil untuk kuantitas dan harga */
            text-align: center;
            /* Posisi tengah */
            color: #555;
        }


        .cart-summary {
            margin-top: 20px;
        }

        .cart-total {
            display: flex;
            justify-content: space-between;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .checkout-btn {
            width: 100%;
            padding: 12px;
            background-color: #48bb78;
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .checkout-btn:hover {
            background-color: #38a169;
        }

        #checkout-notes {
            width: 100%;
            border-radius: 10px;
            padding: 10px;
            resize: none;
            /* Menghilangkan kemampuan resize */
            min-height: 80px;
            /* Memberikan tinggi minimal */
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --dark-bg-primary: #121212;
                --dark-bg-secondary: #1e1e1e;
                --dark-text-primary: #ffffff;
                --dark-text-secondary: #e0e0e0;
                --dark-accent-color: #4299e1;
                --dark-accent-hover: #3182ce;
                --dark-border-color: #ffffff;
                --dark-shadow-color: rgba(255, 255, 255, 0.1);
            }
        }

        /* Explicit Light Mode Styling */
        body,
        .fi-page-content {
            background-color: var(--light-bg-primary) !important;
            color: var(--light-text-primary) !important;
        }

        /* Global Color Application */
        .kasir-page {
            background-color: var(--light-bg-primary);
            color: var(--light-text-primary);
        }

        .search-input,
        .category-filter {
            background-color: var(--light-bg-secondary) !important;
            color: var(--light-text-primary) !important;
            border-color: var(--light-border-color) !important;
            box-shadow: 5px 5px 0 var(--light-shadow-color) !important;
        }

        .product-card {
            background-color: var(--light-bg-secondary) !important;
            border-color: var(--light-border-color) !important;
            box-shadow: 6px 6px 0 var(--light-shadow-color) !important;
            color: var(--light-text-primary) !important;
        }

        .product-name {
            color: var(--light-text-primary) !important;
        }

        .product-price {
            color: var(--light-accent-color) !important;
        }

        .add-to-cart,
        .checkout-btn {
            background-color: var(--light-accent-color) !important;
            color: var(--light-bg-secondary) !important;
            border-color: var(--light-border-color) !important;
            box-shadow: 5px 5px 0 var(--light-border-color) !important;
        }

        .cart-preview {
            background-color: var(--light-bg-secondary) !important;
            border-color: var(--light-border-color) !important;
            box-shadow: 8px 8px 0 var(--light-shadow-color) !important;
            color: var(--light-text-primary) !important;
        }

        /* Neobrutalist Design Elements */
        .search-input,
        .category-filter,
        .product-card,
        .add-to-cart,
        .checkout-btn,
        .cart-preview {
            transition: all 0.3s ease;
            border-width: 3px;
            border-style: solid;
            border-radius: 12px;
        }

        /* Hover Effects */
        .product-card:hover {
            transform: translate(-6px, -6px);
            box-shadow: 10px 10px 0 var(--light-accent-color) !important;
        }

        .add-to-cart:hover,
        .checkout-btn:hover {
            background-color: var(--light-accent-hover) !important;
            transform: translate(-4px, -4px);
            box-shadow: 7px 7px 0 var(--light-border-color) !important;
        }

        /* Layout and Responsiveness */
        .kasir-page {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            padding: 20px;
            width: 100%;
        }

        .content-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        @media (min-width: 1024px) {
            .content-container {
                grid-template-columns: 2fr 1fr;
            }
        }

        .product-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }

        /* Additional Refinements */
        .product-image {
            border-bottom: 3px solid var(--light-border-color);
        }

        /* Ensure readability and contrast */
        .product-stock {
            color: var(--light-text-secondary);
        }
    </style>

    <script>
        console.log("Helllo");
        const cart = [];

        function addToCart(productId, productName, productPrice, productCategory) {
            const existingProduct = cart.find(item => item.id === productId);

            if (existingProduct) {
                existingProduct.quantity += 1; // Increase quantity if product already in cart
            } else {
                cart.push({
                    id: productId,
                    name: productName,
                    price: productPrice,
                    category: productCategory,
                    quantity: 1
                });
            }

            // Show cart preview section
            const cartPreview = document.getElementById('cart-preview');
            const cartTotalItems = document.getElementById('cart-total-items');
            const cartTotalPrice = document.getElementById('cart-total-price');
            const totalPrice = cart.reduce((total, item) => {
                return total + (item.price * item.quantity);
            }, 0);
            cartTotalItems.textContent = `${cart.length} items`;
            cartTotalPrice.textContent = `Rp. ${totalPrice.toLocaleString()}`;
            cartPreview.style.display = 'block';

            const jsonCart = JSON.stringify(cart, null, 2); // Mengonversi array cart ke string JSON yang indah
            console.log(jsonCart);
            // Update the cart preview with added products
            updateCartPreview();
        }

        function removeToCart(productId, productName, productPrice, productCategory) {
            const existingProduct = cart.find(item => item.id === productId);

            if(existingProduct.quantity <= 0 ){
                return;
            }
            else if (existingProduct) {
                existingProduct.quantity -= 1; // Increase quantity if product already in cart
            }

            // Show cart preview section
            const cartPreview = document.getElementById('cart-preview');
            const cartTotalItems = document.getElementById('cart-total-items');
            const cartTotalPrice = document.getElementById('cart-total-price');
            const totalPrice = cart.reduce((total, item) => {
                return total + (item.price * item.quantity);
            }, 0);
            cartTotalItems.textContent = `${cart.length} items`;
            cartTotalPrice.textContent = `Rp. ${totalPrice.toLocaleString()}`;
            cartPreview.style.display = 'block';

            const jsonCart = JSON.stringify(cart, null, 2); // Mengonversi array cart ke string JSON yang indah
            console.log(jsonCart);
            // Update the cart preview with added products
            updateCartPreview();
        }

        // Update the cart preview UI
        function updateCartPreview() {
            const cartItemsContainer = document.getElementById('cart-items');
            cartItemsContainer.innerHTML = ''; // Clear the previous items

            cart.forEach(item => {
                const cartItem = document.createElement('li');
                cartItem.classList.add('cart-item');
                cartItem.innerHTML = `
                <span class="cart-item-price">Rp ${item.price.toLocaleString()}</span>
                <span class="cart-item-quantity">x${item.quantity}</span>
                <span class="cart-item-name">${item.name}</span>
                `;
                cartItemsContainer.appendChild(cartItem);
            });
        }
        // Filter products based on category and search input
        function filterProducts() {
            const searchInput = document.getElementById('search-input').value.toLowerCase();
            const categoryFilter = document.getElementById('category-filter').value;
            const productCards = document.querySelectorAll('.product-card');

            productCards.forEach(card => {
                const productName = card.querySelector('.product-name').textContent.toLowerCase();
                const productCategory = card.getAttribute('data-category');
                console.log('Search Input:', searchInput);
                console.log('Category Filter:', categoryFilter);

                // Filter by category and search input
                if (
                    (productName.includes(searchInput) || searchInput === '') &&
                    (productCategory.includes(categoryFilter) || categoryFilter === '')
                ) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
        async function checkout() {
            try {
                const notes = document.getElementById('checkout-notes').value;
                const filteredCart = cart.map(item => {
                    return {
                        id: item.id,
                        price: item.price,
                        quantity: item.quantity
                    };
                });

                console.log(filteredCart); // Log untuk memeriksa data cart yang dikirim

                const response = await fetch('/checkout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                    },
                    body: JSON.stringify({
                        cart: filteredCart,
                        notes: notes,
                    })
                });
                console.log(response);
                const data = await response.json();

                if (data.message !== 'Checkout successful') {
                    alert('Checkout failed 2');
                } else {
                    alert('Checkout successful');
                    cart.length = 0; // Clear the cart after checkout
                    updateCartPreview(); // Refresh the cart preview
                }
            } catch (error) {
                //Teori menggilakkan
                // alert('Checkout successful');
                cart.length = 0; // Clear the cart after checkout
                window.location.href = '/admin/invoice-views';

                //Kode asli dibawah:
                // alert('Checkout failed');
                // console.error('Checkout error:', error);
            }
        }
    </script>
</x-filament-panels::page>
