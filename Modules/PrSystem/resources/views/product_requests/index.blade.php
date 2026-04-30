<x-prsystem::app-layout>
    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Usul Product Master') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Ajukan produk baru untuk dicek admin sebelum masuk master data.</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-600"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Ada kesalahan dalam form:</h3>
                            <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('product-requests.store') }}" class="space-y-6 max-w-3xl">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-prsystem::input-label for="code" :value="__('Product Code')" />
                                <x-prsystem::text-input id="code" class="block mt-1 w-full uppercase" type="text" name="code" :value="old('code')" required autofocus oninput="this.value = this.value.toUpperCase()" />
                                <x-prsystem::input-error :messages="$errors->get('code')" class="mt-2" />
                            </div>

                            <div>
                                <x-prsystem::input-label for="name" :value="__('Product Name')" />
                                <x-prsystem::text-input id="name" class="block mt-1 w-full uppercase" type="text" name="name" :value="old('name')" required oninput="this.value = this.value.toUpperCase()" />
                                <x-prsystem::input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-prsystem::input-label for="category" :value="__('Category')" />
                            <select id="category" name="category" class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm" required>
                                <option value="">-- Select Category --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                            <x-prsystem::input-error :messages="$errors->get('category')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <x-prsystem::input-label for="unit" :value="__('Unit')" />
                                <select id="unit" name="unit" class="block mt-1 w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm" required>
                                    <option value="">-- Pilih Unit --</option>
                                    <option value="PCS" {{ old('unit') == 'PCS' ? 'selected' : '' }}>PCS</option>
                                    <option value="UNIT" {{ old('unit') == 'UNIT' ? 'selected' : '' }}>UNIT</option>
                                    <option value="SET" {{ old('unit') == 'SET' ? 'selected' : '' }}>SET</option>
                                    <option value="KG" {{ old('unit') == 'KG' ? 'selected' : '' }}>KG</option>
                                    <option value="RIM" {{ old('unit') == 'RIM' ? 'selected' : '' }}>RIM</option>
                                    <option value="LTR" {{ old('unit') == 'LTR' ? 'selected' : '' }}>LTR</option>
                                    <option value="KBK" {{ old('unit') == 'KBK' ? 'selected' : '' }}>KBK</option>
                                    <option value="MTR" {{ old('unit') == 'MTR' ? 'selected' : '' }}>MTR</option>
                                </select>
                                <x-prsystem::input-error :messages="$errors->get('unit')" class="mt-2" />
                            </div>

                            <div>
                                <x-prsystem::input-label for="price_estimation_display" :value="__('Estimasi Harga (Rp)')" />
                                <x-prsystem::text-input 
                                    id="price_estimation_display" 
                                    class="block mt-1 w-full" 
                                    type="text" 
                                    :value="old('price_estimation')" 
                                    required 
                                    oninput="formatPrice(this)" 
                                />
                                <input type="hidden" name="price_estimation" id="price_estimation" value="{{ old('price_estimation') }}">
                                <x-prsystem::input-error :messages="$errors->get('price_estimation')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-prsystem::input-label for="reference_link" :value="__('Link Referensi')" />
                            <x-prsystem::text-input id="reference_link" class="block mt-1 w-full" type="url" name="reference_link" :value="old('reference_link')" placeholder="https://..." required />
                            <x-prsystem::input-error :messages="$errors->get('reference_link')" class="mt-2" />
                        </div>

                        <input type="hidden" name="site_id" value="{{ auth()->user()->site_id }}">

                        <div class="flex items-center gap-4">
                            <x-prsystem::primary-button>{{ __('Kirim Usulan') }}</x-prsystem::primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Riwayat Usulan Saya</h3>
                            <p class="text-sm text-gray-500">Status request akan berubah setelah dicek admin.</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3">Code</th>
                                    <th class="px-4 py-3">Name</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Decision By</th>
                                    <th class="px-4 py-3">Reference</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse($requests as $request)
                                    <tr>
                                        <td class="px-4 py-3 font-mono text-gray-800">{{ $request->code }}</td>
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-gray-900">{{ $request->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $request->category }} - {{ $request->unit }}</div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $request->status === 'Approved' ? 'bg-green-100 text-green-800' : ($request->status === 'Rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ $request->status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">{{ $request->decisionBy?->name ?? '-' }}</td>
                                        <td class="px-4 py-3">
                                            <a href="{{ $request->reference_link }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 break-all">{{ $request->reference_link }}</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-10 text-center text-gray-500">Belum ada usulan produk.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-prsystem::app-layout>

<script>
    function formatPrice(input) {
        // 1. Hilangkan semua karakter selain angka (huruf, titik, koma dihapus)
        let rawValue = input.value.replace(/[^0-9]/g, '');
        // 2. Masukkan angka murni ke input tersembunyi untuk dikirim ke database
        document.getElementById('price_estimation').value = rawValue;
        // 3. Format angka dengan pemisah ribuan (titik) untuk tampilan
        if (rawValue) {
            input.value = parseInt(rawValue, 10).toLocaleString('id-ID');
        } else {
            input.value = '';
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        let displayInput = document.getElementById('price_estimation_display');
        if (displayInput && displayInput.value) {
            formatPrice(displayInput);
        }
    });
</script>