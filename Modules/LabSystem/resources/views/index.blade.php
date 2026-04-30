<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#E6F0F9] font-sans antialiased overflow-x-hidden min-h-screen">

    <div class="relative min-h-screen p-4 sm:p-8 flex items-center justify-center">
        
        <div class="absolute top-0 left-0 w-96 h-96 bg-[#D4E8F9] rounded-full mix-blend-multiply filter blur-3xl opacity-70"></div>
        <div class="absolute bottom-0 right-10 w-72 h-72 bg-[#D4E8F9] rounded-full mix-blend-multiply filter blur-3xl opacity-70"></div>

        <div class="relative w-full max-w-[1400px] flex gap-6 rounded-[40px] bg-white/90 p-6 backdrop-blur-xl shadow-[0_20px_50px_-12px_rgba(0,0,0,0.05)] sm:p-8 border border-white">
            
            <div class="hidden w-20 flex-col items-center gap-8 rounded-[30px] bg-[#F7F9FC] py-8 lg:flex border border-gray-100/50">
                <div class="h-10 w-10 rounded-full border-4 border-[#4FA5F5] border-t-[#FF6B93]"></div>
                
                <div class="flex flex-col gap-6 mt-4">
                    <a href="#" class="rounded-full bg-white p-3 shadow-sm text-gray-400 hover:text-gray-800 transition"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg></a>
                    <a href="#" class="rounded-full p-3 text-gray-400 hover:bg-white hover:shadow-sm hover:text-gray-800 transition"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg></a>
                    <div class="rounded-full bg-[#FCE57F] p-3 text-gray-900 shadow-sm cursor-pointer"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg></div>
                    <a href="#" class="rounded-full p-3 text-gray-400 hover:bg-white hover:shadow-sm hover:text-gray-800 transition"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" /></svg></a>
                </div>
            </div>

            <div class="flex-1 space-y-8">
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 px-2 mt-2">
                    <div>
                        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Kendali Mutu</h1>
                        <p class="text-sm font-medium text-gray-400 mt-1">Pusat kendali proses laboratorium kelapa sawit.</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="relative hidden md:block">
                            <input type="text" placeholder="Search something..." class="w-64 rounded-full bg-[#F3F6F9] px-6 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 placeholder-gray-400 border-none">
                            <svg class="absolute right-4 top-3 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-[24px] bg-white p-6 shadow-[0_8px_20px_-6px_rgba(0,0,0,0.03)] border border-gray-50 flex flex-col justify-between">
                        <div class="flex justify-between items-start">
                            <p class="text-sm font-bold text-gray-900">Batch Sampling</p>
                            <span class="text-gray-400 font-bold">:</span>
                        </div>
                        <div class="mt-4 flex items-end justify-between">
                            <p class="text-3xl font-black text-gray-900">{{ number_format($stats['sampling_batches'] ?? 0) }}</p>
                            <div class="h-8 w-8 rounded-full bg-[#4FA5F5] opacity-80"></div>
                        </div>
                    </div>

                    <div class="rounded-[24px] bg-white p-6 shadow-[0_8px_20px_-6px_rgba(0,0,0,0.03)] border border-gray-50 flex flex-col justify-between">
                        <div class="flex justify-between items-start">
                            <p class="text-sm font-bold text-gray-900">Analisa Pending</p>
                            <span class="text-gray-400 font-bold">:</span>
                        </div>
                        <div class="mt-4 flex items-end justify-between">
                            <p class="text-3xl font-black text-gray-900">{{ number_format($stats['pending_analyses'] ?? 0) }}</p>
                            <div class="h-8 w-8 rounded-full bg-[#FF6B93] opacity-80"></div>
                        </div>
                    </div>

                    <div class="rounded-[24px] bg-white p-6 shadow-[0_8px_20px_-6px_rgba(0,0,0,0.03)] border border-gray-50 flex flex-col justify-between">
                        <div class="flex justify-between items-start">
                            <p class="text-sm font-bold text-gray-900">Laporan Terbit</p>
                            <span class="text-gray-400 font-bold">:</span>
                        </div>
                        <div class="mt-4 flex items-end justify-between">
                            <p class="text-3xl font-black text-gray-900">{{ number_format($stats['published_reports'] ?? 0) }}</p>
                            <div class="h-8 w-8 rounded-full bg-[#52D3D8] opacity-80"></div>
                        </div>
                    </div>

                    <div class="rounded-[24px] bg-white p-6 shadow-[0_8px_20px_-6px_rgba(0,0,0,0.03)] border border-gray-50 flex flex-col justify-between">
                        <div class="flex justify-between items-start">
                            <p class="text-sm font-bold text-gray-900">Titik Ukur</p>
                            <span class="text-gray-400 font-bold">:</span>
                        </div>
                        <div class="mt-4 flex items-end justify-between">
                            <p class="text-3xl font-black text-gray-900">{{ number_format($stats['measurement_points'] ?? 0) }}</p>
                            <div class="h-8 w-8 rounded-full bg-[#FCE57F] opacity-80"></div>
                        </div>
                    </div>
                </div>

                <div class="relative overflow-hidden rounded-[30px] bg-[#FCEB8C] p-8 md:p-10 flex flex-col md:flex-row items-center justify-between gap-6 shadow-sm">
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/30 rounded-full blur-2xl"></div>
                    <div class="absolute -bottom-10 right-20 w-32 h-32 bg-white/40 rounded-full blur-xl"></div>
                    
                    <div class="relative z-10 max-w-lg">
                        <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900">Mulai Pengambilan <span class="text-[#FF6B93]">Sampling</span> Baru</h2>
                        <p class="mt-3 text-sm font-medium text-gray-800/80 leading-relaxed">
                            Sistem ini mengelola pengambilan sampel, analisa parameter mutu, hingga pelaporan otomatis secara terintegrasi.
                        </p>
                    </div>
                    
                    <div class="relative z-10 w-full md:w-auto flex justify-end">
                        <a href="{{ route('lab.sampling.form') }}" class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-white text-gray-900 shadow-md hover:scale-105 hover:shadow-lg transition-all duration-300">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-3 rounded-[30px] bg-white p-8 shadow-[0_8px_20px_-6px_rgba(0,0,0,0.03)] border border-gray-50">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-extrabold text-gray-900">Modul Laboratorium</h3>
                            <span class="text-sm font-bold text-[#FF6B93] cursor-pointer hover:underline">See all</span>
                        </div>

                        <div class="space-y-4">
                            
                            <a href="{{ route('lab.sampling.form') }}" class="flex items-center justify-between p-3 rounded-2xl hover:bg-gray-50 transition cursor-pointer group w-full text-left">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#FFE5EE] text-[#FF6B93]">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">Taking Data Sampling</p>
                                        <p class="text-xs font-medium text-gray-400">Registrasi batch & sumber sampel</p>
                                    </div>
                                </div>
                                <div class="text-sm font-bold text-gray-400 group-hover:text-gray-900 transition">&rarr;</div>
                            </a>

                            <div class="flex items-center justify-between p-3 rounded-2xl hover:bg-gray-50 transition cursor-pointer group w-full text-left">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#E5F3FF] text-[#4FA5F5]">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">Analisa Laboratorium</p>
                                        <p class="text-xs font-medium text-gray-400">Pencatatan parameter & validasi</p>
                                    </div>
                                </div>
                                <div class="text-sm font-bold text-gray-400 group-hover:text-gray-900 transition">&rarr;</div>
                            </div>

                            <a href="{{ route('lab.report.quality-daily') }}" class="flex items-center justify-between p-3 rounded-2xl hover:bg-gray-50 transition cursor-pointer group w-full text-left">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#F4EAFB] text-[#A855F7]">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">Penyajian Data (Quality Daily Report)</p>
                                        <p class="text-xs font-medium text-gray-400">Insight kualitas & pelaporan harian</p>
                                    </div>
                                </div>
                                <div class="text-sm font-bold text-gray-400 group-hover:text-gray-900 transition">&rarr;</div>
                            </a>

                            <a href="{{ route('lab.sampling.verifier') }}" class="flex items-center justify-between p-3 rounded-2xl hover:bg-gray-50 transition cursor-pointer group w-full text-left">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#E8FFF6] text-[#10B981]">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7M16 3v4M8 3v4m-6 4h20" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">Inbox Verifier</p>
                                        <p class="text-xs font-medium text-gray-400">Tinjau dan verifikasi hasil analisa</p>
                                    </div>
                                </div>
                                <div class="text-sm font-bold text-gray-400 group-hover:text-gray-900 transition">&rarr;</div>
                            </a>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>