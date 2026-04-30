@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-6">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('lab.config.verifiers') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold text-sm mb-3 inline-block">
                ← Kembali
            </a>
            <h1 class="text-4xl font-bold text-gray-900">
                @if(isset($assignment))
                    ✏️ Edit Penugasan Verifikator
                @else
                    ➕ Tambah Penugasan Verifikator Baru
                @endif
            </h1>
        </div>

        <!-- Form Card -->
        <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-8 border border-white/20 shadow-lg">
            <form action="{{ isset($assignment) ? route('lab.config.verifiers.update', $assignment->id) : route('lab.config.verifiers.store') }}" method="POST">
                @csrf
                @if(isset($assignment))
                    @method('PUT')
                @endif

                <!-- Verifikator Selection -->
                <div class="mb-6">
                    <label for="user_id" class="block text-sm font-semibold text-gray-700 mb-2">👤 Pilih Verifikator</label>
                    <select name="user_id" id="user_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Pilih Verifikator --</option>
                        @foreach($verifiers as $verifier)
                            <option value="{{ $verifier->id }}" 
                                {{ (isset($assignment) && $assignment->user_id == $verifier->id) || old('user_id') == $verifier->id ? 'selected' : '' }}>
                                {{ $verifier->name }} ({{ implode(', ', $verifier->roles->pluck('name')->toArray()) ?? 'No Role' }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Assignment Type -->
                <div class="mb-6">
                    <label for="assignment_type" class="block text-sm font-semibold text-gray-700 mb-2">📋 Tipe Penugasan</label>
                    <select name="assignment_type" id="assignment_type" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="toggleValueField()">
                        <option value="">-- Pilih Tipe --</option>
                        <option value="global" {{ (isset($assignment) && $assignment->assignment_type == 'global') || old('assignment_type') == 'global' ? 'selected' : '' }}>
                            🌍 Global (Semua Parameter)
                        </option>
                        <option value="parameter" {{ (isset($assignment) && $assignment->assignment_type == 'parameter') || old('assignment_type') == 'parameter' ? 'selected' : '' }}>
                            📌 Parameter Spesifik
                        </option>
                        <option value="category" {{ (isset($assignment) && $assignment->assignment_type == 'category') || old('assignment_type') == 'category' ? 'selected' : '' }}>
                            📂 Kategori Spesifik
                        </option>
                        <option value="shift" {{ (isset($assignment) && $assignment->assignment_type == 'shift') || old('assignment_type') == 'shift' ? 'selected' : '' }}>
                            ⏰ Shift Spesifik
                        </option>
                    </select>
                    @error('assignment_type')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Assignment Value (Dynamic) -->
                <div id="value-field" class="mb-6">
                    <label for="assignment_value" class="block text-sm font-semibold text-gray-700 mb-2">🎯 Target Penugasan</label>
                    <select name="assignment_value" id="assignment_value" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Pilih --</option>
                    </select>
                    @error('assignment_value')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-2">Pilih Parameter atau Kategori sesuai dengan tipe penugasan</p>
                </div>

                <!-- Site Selection -->
                <div class="mb-6">
                    <label for="site_id" class="block text-sm font-semibold text-gray-700 mb-2">📍 Lokasi (Opsional)</label>
                    <select name="site_id" id="site_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">🌍 Global (Semua Lokasi)</option>
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}" {{ (isset($assignment) && $assignment->site_id == $site->id) || old('site_id') == $site->id ? 'selected' : '' }}>
                                {{ $site->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('site_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-2">Kosongkan untuk membuat penugasan berlaku di semua lokasi</p>
                </div>

                <!-- Notes -->
                <div class="mb-6">
                    <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">📝 Catatan / Keterangan</label>
                    <textarea name="notes" id="notes" rows="3" placeholder="Contoh: Verifikator utama untuk parameter kualitas hasil..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ isset($assignment) ? $assignment->notes : old('notes') }}</textarea>
                </div>

                <!-- Active Status -->
                <div class="mb-6 flex items-center gap-3">
                    <input type="checkbox" name="is_active" id="is_active" value="1" 
                        {{ (isset($assignment) && $assignment->is_active) || old('is_active') == '1' ? 'checked' : '' }}
                        class="w-5 h-5 text-indigo-600 rounded focus:ring-2 focus:ring-indigo-500">
                    <label for="is_active" class="text-sm font-semibold text-gray-700">
                        ✅ Penugasan Aktif
                    </label>
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 pt-6 border-t border-gray-200">
                    <button type="submit" class="flex-1 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold">
                        {{ isset($assignment) ? '💾 Simpan Perubahan' : '➕ Tambah Penugasan' }}
                    </button>
                    <a href="{{ route('lab.config.verifiers') }}" class="flex-1 px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-semibold text-center">
                        ❌ Batal
                    </a>
                </div>
            </form>
        </div>

        <!-- Info -->
        <div class="mt-8 bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg">
            <h3 class="font-semibold text-blue-900 mb-2">💡 Tips</h3>
            <ul class="text-sm text-blue-800 space-y-1">
                <li>• Jika tipe "Global", verifikator akan menangani semua parameter tanpa filter</li>
                <li>• Jika tipe "Parameter", pilih parameter spesifik (misal: FFA, Moisture)</li>
                <li>• Jika tipe "Kategori", pilih kategori parameter (misal: fisik, kimia)</li>
                <li>• Lokasi bisa dikosongkan untuk penugasan global ke semua site</li>
            </ul>
        </div>
    </div>
</div>

<script>
    const parameterSelect = {
        parameter: @json($parameters ?? []),
        category: @json($categories ?? []),
        shift: ['1', '2', '3']
    };

    function toggleValueField() {
        const type = document.getElementById('assignment_type').value;
        const valueSelect = document.getElementById('assignment_value');
        
        valueSelect.innerHTML = '<option value="">-- Pilih --</option>';
        
        if (type && parameterSelect[type]) {
            parameterSelect[type].forEach(val => {
                const option = document.createElement('option');
                option.value = val;
                option.text = val;
                @if(isset($assignment))
                    if (val === '{{ $assignment->assignment_value }}') {
                        option.selected = true;
                    }
                @endif
                valueSelect.appendChild(option);
            });
        } else if (type === 'global') {
            valueSelect.disabled = true;
            valueSelect.innerHTML = '<option value="">N/A - Global</option>';
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
        toggleValueField();
    });
</script>
@endsection
