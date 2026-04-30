@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900">📋 Inbox Verifikasi Lab</h1>
                    <p class="text-gray-600 mt-2">Daftar sampel yang menunggu verifikasi dan persetujuan Anda</p>
                </div>
                <a href="{{ route('lab.config.verifiers') }}" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold">
                    ⚙️ Kelola Config
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-lg mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Date From -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ $dateFrom->format('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Date To -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ $dateTo->format('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Site Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Lokasi</label>
                    <select name="site_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Semua Lokasi --</option>
                        @foreach($sites as $id => $name)
                            <option value="{{ $id }}" {{ $siteFilter == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Shift Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Shift</label>
                    <select name="shift" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Semua Shift --</option>
                        <option value="1" {{ $shiftFilter == '1' ? 'selected' : '' }}>Shift 1</option>
                        <option value="2" {{ $shiftFilter == '2' ? 'selected' : '' }}>Shift 2</option>
                        <option value="3" {{ $shiftFilter == '3' ? 'selected' : '' }}>Shift 3</option>
                    </select>
                </div>

                <!-- Filter Button -->
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold">
                        🔍 Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Notifications -->
        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded mb-6">
                <p class="text-red-700 font-semibold">Terjadi Kesalahan</p>
                @foreach($errors->all() as $error)
                    <p class="text-red-600 text-sm">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if(session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded mb-6">
                <p class="text-emerald-700 font-semibold">✅ {{ session('success') }}</p>
            </div>
        @endif

        <!-- Inbox List -->
        <div class="space-y-4">
            @forelse($batches as $batch)
                <a href="{{ route('lab.sampling.verifier.detail', $batch->id) }}" class="block group">
                    <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-lg hover:shadow-xl hover:border-indigo-200 transition cursor-pointer">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <!-- Batch Info -->
                                <div class="flex items-center gap-4 mb-3">
                                    <span class="inline-block px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-semibold">
                                        Shift {{ $batch->shift }}
                                    </span>
                                    <span class="text-sm text-gray-600">{{ Carbon\Carbon::parse($batch->sampling_date)->format('d M Y') }}</span>
                                    <span class="text-sm text-gray-600">{{ Carbon\Carbon::parse($batch->submitted_at)->format('H:i') }}</span>
                                </div>

                                <p class="text-lg font-bold text-gray-900 mb-1">{{ $batch->batch_code ?? 'Batch #' . $batch->id }}</p>
                                <p class="text-sm text-gray-600">Petugas: <span class="font-semibold">{{ $batch->sampler_name ?? '-' }}</span></p>
                                <p class="text-sm text-gray-600">Unit: <span class="font-semibold">{{ $batch->source_unit ?? '-' }}</span></p>
                            </div>

                            <!-- Status Badge -->
                            <div class="text-right">
                                @if($batch->abnormal_count > 0)
                                    <div class="inline-block px-4 py-2 bg-rose-100 text-rose-800 rounded-lg mb-2">
                                        <p class="text-sm font-bold">⚠️ {{ $batch->abnormal_count }} Anomali</p>
                                    </div>
                                @else
                                    <div class="inline-block px-4 py-2 bg-emerald-100 text-emerald-800 rounded-lg mb-2">
                                        <p class="text-sm font-bold">✅ Normal</p>
                                    </div>
                                @endif
                                <p class="text-xs text-gray-500 mt-1">Klik untuk verifikasi →</p>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="text-center py-12">
                    <p class="text-2xl text-gray-400 mb-2">📭</p>
                    <p class="text-gray-600 font-semibold">Tidak ada sampel yang menunggu verifikasi</p>
                    <p class="text-sm text-gray-500">Semua sampel sudah diverifikasi atau filter terlalu spesifik</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $batches->render('pagination::tailwind') }}
        </div>
    </div>
</div>
@endsection
