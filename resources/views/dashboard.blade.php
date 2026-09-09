<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Manajemen Industri ATMI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50 flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-[#0A192F] text-white flex flex-col">
        <div class="h-16 flex items-center justify-center border-b border-gray-700">
            <h1 class="text-lg font-bold tracking-widest text-teal-400">MI - ATMI</h1>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="/dashboard" class="flex items-center px-4 py-3 bg-teal-600/20 text-teal-400 rounded-lg">
                <span class="font-medium">Dashboard</span>
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-gray-300 hover:bg-white/5 hover:text-white rounded-lg transition">
                <span class="font-medium">Data Mahasiswa</span>
            </a>
        </nav>
        <div class="p-4 border-t border-gray-700">
            <!-- Form Logout -->
            <form action="/logout" method="POST">
                @csrf
                <button type="submit" class="w-full bg-red-500/10 text-red-400 font-medium py-2 rounded-lg hover:bg-red-500/20 transition">
                    Keluar Sistem
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col">
        <!-- Navbar Atas -->
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8">
            <h2 class="text-xl font-semibold text-gray-800">Sistem Informasi Akademik</h2>
            <div class="flex items-center gap-3">
                <div class="text-sm text-right">
                    <p class="font-bold text-gray-700">{{ auth()->user()->name }}</p>
                    <p class="text-gray-500">{{ auth()->user()->email }}</p>
                </div>
                <div class="h-10 w-10 bg-teal-600 rounded-full flex items-center justify-center text-white font-bold">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
            </div>
        </header>

        <!-- Area Konten Utama -->
        <div class="p-8 flex-1 overflow-y-auto">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-2">Selamat Datang, {{ auth()->user()->name }}! 👋</h3>
                <p class="text-gray-600">Ini adalah halaman dashboard utama. Di sini kamu dan kelompokmu bisa mulai membuat tabel CRUD (Create, Read, Update, Delete) untuk mengelola data kampus.</p>
            </div>
        </div>
    </main>

</body>
</html>