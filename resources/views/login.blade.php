<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Perusahaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="relative min-h-screen flex items-center justify-center">

    <!-- Background Image & Overlay Gelap -->
    <div class="absolute inset-0 z-0">
        <!-- Memanggil gambar latar dari folder public -->
        <img src="{{ asset('img/bg-praktek.jpg') }}" alt="Background Praktek" class="w-full h-full object-cover">
        <!-- Overlay biru dongker transparan agar teks tetap terbaca -->
        <div class="absolute inset-0 bg-[#0A192F]/80 mix-blend-multiply"></div>
    </div>

    <!-- Card Login Glassmorphism -->
    <div class="relative z-10 w-full max-w-md bg-white/10 backdrop-blur-md border border-white/20 p-8 rounded-2xl shadow-2xl mx-4">
        
        <!-- Logo Prodi -->
        <div class="flex justify-center mb-6">
            <img src="{{ asset('img/logo-atmi.jpg') }}" alt="Logo ATMI Cikarang" class="h-24 w-auto rounded-xl shadow-lg border-2 border-white/30">
        </div>

        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-white tracking-wide">HELLO ! Come On Sign In</h2>
            <p class="text-teal-400 text-sm mt-1 font-medium">Masuk ke akun mu untuk lihat update data terbaru </p>
        </div>

        <!-- Alert Error -->
        @if(session('error'))
            <div class="bg-red-500/20 border border-red-500/50 text-red-200 px-4 py-3 rounded-lg mb-6 text-sm text-center">
                {{ session('error') }}
            </div>
        @endif

        <form action="/proses-login" method="POST">
            @csrf
            
            <div class="mb-5">
                <label for="email" class="block text-gray-300 text-sm font-medium mb-2">Email Perusahaan</label>
                <div class="relative">
                    <input type="email" id="email" name="email" required 
                        class="w-full bg-[#0A192F]/50 border border-gray-500/50 rounded-lg py-3 px-4 text-white focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition placeholder-gray-500" 
                        placeholder="nama@perusahaan.ac.id">
                </div>
            </div>
            
            <div class="mb-6">
                <label for="password" class="block text-gray-300 text-sm font-medium mb-2">Kata Sandi</label>
                <input type="password" id="password" name="password" required 
                    class="w-full bg-[#0A192F]/50 border border-gray-500/50 rounded-lg py-3 px-4 text-white focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition placeholder-gray-500 mb-2" 
                    placeholder="••••••••">
            </div>
            
            <div class="mb-6 mt-8">
                <button type="submit" 
                    class="w-full bg-teal-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-teal-500 transition duration-300 shadow-[0_0_15px_rgba(13,148,136,0.4)]">
                    LOGIN
                </button>
            </div>

            <div class="text-center text-sm text-gray-400">
                Butuh bantuan? <a href="#" class="text-teal-400 hover:text-teal-300 transition font-medium">Hubungi Admin</a>
            </div>
        </form>
    </div>

</body>
</html>