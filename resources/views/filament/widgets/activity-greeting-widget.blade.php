<x-filament-widgets::widget>
    <x-filament::section>
        <div class="greeting-widget">
            <div class="greeting-content">
                {{-- <img src="{{ asset('images/logo.png') }}" alt="Logo" class="greeting-logo"> --}}
                <div class="greeting-text">
                    <h2>Selamat Beraktivitas!</h2>
                    <p>Semoga hari ini penuh produktivitas dan kebahagiaan.</p>
                </div>
            </div>
        </div>
    </x-filament::section>

    <style>
        .greeting-widget {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f3f4f6;
            /* padding: 5px; Menurunkan padding agar lebih compact */
            border-radius: 8px;
            /* font-size: 0.7rem; */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 100%; /* Menjaga agar widget tidak terlalu lebar */
            width: 100%; /* Menggunakan 100% lebar kontainer */
        }

        .greeting-content {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column; /* Menjaga teks tetap berada di tengah */
        }

        .greeting-logo {
            width: 40px; /* Memperkecil ukuran logo */
            height: 40px;
            margin-bottom: 10px; /* Mengurangi margin bawah logo */
        }

        .greeting-text h2 {
            font-family: 'Arial', sans-serif;
            font-size: 1.2rem; /* Mengurangi ukuran font */
            color: #2c3e50;
            /* margin-bottom: 5px; */
            text-align: center;
        }

        .greeting-text p {
            font-size: 0.7rem;
            color: #7f8c8d;
            text-align: center;
        }

        .greeting-widget:hover {
            background-color: #e1e8eb;
        }
    </style>
</x-filament-widgets::widget>
