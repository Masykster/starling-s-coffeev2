<?php
require_once 'config.php';
$pageTitle = "Starling's Coffee";
include 'includes/header.php';
?>

    <!-- Konten Halaman Beranda -->
    <main>
        <!-- Slider Gambar -->
        <section class="slider-section py-4">
            <div class="container">
                <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner rounded">
                        <div class="carousel-item active">
                            <img src="images/slider-1.jpg" class="d-block w-100" alt="Suasana kedai kopi yang nyaman" 
                                width="1200" height="500" style="height: 500px; object-fit: cover;">
                        </div>
                        <div class="carousel-item">
                            <img src="images/slider-2.jpg" class="d-block w-100" alt="Secangkir kopi latte art" 
                                width="1200" height="500" loading="lazy" style="height: 500px; object-fit: cover;">
                        </div>
                        <div class="carousel-item">
                            <img src="images/slider-3.jpg" class="d-block w-100" alt="Proses pembuatan kopi" 
                                width="1200" height="500" loading="lazy" style="height: 500px; object-fit: cover;">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </section>
        
        <section class="card bg-light-green py-5 my-4">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <img src="images/menu.jpg" alt="Menu Makanan" class="img-fluid rounded" loading="lazy" width="600" height="400">
                    </div>
                    <div class="col-md-6 text-center py-4">
                        <h1 class="display-4 fw-bold mb-4">Explore Our Menu</h1>
                        <p class="lead mb-4">Coba Kelezatan Menu - Menu Kami yang berkualitas dengan Menggunakan Bahan baku yang Terbaik.</p>
                        <a href="menu.php" class="btn btn-outline-dark btn-lg">Browse Here</a>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="card reverse bg-light-pink py-5 my-4">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 order-md-2">
                        <img src="images/about.jpg" alt="Tentang" class="img-fluid rounded" loading="lazy" width="600" height="400">
                    </div>
                    <div class="col-md-6 order-md-1 text-center py-4">
                        <h2 class="display-5 fw-bold mb-4">Our Story</h2>
                        <p class="lead mb-4">Bagaimana Cerita dan Misi Kami.</p>
                        <a href="about.php" class="btn btn-outline-dark btn-lg">Look at Us</a>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="card bg-light-green py-5 my-4">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <img src="images/contact.jpg" alt="Kontak" class="img-fluid rounded" loading="lazy" width="600" height="400">
                    </div>
                    <div class="col-md-6 text-center py-4">
                        <h2 class="display-5 fw-bold mb-4">Contact Us</h2>
                        <p class="lead mb-4">Punya Pertanyaan? Hubungi Kami</p>
                        <a href="contact.php" class="btn btn-outline-dark btn-lg">Call Us Here</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

<?php include 'includes/footer.php'; ?>

