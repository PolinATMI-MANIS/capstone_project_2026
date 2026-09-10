<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Capstone 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<!-- Ubah URL background-image di bawah ini dengan gambar aslinya -->
<body class="relative flex items-center justify-center h-screen bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&w=1920&q=80');">
    
    <!-- Overlay Gelap -->
    <div class="absolute inset-0 bg-black/50"></div>

    <!-- Card Login Glassmorphism -->
    <div class="relative z-10 bg-white/10 backdrop-blur-md border border-white/20 p-8 rounded-2xl shadow-2xl w-full max-w-md text-white">
        
        <!-- Placeholder Logo -->
        <div class="bg-white rounded-lg p-2 w-24 h-12 mx-auto mb-6 flex items-center justify-center">
            <span class="text-xs text-black font-bold">LOGO ATMI</span>
        </div>
        
        <div class="text-center mb-8">
            <h1 class="text-xl font-bold mb-1 uppercase tracking-wide">HELLO ! Come On Sign In</h1>
            <p class="text-teal-400 text-xs font-medium">Masuk ke akun mu untuk lihat update data terbaru</p>
        </div>

        @if(session()->has('loginError'))
            <div class="bg-red-500/20 border border-red-500/50 text-red-200 p-3 rounded-lg text-sm mb-4 text-center">
                {{ session('loginError') }}
            </div>
        @endif

        <form action="/proses-login" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium mb-1 text-gray-300">Email Perusahaan</label>
                <input type="email" name="email" required class="w-full px-4 py-2.5 bg-[#1a2234]/80 border border-gray-600/50 rounded-lg focus:outline-none focus:border-teal-500 text-white placeholder-gray-500 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium mb-1 text-gray-300">Kata Sandi</label>
                <input type="password" name="password" required class="w-full px-4 py-2.5 bg-[#1a2234]/80 border border-gray-600/50 rounded-lg focus:outline-none focus:border-teal-500 text-white text-sm">
            </div>
            <button type="submit" class="w-full bg-[#10b981] hover:bg-[#059669] text-white font-bold py-2.5 rounded-lg transition mt-4 text-sm tracking-wide">
                LOGIN
            </button>
        </form>

        <p class="text-center text-xs text-gray-400 mt-6">Butuh bantuan? <a href="#" class="text-teal-400 hover:underline">Hubungi Admin</a></p>
    </div>

</body>
</html>