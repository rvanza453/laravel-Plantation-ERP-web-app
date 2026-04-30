@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900">⚙️ Konfigurasi Penugasan Verifikator Lab</h1>
                    <p class="text-gray-600 mt-2">Kelola siapa yang bertugas memverifikasi parameter/kategori lab sampling</p>
                </div>
                <a href="{{ route('lab.config.verifiers.create') }}" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold">
                    ➕ Tambah Penugasan
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-lg mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Type Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tipe Penugasan</label>
                    <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Semua Tipe --</option>
                        <option value="global" {{ $type === 'global' ? 'selected' : '' }}>Global (Semua Parameter)</option>
                        <option value="parameter" {{ $type === 'parameter' ? 'selected' : '' }}>Parameter Spesifik</option>
                        <option value="category" {{ $type === 'category' ? 'selected' : '' }}>Kategori Spesifik</option>
                        <option value="shift" {{ $type === 'shift' ? 'selected' : '' }}>Shift Spesifik</option>
                    </select>
                </div>

                <!-- Site Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Lokasi</label>
                    <select name="site_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Semua Lokasi (Global) --</option>
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}" {{ $siteId == $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                        @endforeach
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
        @if(session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded mb-6">
                <p class="text-emerald-700 font-semibold">✅ {{ session('success') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded mb-6">
                @foreach($errors->all() as $error)
                    <p class="text-red-700">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Table -->
        <div class="bg-white/80 backdrop-blur-xl rounded-2xl border border-white/20 shadow-lg overflow-hidden">
            @if($assignments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-indigo-50 to-indigo-100 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700">Verifikator</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700">Tipe Penugasan</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700">Nilai / Target</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700">Lokasi</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700">Status</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($assignments as $assignment)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4 font-semibold text-gray-900">
                                        👤 {{ $assignment->user->name }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">
                                            {{ ucfirst($assignment->assignment_type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        @if($assignment->assignment_type === 'global')
                                            <code class="bg-gray-100 px-2 py-1 rounded">Semua Parameter</code>
                                        @else
                                            <code class="bg-gray-100 px-2 py-1 rounded">{{ $assignment->assignment_value }}</code>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $assignment->site?->name ?? '🌍 Global' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($assignment->is_active)
                                            <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-semibold">
                                                ✅ Aktif
                                            </span>
                                        @else
                                            <span class="inline-block px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-semibold">
                                                ⏸️ Non-Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center space-x-2">
                                        <a href="{{ route('lab.config.verifiers.edit', $assignment->id) }}" class="inline-block px-3 py-1 text-indigo-600 hover:bg-indigo-50 rounded transition text-sm font-semibold">
                                            ✏️ Edit
                                        </a>
                                        <form action="{{ route('lab.config.verifiers.destroy', $assignment->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Yakin hapus penugasan ini?')" class="px-3 py-1 text-rose-600 hover:bg-rose-50 rounded transition text-sm font-semibold">
                                                🗑️ Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-6 border-t border-gray-200">
                    {{ $assignments->render('pagination::tailwind') }}
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-2xl text-gray-400 mb-2">⚙️</p>
                    <p class="text-gray-600 font-semibold">Belum ada penugasan verifikator</p>
                    <p class="text-sm text-gray-500 mb-4">Mulai dengan menambahkan penugasan baru</p>
                    <a href="{{ route('lab.config.verifiers.create') }}" class="inline-block px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold">
                        ➕ Tambah Penugasan Pertama
                    </a>
                </div>
            @endif
        </div>

        <!-- Info Box -->
        <div class="mt-8 bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg">
            <h3 class="font-semibold text-blue-900 mb-3">ℹ️ Panduan Penggunaan</h3>
            <ul class="text-sm text-blue-800 space-y-2">
                <li>✓ <strong>Global:</strong> Verifikator bertanggung jawab untuk semua parameter</li>
                <li>✓ <strong>Parameter:</strong> Verifikator hanya untuk parameter spesifik (misal: hanya FFA)</li>
                <li>✓ <strong>Kategori:</strong> Verifikator untuk seluruh kategori parameter (misal: semua parameter di kategori "Kualitas")</li>
                <li>✓ <strong>Shift:</strong> Verifikator untuk shift tertentu saja</li>
                <li>✓ <strong>Lokasi:</strong> Jika kosong, penugasan berlaku global untuk semua lokasi</li>
            </ul>
        </div>
    </div>
</div>
@endsection
