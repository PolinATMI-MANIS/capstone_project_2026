<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order | Capstone 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-[#F8F9FA] flex h-screen overflow-hidden text-gray-800">

    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col z-20">
        <div class="h-16 flex items-center px-6 border-b border-gray-100">
            <div class="w-5 h-5 bg-red-500 rounded-sm mr-3"></div>
            <h1 class="text-sm font-bold tracking-widest text-gray-800 uppercase">Capstone 2026</h1>
        </div>
        
        <div class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-2">Modules</div>
        
        <nav class="flex-1 px-3 space-y-1">
            <a href="#" class="flex items-center px-4 py-2.5 text-gray-500 hover:bg-gray-50 rounded-lg transition">
                <span class="font-medium text-sm">Production</span>
            </a>
            <a href="#" class="flex items-center px-4 py-2.5 text-gray-500 hover:bg-gray-50 rounded-lg transition">
                <span class="font-medium text-sm">Inventory</span>
            </a>
            
            <!-- Menu Aktif (Warna Oranye) -->
            <a href="/purchase" class="flex items-center px-4 py-2.5 bg-orange-50 text-orange-600 rounded-lg border-l-4 border-orange-500">
                <span class="font-medium text-sm">Purchase Order</span>
            </a>
            
            <a href="/delivery" class="flex items-center px-4 py-2.5 text-gray-500 hover:bg-gray-50 rounded-lg transition">
                <span class="font-medium text-sm">Delivery Order</span>
            </a>
        </nav>
        
        <div class="p-4 border-t border-gray-100">
            <form action="/logout" method="POST">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 text-sm font-medium text-red-500 hover:bg-red-50 rounded-lg transition">
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col relative">
        
        <!-- Navbar Atas -->
        <header class="h-16 bg-white border-b border-gray-100 flex items-center justify-end px-8 z-10">
            <div class="flex items-center gap-3">
                <p class="text-sm font-medium text-gray-600">{{ auth()->user()->name ?? 'Administrator' }}</p>
                <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-gray-500 font-bold text-xs">A</div>
            </div>
        </header>

        <!-- Area Konten -->
        <div class="p-8 flex-1 overflow-y-auto relative z-10">
            
            <!-- Sistem Tab -->
            <div class="flex gap-3 mb-8 border-b border-gray-200 pb-4">
                <button class="bg-[#FF784B] text-white px-6 py-2 rounded-full text-sm font-semibold shadow-md">Purchase Order</button>
                <button class="bg-white text-gray-500 border border-gray-200 px-6 py-2 rounded-full text-sm font-medium hover:bg-gray-50 transition">Delivery Order</button>
            </div>

            <!-- Header Tabel -->
            <div class="flex justify-between items-end mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Purchase Order Directory</h2>
                    <p class="text-sm text-gray-500 mt-1">Sistem manajemen dan pengawasan dokumen PO.</p>
                </div>
                <button class="bg-[#FF784B] hover:bg-[#e66a3d] text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition shadow-sm">
                    + Add Purchase Order
                </button>
            </div>

            <!-- Tabel Data -->
            <div class="bg-white/80 border border-gray-100 rounded-xl shadow-sm overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 text-[11px] text-gray-400 uppercase tracking-widest bg-gray-50/50">
                            <th class="p-4 font-semibold">ID</th>
                            <th class="p-4 font-semibold">Supplier Name</th>
                            <th class="p-4 font-semibold">Status</th>
                            <th class="p-4 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- State Kosong (Gear Icon) -->
                        <tr>
                            <td colspan="4" class="py-24 text-center">
                                <div class="flex flex-col items-center justify-center opacity-40">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-500 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <p class="text-xs font-bold text-gray-600 uppercase tracking-widest">Belum ada data PO tercatat.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>