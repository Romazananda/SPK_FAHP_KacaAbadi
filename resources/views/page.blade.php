<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kaca Abadi - Beranda</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 text-gray-800 font-sans">

    {{-- Header --}}
    <header class="bg-white shadow-md p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold text-indigo-600">Kaca Abadi</h1>
            <nav>
                <a href="#features" class="px-3 text-gray-600 hover:text-indigo-600">Fitur</a>
                <a href="#about" class="px-3 text-gray-600 hover:text-indigo-600">Tentang</a>
            </nav>
        </div>
    </header>

    {{-- Hero Section --}}
    <section 
        class="relative text-white text-center py-20 bg-cover bg-center" 
        style="background-image: url('{{ asset('images/kaca2.jpg') }}');"
    >
        <div class="absolute inset-0 bg-black/50"></div> <!-- overlay agar teks tetap terbaca -->
        <div class="relative z-10">
            <h2 class="hero-title">Selamat Datang di Kaca Abadi</h2>
            <p class="hero-subtitle">Kualitas, desain, dan keindahan dalam satu produk.</p>
            <div class="mt-6 flex justify-center gap-4">
                <a href="/register" class="btn-secondary">Daftar Sekarang</a>
                <a href="/login" class="btn-primary">Masuk</a>
            </div>
        </div>
    </section>

    {{-- Features Section --}}
    <section id="features" class="py-20 bg-gray-100 text-gray-800">
        <div class="container-center">
            <h3 class="section-title">Fitur Kami</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="card">
                    <h4 class="font-semibold text-xl mb-2">Kualitas Terbaik</h4>
                    <p>Produk kaca premium dengan desain modern dan daya tahan yang tinggi.</p>
                </div>
                <div class="card">
                    <h4 class="font-semibold text-xl mb-2">Desain Custom</h4>
                    <p>Kami menerima pesanan kaca dengan desain sesuai keinginan Anda untuk hasil yang unik.</p>
                </div>
                <div class="card">
                    <h4 class="font-semibold text-xl mb-2">Harga Terjangkau</h4>
                    <p>Memberikan kualitas premium dengan harga yang tetap ramah di kantong pelanggan.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- About Section --}}
    <section id="about" class="py-20 bg-white text-gray-800">
        <div class="container-center max-w-3xl">
            <h3 class="section-title">Tentang Kaca Abadi</h3>
            <p class="mb-4">Kami menyediakan berbagai produk dan layanan kaca berkualitas tinggi untuk kebutuhan rumah maupun bisnis Anda.</p>
            <p>Dengan fokus pada kepuasan pelanggan dan inovasi berkelanjutan, kami berkomitmen memberikan pengalaman terbaik untuk Anda.</p>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-gray-900 text-white py-6 text-center">
        <p>&copy; {{ date('Y') }} Kaca Abadi. All rights reserved.</p>
    </footer>

</body>
</html>
