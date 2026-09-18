<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Capstone 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="relative min-h-screen flex items-center justify-center">

    <!-- Background Image & Overlay Gelap -->
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('img/bg-praktek.jpg') }}" alt="Background Praktek" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-[#0A192F]/80 mix-blend-multiply"></div>
    </div>

    <!-- Card Login Glassmorphism -->
    <div class="relative z-10 w-full max-w-md bg-white/10 backdrop-blur-md border border-white/20 p-8 rounded-2xl shadow-2xl mx-4">
        
        <!-- Logo ATMI -->
        <div class="flex justify-center mb-6">
            <img src="{{ asset('img/logo-atmi.jpg') }}" alt="Logo ATMI Cikarang" class="h-20 w-auto rounded-xl shadow-lg border-2 border-white/30">
        </div>

        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-white tracking-wide">HELLO ! Come On Sign In</h2>
            <p class="text-teal-400 text-sm mt-1 font-medium">Masuk ke akun mu untuk lihat update data terbaru</p>
        </div>

        <!-- Alert Error (Mendukung session 'error' maupun 'loginError') -->
        @if(session('error') || session('loginError'))
            <div class="bg-red-500/20 border border-red-500/50 text-red-200 px-4 py-3 rounded-lg mb-6 text-sm text-center">
                {{ session('error') ?? session('loginError') }}
            </div>
        @endif

        <form action="/proses-login" method="POST">
            @csrf
            
            <div class="mb-5">
                <label for="email" class="block text-gray-300 text-sm font-medium mb-2">Email Perusahaan</label>
                <input type="email" id="email" name="email" required 
                    class="w-full bg-[#0A192F]/50 border border-gray-500/50 rounded-lg py-3 px-4 text-white focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition placeholder-gray-500" 
                    placeholder="nama@perusahaan.ac.id">
            </div>
            
            <div class="mb-6">
                <label for="password" class="block text-gray-300 text-sm font-medium mb-2">Kata Sandi</label>
                <input type="password" id="password" name="password" required 
                    class="w-full bg-[#0A192F]/50 border border-gray-500/50 rounded-lg py-3 px-4 text-white focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition placeholder-gray-500" 
                    placeholder="••••••••">
            </div>
            
            <div class="mb-6 mt-8">
                <button type="submit" 
                    class="w-full bg-teal-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-teal-500 transition duration-300 shadow-[0_0_15px_rgba(13,148,136,0.4)]">
                    LOGIN
                </button>
            </div>

            <div class="text-center text-sm text-gray-400">
                Butuh bantuan?<a href="https://wa.me/6282210385033?text=Halo%20Admin,%20saya%20butuh%20bantuan%20karena%20tidak%20bisa%20login%20ke%20sistem." 
                target="_blank" 
                class="text-info text-decoration-none fw-bold" 
                style="font-size: 0.85rem;">
                    Hubungi Admin
                </a>
            </div>
        </form>
    </div>

</body>
</html>