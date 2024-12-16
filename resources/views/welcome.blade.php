<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetHub Market - Temukan Sahabat Berbulu Anda</title>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', 'Arial', sans-serif;
        }

        body {
            background-color: #f0f4f8;
            color: #1a202c;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
            min-height: 90vh;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
            min-height: 15vh;
        }

        /* .map-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
            min-height: 90vh;
        } */

        /* Navigation Styles */
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
        }

        .nav-links {
            display: flex;
            gap: 15px;
        }

        .nav-links a {
            text-decoration: none;
            color: #2d3748;
            border: 2px solid #2d3748;
            padding: 8px 12px;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .nav-links a:hover {
            background-color: #2d3748;
            color: #ffffff;
        }

        .mobile-menu-btn {
            display: none;
            border: 2px solid #2d3748;
            padding: 8px;
            background: none;
            cursor: pointer;
            border-radius: 4px;
        }

        /* Hero Section */
        .hero {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            padding: 50px 20px;
            align-items: center;
        }

        .hero-content {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .hero-title {
            font-size: 2.5rem;
            color: #2d3748;
            border-bottom: 3px solid #4a5568;
            padding-bottom: 10px;
        }

        .hero-image {
            position: relative;
            border: 3px solid #2d3748;
            background-color: #ffffff;
            box-shadow: 8px 8px 0 0 rgba(45,55,72,0.2);
            border-radius: 8px;
            overflow: hidden;
        }

        .hero-image img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 5px;
        }

        /* Map Section */
        .map-section {
            padding: 50px 20px;
            background-color: #ffffff;
            display: flex;
    flex-direction: column;
    gap: 1rem;
        }

        .map-container {
    width: 100%;
    height: 400px;
    border: 3px solid #2d3748;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 8px 8px 0 0 rgba(45,55,72,0.2);
    background-color: #ffffff; /* Pastikan latar belakang putih agar tidak ada tumpang tindih warna */
    position: relative; /* Pastikan posisi relatif agar elemen di dalamnya terstruktur dengan baik */

}


        /* Footer */
        footer {
            background-color: #2d3748;
            color: #ffffff;
            height: 100%;
            padding: 50px 20px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
        }

        .neobrutalist-button {
    display: inline-block;
    align-self: center;
    padding: 7px 15px;
    background-color: #f8e99f; /* Bold yellow background */
    color: #000; /* Black text */
    font-size: 1.2rem;
    font-weight: 700;
    text-transform: uppercase;
    text-decoration: none;
    border: 4px solid #000; /* Bold black border */
    box-shadow: 6px 6px 0 #000; /* Bold shadow */
    transition: all 0.2s ease;
    position: relative;
    cursor: pointer;
}

.neobrutalist-button:hover {
    background-color: #000; /* Invert colors */
    color: #fddd3e; /* Yellow text */
    box-shadow: 4px 4px 0 #000; /* Slightly smaller shadow */
    transform: translate(-2px, -2px); /* Shift upwards for effect */
}

.neobrutalist-button:active {
    box-shadow: 2px 2px 0 #000; /* Minimal shadow on click */
    transform: translate(0, 0); /* Reset position on click */
}

        .footer-section h3 {
            margin-bottom: 15px;
            font-size: 1.5rem;
            color: #e2e8f0;
        }

        @media (max-width: 768px) {
            .hero, .footer-grid {
                grid-template-columns: 1fr;
            }

            .nav-links {
                display: none;
            }

            .mobile-menu-btn {
                display: block;
            }
        }
    </style>
</head>
<body>
    <nav>
        <div class="logo">
            <img src="{{ asset('images/logo_kucing.png') }}" alt="Pet Market Hero">
            <h1>Marelan Petmarket</h1>
        </div>
        <div class="nav-links">
            <a href="/admin/login">Login</a>
        </div>
        <button class="mobile-menu-btn">Menu</button>
    </nav>

    <div class="hero container">
        <div class="hero-content">
            <h2 class="hero-title">Temukan Sahabat Berbulu Anda</h2>
            <p>Marelan Petmarket - Dapatkan produk terbaik untuk kucing dan anjing kesayangan anda.</p>
        </div>
        <div class="hero-image">
            <img src="{{ asset('images/logo_kucing.png') }}" alt="Pet Market Hero">
        </div>
    </div>


    <div class="map-section container">
        <div class="map-container" id="map"></div>
        <a href="https://www.google.com/maps/place/Pet+Market+Marelan/@3.633189,98.6578681,17z/data=!4m14!1m7!3m6!1s0x30313333a3eb2625:0xc7498de33d563852!2sPet+Market+Marelan!8m2!3d3.633189!4d98.660443!16s%2Fg%2F11sw0ct8ts!3m5!1s0x30313333a3eb2625:0xc7498de33d563852!8m2!3d3.633189!4d98.660443!16s%2Fg%2F11sw0ct8ts?entry=ttu&g_ep=EgoyMDI0MTIxMS4wIKXMDSoASAFQAw%3D%3D" class="neobrutalist-button" target="blank">Visit Link</a>
    </div>


    <footer>
        <div class="footer-grid footer-container">
            <div class="footer-section">
                <h3>Marelan Petmarket</h3>
                <p>Platform terbaik untuk pecinta kucing dan anjing.</p>
            </div>
            <div class="footer-section">
                <h3>Kontak</h3>
                <p>Telepon: +62 821-6389-4688</p>
            </div>
        </div>
    </footer>

    <!-- Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        // Mobile Menu Toggle
        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        const navLinks = document.querySelector('.nav-links');

        mobileMenuBtn.addEventListener('click', () => {
            navLinks.classList.toggle('show');
        });

        // Initialize Map
        document.addEventListener('DOMContentLoaded', () => {
            // Koordinat untuk Medan, Sumatera Utara (contoh lokasi)
            const map = L.map('map').setView([3.6333067763353166, 98.66045372434878], 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Marker untuk lokasi Marelan Petmarket
            L.marker([3.5952, 98.6722])
             .addTo(map)
             .bindPopup('Marelan Petmarket')
             .openPopup();
        });
    </script>
</body>
</html>
