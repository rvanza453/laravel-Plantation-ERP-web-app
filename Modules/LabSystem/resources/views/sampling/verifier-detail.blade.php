@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-6">
    <div class="max-w-5xl mx-auto">
        <!-- Header & Navigation -->
        <div class="mb-8">
            <a href="{{ route('lab.sampling.verifier') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold text-sm mb-3 inline-block">
                ← Kembali ke Inbox
            </a>
            <h1 class="text-4xl font-bold text-gray-900">🔍 Verifikasi Lab Sampling</h1>
            <p class="text-gray-600 mt-2">{{ $batch->batch_code ?? 'Batch #' . $batch->id }}</p>
        </div>

        <!-- Batch Info Header -->
        <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-lg mb-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs text-gray-600 font-semibold">Tanggal Sampling</p>
                    <p class="text-lg font-bold text-gray-900">{{ Carbon\Carbon::parse($batch->sampling_date)->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-600 font-semibold">Shift</p>
                    <p class="text-lg font-bold text-gray-900">Shift {{ $batch->shift }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-600 font-semibold">Petugas Sampling</p>
                    <p class="text-lg font-bold text-gray-900">{{ $batch->sampler_name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-600 font-semibold">Status Anomali</p>
                    @if($abnormalCount > 0)
                        <p class="text-lg font-bold text-rose-600">⚠️ {{ $abnormalCount }} Anomali</p>
                    @else
                        <p class="text-lg font-bold text-emerald-600">✅ Normal</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded mb-6">
                @foreach($errors->all() as $error)
                    <p class="text-red-700">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Data Sampel Section -->
        <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-8 border border-white/20 shadow-lg mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">📊 Data Pengukuran Sampel</h2>
            
            <div class="space-y-6">
                @forelse($groupedRows as $category => $measurements)
                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <!-- Category Header -->
                        <div class="bg-gradient-to-r from-indigo-50 to-indigo-100 px-6 py-4 border-b border-indigo-200">
                            <h3 class="text-lg font-bold text-gray-900">📂 {{ $category ?? 'Lainnya' }}</h3>
                        </div>

                        <!-- Measurements Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Parameter</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Standar</th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700">Terukur</th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($measurements as $measurement)
                                        <tr @if($measurement->is_abnormal) class="bg-rose-50" @endif>
                                            <td class="px-6 py-4 font-semibold text-gray-900">{{ $measurement->parameter_name }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">
                                                <code class="bg-gray-100 px-2 py-1 rounded text-xs">{{ $measurement->standard_text ?? '-' }}</code>
                                            </td>
                                            <td class="px-6 py-4 text-center font-semibold text-gray-900">
                                                {{ $measurement->measured_value ?? $measurement->measured_text ?? '-' }} <span class="text-xs text-gray-500">{{ $measurement->unit ?? '' }}</span>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                @if($measurement->is_abnormal)
                                                    <span class="inline-block px-3 py-1 bg-rose-100 text-rose-800 rounded-full text-xs font-semibold">
                                                        ⚠️ Abnormal
                                                    </span>
                                                @else
                                                    <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-semibold">
                                                        ✅ Normal
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-8">Tidak ada data pengukuran</p>
                @endforelse
            </div>
        </div>

        <!-- Verification Form -->
        <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-8 border border-white/20 shadow-lg">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">✅ Verifikasi Sampel</h2>
            
            <form action="{{ route('lab.sampling.approve', $batch->id) }}" method="POST">
                @csrf
                
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        📝 Hasil Analisa & Klarifikasi <span class="text-rose-600">*</span>
                    </label>
                    <p class="text-xs text-gray-600 mb-3">
                        Tulis hasil analisa mendalam Anda. Jelaskan kondisi sampel, identifikasi anomali (jika ada), dan klarifikasi hasil pengukuran. 
                        <strong>Field ini wajib diisi sebelum verifikasi dapat diselesaikan.</strong>
                    </p>
                    <textarea 
                        name="verifier_notes" 
                        rows="8" 
                        placeholder="Contoh: Semua parameter dalam kondisi normal. Hasil FFA 2.5% menunjukkan kualitas CPO yang baik. Tidak ada anomali terdeteksi pada pengukuran hari ini. Sampel memenuhi standar kualitas yang ditetapkan..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono text-sm"
                        required
                    ></textarea>
                    @error('verifier_notes')
                        <p class="text-rose-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Warning Alert -->
                <div class="bg-amber-50 border-l-4 border-amber-500 p-4 mb-6 rounded">
                    <p class="text-sm text-amber-800">
                        <strong>⚠️ Perhatian:</strong> Setelah verifikasi, data tidak dapat diubah atau ditarik kembali. 
                        Pastikan Anda telah membaca dan menganalisa semua hasil dengan cermat sebelum melanjutkan.
                    </p>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="w-full px-6 py-4 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition font-semibold text-lg shadow-lg hover:shadow-xl"
                >
                    ✅ Verifikasi & Publikasikan
                </button>
            </form>

            <!-- Additional Info -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <p class="text-xs text-gray-600">
                    <strong>Catatan:</strong> Verifikasi berarti Anda telah menyetujui dan menganalisa semua data pengukuran. 
                    Data akan langsung dipublikasikan ke laporan manajemen dan tidak dapat diubah.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

