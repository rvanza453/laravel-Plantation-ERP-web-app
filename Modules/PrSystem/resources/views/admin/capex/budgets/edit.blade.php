<x-prsystem::app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Budget CAPEX
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="xl:col-span-1 space-y-6">
                    <div class="rounded-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-900 p-6 text-white shadow-xl">
                        <div class="text-xs uppercase tracking-[0.2em] text-white/60">Budget Code</div>
                        <div class="mt-2 text-2xl font-black tracking-tight">{{ $budget->budget_code }}</div>
                        <div class="mt-3 text-sm text-white/75">{{ $budget->department?->name ?? '-' }} · {{ $budget->capexAsset?->name ?? '-' }}</div>

                        <div class="mt-6 grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-xl bg-white/10 p-3">
                                <div class="text-white/60 text-xs">Total Limit</div>
                                <div class="mt-1 font-bold">Rp {{ number_format((float) $budget->amount + (float) ($budget->pta_amount ?? 0), 0, ',', '.') }}</div>
                            </div>
                            <div class="rounded-xl bg-white/10 p-3">
                                <div class="text-white/60 text-xs">Sisa</div>
                                <div class="mt-1 font-bold">Rp {{ number_format($budget->remaining_amount, 0, ',', '.') }}</div>
                            </div>
                            <div class="rounded-xl bg-white/10 p-3">
                                <div class="text-white/60 text-xs">Terpakai</div>
                                <div class="mt-1 font-bold">Rp {{ number_format($usedAmount, 0, ',', '.') }}</div>
                            </div>
                            <div class="rounded-xl bg-white/10 p-3">
                                <div class="text-white/60 text-xs">Qty Terpakai</div>
                                <div class="mt-1 font-bold">{{ number_format($usedQuantity, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <div class="text-sm font-semibold text-gray-900">Status Budget</div>
                                <div class="text-xs text-gray-500 mt-1">PTA dikelola dari halaman daftar budget.</div>
                            </div>
                            @if($budget->is_budgeted)
                                <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">Budgeted</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">Unbudgeted</span>
                            @endif
                        </div>

                        <div class="mt-4 space-y-2 text-sm text-gray-600">
                            <div class="flex items-center justify-between"><span>Requests terkait</span><span class="font-semibold text-gray-900">{{ $budget->capex_requests_count }}</span></div>
                            <div class="flex items-center justify-between"><span>Budget awal</span><span class="font-semibold text-gray-900">Rp {{ number_format($budget->amount, 0, ',', '.') }}</span></div>
                            <div class="flex items-center justify-between"><span>PTA aktif</span><span class="font-semibold text-gray-900">Rp {{ number_format($budget->pta_amount ?? 0, 0, ',', '.') }}</span></div>
                        </div>
                    </div>
                </div>

                <div class="xl:col-span-2">
                    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-200">
                        <div class="border-b border-gray-100 px-6 py-5">
                            <h3 class="text-lg font-bold text-gray-900">Perbarui Budget</h3>
                            <p class="mt-1 text-sm text-gray-500">Sesuaikan nilai budget tanpa mengubah data yang sudah dipakai oleh request aktif.</p>
                        </div>

                        <form action="{{ route('admin.capex.budgets.update', $budget) }}" method="POST" class="p-6 space-y-6">
                            @csrf
                            @method('PUT')

                            @if(session('error'))
                                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                                    {{ session('error') }}
                                </div>
                            @endif

                            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                                @if($budget->capex_requests_count > 0)
                                    Budget ini sudah dipakai oleh request aktif, jadi department dan asset tetap terkunci untuk menjaga histori tetap konsisten.
                                @else
                                    Budget ini belum dipakai. Department dan asset masih bisa disesuaikan jika diperlukan.
                                @endif
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Department</label>
                                    <select name="department_id" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 text-sm font-medium {{ $budget->capex_requests_count > 0 ? 'bg-gray-100 text-gray-500' : '' }}" {{ $budget->capex_requests_count > 0 ? 'disabled' : '' }}>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" @selected(old('department_id', $budget->department_id) == $department->id)>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($budget->capex_requests_count > 0)
                                        <input type="hidden" name="department_id" value="{{ $budget->department_id }}">
                                    @endif
                                    @error('department_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Asset</label>
                                    <select id="capex_asset_id" name="capex_asset_id" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 text-sm font-medium {{ $budget->capex_requests_count > 0 ? 'bg-gray-100 text-gray-500' : '' }}" {{ $budget->capex_requests_count > 0 ? 'disabled' : '' }}>
                                        @foreach($assets as $asset)
                                            <option value="{{ $asset->id }}" @selected(old('capex_asset_id', $budget->capex_asset_id) == $asset->id)>
                                                {{ $asset->code }} - {{ $asset->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($budget->capex_requests_count > 0)
                                        <input type="hidden" name="capex_asset_id" value="{{ $budget->capex_asset_id }}">
                                    @endif
                                    @error('capex_asset_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Total Quantity</label>
                                    <input type="number" name="original_quantity" min="1" value="{{ old('original_quantity', $budget->original_quantity) }}" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 text-sm font-medium" required>
                                    @error('original_quantity')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Total Amount</label>
                                    <input type="number" name="amount" min="0" value="{{ old('amount', $budget->amount) }}" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 text-sm font-medium" required>
                                    @error('amount')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Fiscal Year</label>
                                    <input type="number" name="fiscal_year" value="{{ old('fiscal_year', $budget->fiscal_year) }}" min="2020" max="2099" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 text-sm font-medium" required>
                                    @error('fiscal_year')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>

                                <div class="flex items-center gap-4 pt-8">
                                    <label class="inline-flex items-center gap-3 cursor-pointer">
                                        <input type="checkbox" name="is_budgeted" value="1" {{ old('is_budgeted', $budget->is_budgeted) ? 'checked' : '' }} class="w-5 h-5 rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 cursor-pointer">
                                        <span class="text-sm font-semibold text-gray-900">Budgeted</span>
                                    </label>
                                    <label class="inline-flex items-center gap-3 cursor-pointer">
                                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $budget->is_active) ? 'checked' : '' }} class="w-5 h-5 rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 cursor-pointer">
                                        <span class="text-sm font-semibold text-gray-900">Active</span>
                                    </label>
                                </div>
                            </div>

                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                                <div class="flex items-center justify-between gap-3 flex-wrap">
                                    <div>
                                        <div class="font-semibold text-slate-900">Ringkasan setelah update</div>
                                        <div class="text-xs text-slate-500 mt-1">Total limit baru akan dihitung otomatis dari amount + PTA yang sudah ada.</div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                                        <div class="rounded-lg bg-white px-3 py-2 border border-slate-200">
                                            <div class="text-xs text-slate-500">Terpakai</div>
                                            <div class="font-bold text-slate-900">Rp {{ number_format($usedAmount, 0, ',', '.') }}</div>
                                        </div>
                                        <div class="rounded-lg bg-white px-3 py-2 border border-slate-200">
                                            <div class="text-xs text-slate-500">Sisa saat ini</div>
                                            <div class="font-bold text-slate-900">Rp {{ number_format($budget->remaining_amount, 0, ',', '.') }}</div>
                                        </div>
                                        <div class="rounded-lg bg-white px-3 py-2 border border-slate-200">
                                            <div class="text-xs text-slate-500">Qty terpakai</div>
                                            <div class="font-bold text-slate-900">{{ number_format($usedQuantity, 0, ',', '.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                                <a href="{{ route('admin.capex.budgets.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 text-sm font-medium transition-colors">
                                    Batal
                                </a>
                                <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 text-sm font-semibold shadow-sm transition-colors">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const assetSelect = document.getElementById('capex_asset_id');

            if (assetSelect && !assetSelect.disabled) {
                new TomSelect(assetSelect, {
                    create: false,
                    sortField: {
                        field: 'text',
                        direction: 'asc'
                    },
                    placeholder: 'Cari Asset...',
                    dropdownParent: 'body'
                });
            }
        });
    </script>
</x-prsystem::app-layout>