document.addEventListener('DOMContentLoaded', () => {
    // -- Fitur Umum (untuk semua halaman) --

    // 1. Mode Gelap
    const themeToggle = document.getElementById('theme-toggle');
    const sunIcon = document.getElementById('theme-icon-sun');
    const moonIcon = document.getElementById('theme-icon-moon');
    const body = document.body;

    const applyTheme = (theme) => {
        if (theme === 'dark') {
            body.classList.add('dark-mode');
            sunIcon.style.display = 'block';
            moonIcon.style.display = 'none';
        } else {
            body.classList.remove('dark-mode');
            sunIcon.style.display = 'none';
            moonIcon.style.display = 'block';
        }
    };
    
    const savedTheme = localStorage.getItem('theme') || 'light';
    applyTheme(savedTheme);

    themeToggle.addEventListener('click', () => {
        const newTheme = body.classList.contains('dark-mode') ? 'light' : 'dark';
        localStorage.setItem('theme', newTheme);
        applyTheme(newTheme);
    });

    // 2. Menu Hamburger
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.querySelector('.nav-menu');
    hamburger.addEventListener('click', () => {
        navMenu.classList.toggle('active');
    });


    // -- Fitur Spesifik Halaman --

    // 1. Slider Gambar (hanya untuk index.html)
    const sliderWrapper = document.querySelector('.slider-wrapper');
    if (sliderWrapper) {
        const slides = document.querySelectorAll('.slide');
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');
        const dotsContainer = document.querySelector('.slider-dots');

        let currentIndex = 0;
        const totalSlides = slides.length;
        let autoPlayInterval;

        slides.forEach((_, i) => {
            const dot = document.createElement('span');
            dot.classList.add('dot');
            dot.addEventListener('click', () => { goToSlide(i); resetAutoPlay(); });
            dotsContainer.appendChild(dot);
        });

        const dots = document.querySelectorAll('.dot');
        const goToSlide = (index) => {
            if (index < 0) index = totalSlides - 1;
            else if (index >= totalSlides) index = 0;
            sliderWrapper.style.transform = `translateX(-${index * 100}%)`;
            currentIndex = index;
            updateDots();
        };
        const updateDots = () => {
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === currentIndex);
            });
        };

        prevBtn.addEventListener('click', () => { goToSlide(currentIndex - 1); resetAutoPlay(); });
        nextBtn.addEventListener('click', () => { goToSlide(currentIndex + 1); resetAutoPlay(); });

        const startAutoPlay = () => { autoPlayInterval = setInterval(() => goToSlide(currentIndex + 1), 4000); };
        const resetAutoPlay = () => { clearInterval(autoPlayInterval); startAutoPlay(); };
        
        goToSlide(0);
        startAutoPlay();
    }

    // 2. Search Functionality (hanya untuk menu.html)
    const menuSearch = document.getElementById('menuSearch');
    if (menuSearch) {
        const menuItems = document.querySelectorAll('.menu-item');
        
        menuSearch.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase().trim();
            
            menuItems.forEach(item => {
                const title = item.querySelector('h3').textContent.toLowerCase();
                const description = item.querySelector('p').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || description.includes(searchTerm)) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });
        });
    }

    // 3. Validasi Form (hanya untuk contact.html)
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const messageInput = document.getElementById('message');
        const successMessage = document.getElementById('form-success-message');
        
        // Function untuk validasi email
        const isValidEmail = (email) => {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        };

        // Function untuk menampilkan error dengan animasi
        const showError = (input, message) => {
            const errorElement = document.getElementById(`${input.id}-error`);
            errorElement.textContent = message;
            
            // Trigger reflow untuk animasi
            errorElement.style.display = 'block';
            errorElement.offsetHeight;
            
            // Tambahkan class untuk animasi
            errorElement.classList.add('visible');
            input.classList.add('error');
            
            // Animasi shake pada input
            input.style.animation = 'shake 0.5s';
            input.addEventListener('animationend', () => {
                input.style.animation = '';
            }, {once: true});
        };

        // Function untuk menyembunyikan error dengan animasi
        const hideError = (input) => {
            const errorElement = document.getElementById(`${input.id}-error`);
            errorElement.classList.remove('visible');
            input.classList.remove('error');
            
            // Tunggu animasi selesai baru sembunyikan element
            setTimeout(() => {
                if (!errorElement.classList.contains('visible')) {
                    errorElement.style.display = 'none';
                }
            }, 300);
        };

        // Event listeners untuk input fields
        [nameInput, emailInput, messageInput].forEach(input => {
            input.addEventListener('input', () => {
                hideError(input);
                successMessage.style.display = 'none';
            });
        });

        contactForm.addEventListener('submit', (e) => {
            e.preventDefault();
            let isValid = true;

            // Validasi nama
            if (!nameInput.value.trim()) {
                showError(nameInput, 'Nama tidak boleh kosong.');
                isValid = false;
            }

            // Validasi email
            if (!emailInput.value.trim()) {
                showError(emailInput, 'Email tidak boleh kosong.');
                isValid = false;
            } else if (!isValidEmail(emailInput.value.trim())) {
                showError(emailInput, 'Format email tidak valid.');
                isValid = false;
            }

            // Validasi pesan
            if (!messageInput.value.trim()) {
                showError(messageInput, 'Pesan tidak boleh kosong.');
                isValid = false;
            }

            // Jika semua validasi sukses
            if (isValid) {
                successMessage.style.display = 'block';
                successMessage.offsetHeight; // Trigger reflow
                successMessage.classList.add('visible');
                successMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                
                // Reset form setelah delay singkat
                setTimeout(() => {
                    contactForm.reset();
                    // Reset semua error messages
                    [nameInput, emailInput, messageInput].forEach(hideError);
                }, 100);
            }
        });
    }
});