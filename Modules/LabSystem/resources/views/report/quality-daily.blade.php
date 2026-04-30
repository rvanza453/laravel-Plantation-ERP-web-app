@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-50">
    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Laporan Mutu Harian</h1>
                    <p class="mt-2 text-gray-600">Progressive Disclosure: Ringkasan → Tren → Detail Mentah</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="exportToExcel()" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">
                        📋 Export Excel
                    </button>
                    <button onclick="exportToPDF()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        📄 Export PDF
                    </button>
                </div>
            </div>
        </div>

        <!-- Date Range Filter -->
        <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 mb-6 border border-white/20 shadow-lg">
            <form method="GET" action="{{ route('lab.report.quality-daily') }}" class="flex flex-col sm:flex-row gap-4 items-end">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" 
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Akhir</label>
                    <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" 
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                </div>
                <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition font-semibold">
                    🔍 Filter
                </button>
                <a href="{{ route('lab.report.quality-daily') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-semibold">
                    ↺ Reset
                </a>
            </form>
        </div>

        <!-- Tab Navigation -->
        <div class="flex gap-2 mb-6 overflow-x-auto pb-2">
            <button onclick="switchTab('summary')" class="tab-btn active px-6 py-3 bg-emerald-600 text-white rounded-lg font-semibold transition hover:bg-emerald-700" data-tab="summary">
                📊 Ringkasan (Level 1)
            </button>
            <button onclick="switchTab('analytics')" class="tab-btn px-6 py-3 bg-white/80 backdrop-blur-xl text-gray-700 rounded-lg font-semibold border border-white/20 transition hover:bg-white" data-tab="analytics">
                📈 Analitik (Level 2)
            </button>
            <button onclick="switchTab('deepdive')" class="tab-btn px-6 py-3 bg-white/80 backdrop-blur-xl text-gray-700 rounded-lg font-semibold border border-white/20 transition hover:bg-white" data-tab="deepdive">
                🔍 Detail Matriks Jam (Level 3)
            </button>
        </div>

        <!-- LEVEL 1: EXECUTIVE SUMMARY -->
        <div id="summary-tab" class="tab-content block">
            <!-- Traffic Light Scorecard -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- FFA Card -->
                <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-lg transition hover:shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex-1">
                            <p class="text-gray-600 text-sm font-medium">FFA (Free Fatty Acid)</p>
                            <p class="text-2xl font-bold text-gray-900 mt-2">
                                @if($scorecard['ffa']['value'] !== null)
                                    {{ $scorecard['ffa']['value'] }}%
                                @else
                                    N/A
                                @endif
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Target: {{ $scorecard['ffa']['target'] }}</p>
                        </div>
                        <div class="text-4xl">
                            @if($scorecard['ffa']['status'] === 'green')
                                🟢
                            @elseif($scorecard['ffa']['status'] === 'yellow')
                                🟡
                            @elseif($scorecard['ffa']['status'] === 'red')
                                🔴
                            @else
                                ⚪
                            @endif
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded px-3 py-2">
                        <p class="text-xs text-gray-600">Sampel: <strong>{{ $scorecard['ffa']['samples'] }}</strong></p>
                    </div>
                </div>

                <!-- Oil Losses Card -->
                <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-lg transition hover:shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex-1">
                            <p class="text-gray-600 text-sm font-medium">Total Kelosohan Minyak</p>
                            <p class="text-2xl font-bold text-gray-900 mt-2">
                                @if($scorecard['oil_losses']['value'] !== null)
                                    {{ $scorecard['oil_losses']['value'] }}%
                                @else
                                    N/A
                                @endif
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Target: {{ $scorecard['oil_losses']['target'] }}</p>
                        </div>
                        <div class="text-4xl">
                            @if($scorecard['oil_losses']['status'] === 'green')
                                🟢
                            @elseif($scorecard['oil_losses']['status'] === 'yellow')
                                🟡
                            @elseif($scorecard['oil_losses']['status'] === 'red')
                                🔴
                            @else
                                ⚪
                            @endif
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded px-3 py-2">
                        <p class="text-xs text-gray-600">Sampel: <strong>{{ $scorecard['oil_losses']['samples'] }}</strong></p>
                    </div>
                </div>

                <!-- Moisture Card -->
                <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-lg transition hover:shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex-1">
                            <p class="text-gray-600 text-sm font-medium">Kelembaban (Moisture)</p>
                            <p class="text-2xl font-bold text-gray-900 mt-2">
                                @if($scorecard['moisture']['value'] !== null)
                                    {{ $scorecard['moisture']['value'] }}%
                                @else
                                    N/A
                                @endif
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Target: {{ $scorecard['moisture']['target'] }}</p>
                        </div>
                        <div class="text-4xl">
                            @if($scorecard['moisture']['status'] === 'green')
                                🟢
                            @elseif($scorecard['moisture']['status'] === 'yellow')
                                🟡
                            @elseif($scorecard['moisture']['status'] === 'red')
                                🔴
                            @else
                                ⚪
                            @endif
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded px-3 py-2">
                        <p class="text-xs text-gray-600">Sampel: <strong>{{ $scorecard['moisture']['samples'] }}</strong></p>
                    </div>
                </div>
            </div>

            <!-- Verifier's Lab Head Notes (Qualitative Analysis) -->
            <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-8 border border-white/20 shadow-lg">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="text-2xl">👨‍🔬</span> Catatan Kepala Lab (Analisa Kualitatif)
                </h2>
                @if($measurements->count() > 0)
                    @php
                        $latestBatch = $samplingBatches->last();
                        $verifierNotes = $latestBatch?->verifier_notes;
                        $verificationLabel = match ($latestBatch?->status ?? 'draft') {
                                'approved' => 'Terverifikasi',
                                'pending' => 'Menunggu Verifikasi',
                                'in_analysis' => 'Sedang Dianalisis',
                                default => 'Belum Diverifikasi',
                            };
                        $verificationBadge = match ($latestBatch?->status ?? 'draft') {
                                'approved' => 'bg-emerald-100 text-emerald-700',
                                'pending' => 'bg-amber-100 text-amber-800',
                                'in_analysis' => 'bg-blue-100 text-blue-700',
                                default => 'bg-slate-100 text-slate-700',
                            };
                    @endphp
                            <div class="bg-slate-50 border border-slate-200 p-6 rounded-xl">
                                <div class="mb-4 flex flex-wrap items-center gap-2">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $verificationBadge }}">{{ $verificationLabel }}</span>
                                    <span class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 border border-slate-200">{{ $latestBatch?->batch_code ?? 'N/A' }}</span>
                                </div>
                                @if($verifierNotes)
                                    <p class="text-gray-800 leading-relaxed whitespace-pre-wrap">{{ $verifierNotes }}</p>
                                    <div class="mt-4 pt-4 border-t border-slate-200 flex flex-col gap-2 text-xs text-gray-600 sm:flex-row sm:justify-between">
                                        <span><strong>Diverifikasi oleh:</strong> {{ $latestBatch?->verifier_name ?? 'N/A' }}</span>
                                        <span><strong>Tanggal:</strong> {{ $latestBatch?->created_at ? \Illuminate\Support\Carbon::parse($latestBatch->created_at)->format('d/m/Y H:i') : 'N/A' }}</span>
                                    </div>
                                @else
                                    <div class="bg-amber-50 border border-amber-200 p-4 rounded-lg text-amber-900 text-sm font-medium">
                                        Data sudah tersimpan dan tampil, tetapi belum ada analisa/verifikasi dari asisten lab.
                                    </div>
                                @endif
                            </div>
                @else
                    <div class="bg-gray-100 border border-gray-300 p-6 rounded text-gray-600 italic">
                            Tidak ada data yang tersimpan dalam periode ini.
                    </div>
                @endif
            </div>

            <!-- Summary Statistics -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
                <div class="bg-white/80 backdrop-blur-xl rounded-xl p-4 border border-white/20 text-center">
                    <p class="text-2xl font-bold text-emerald-600">{{ $samplingBatches->count() }}</p>
                    <p class="text-xs text-gray-600 mt-1">Batch Tersimpan</p>
                </div>
                <div class="bg-white/80 backdrop-blur-xl rounded-xl p-4 border border-white/20 text-center">
                    <p class="text-2xl font-bold text-blue-600">{{ $measurements->count() }}</p>
                    <p class="text-xs text-gray-600 mt-1">Total Pengukuran</p>
                </div>
                <div class="bg-white/80 backdrop-blur-xl rounded-xl p-4 border border-white/20 text-center">
                    <p class="text-2xl font-bold text-amber-600">
                        @php
                            $abnormalCount = 0;
                            foreach($rawTableData as $categoryData) {
                                foreach($categoryData['rows'] as $row) {
                                    if($row['is_abnormal']) $abnormalCount++;
                                }
                            }
                            echo $abnormalCount;
                        @endphp
                    </p>
                    <p class="text-xs text-gray-600 mt-1">Anomali Terdeteksi</p>
                </div>
                <div class="bg-white/80 backdrop-blur-xl rounded-xl p-4 border border-white/20 text-center">
                    <p class="text-2xl font-bold text-purple-600">{{ $verifiedBatches->pluck('shift')->unique()->count() }}</p>
                    <p class="text-xs text-gray-600 mt-1">Shift Terlibat</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div class="bg-white/80 backdrop-blur-xl rounded-xl p-4 border border-white/20 text-center">
                    <p class="text-2xl font-bold text-indigo-600">{{ $verifiedBatches->count() }}</p>
                    <p class="text-xs text-gray-600 mt-1">Sudah Diverifikasi</p>
                </div>
                <div class="bg-white/80 backdrop-blur-xl rounded-xl p-4 border border-white/20 text-center">
                    <p class="text-2xl font-bold text-amber-600">{{ $pendingBatches->count() }}</p>
                    <p class="text-xs text-gray-600 mt-1">Belum Diverifikasi</p>
                </div>
                <div class="bg-white/80 backdrop-blur-xl rounded-xl p-4 border border-white/20 text-center">
                    <p class="text-2xl font-bold text-slate-600">{{ $samplingBatches->where('status', 'draft')->count() }}</p>
                    <p class="text-xs text-gray-600 mt-1">Masih Draft</p>
                </div>
            </div>
        </div>

        <!-- LEVEL 2: ANALYTICS WITH CHARTS -->
        <div id="analytics-tab" class="tab-content hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Trend Line Chart -->
                <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-lg">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">📈 Tren Waktu (Garis Horison = Standar)</h3>
                    <div id="trendChart" class="w-full h-80"></div>
                </div>

                <!-- Shift Comparison Bar Chart -->
                <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-lg">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">⚡ Perbandingan Shift (Total Kelosohan)</h3>
                    <div id="shiftChart" class="w-full h-80"></div>
                </div>
            </div>

            <!-- Chart Legend & Notes -->
            <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-lg">
                <h3 class="text-lg font-bold text-gray-900 mb-4">📋 Penjelasan Grafik</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="border-l-4 border-emerald-500 pl-4">
                        <p class="font-semibold text-gray-900">Garis Hijau</p>
                        <p class="text-sm text-gray-600">Nilai pengukuran seketika per jam</p>
                    </div>
                    <div class="border-l-4 border-red-500 pl-4">
                        <p class="font-semibold text-gray-900">Garis Merah Statis</p>
                        <p class="text-sm text-gray-600">Batas standar maksimum yang diizinkan</p>
                    </div>
                    <div class="border-l-4 border-amber-500 pl-4">
                        <p class="font-semibold text-gray-900">Area Kuning</p>
                        <p class="text-sm text-gray-600">Zona Peringatan (mendekati batas)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- LEVEL 3: DEEP DIVE TABLE -->
        <div id="deepdive-tab" class="tab-content hidden">
            <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-lg">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    🔬 Detail Jam Sampling (slot kosong tetap ditampilkan)
                </h2>

                @if($level3Matrix->count() > 0)
                    @foreach($level3Matrix as $dayMatrix)
                        <div class="mb-8 rounded-2xl border border-slate-200 bg-slate-50/70 p-4 sm:p-5">
                            <div class="mb-4 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <h3 class="text-base sm:text-lg font-black text-gray-900">Tanggal {{ \Illuminate\Support\Carbon::parse($dayMatrix['date'])->translatedFormat('d M Y') }}</h3>
                                    <p class="text-xs sm:text-sm text-gray-600">Jam yang tidak ada sample akan tampil sebagai sel kosong.</p>
                                </div>
                                <div class="text-xs font-bold text-slate-500">Slot jam: {{ count($dayMatrix['hours']) }}</div>
                            </div>

                            @foreach($dayMatrix['categories'] as $categoryData)
                                <div class="mb-6 last:mb-0">
                                    <button onclick="toggleCategory('category-{{ $loop->parent->index }}-{{ $loop->index }}')"
                                        class="w-full bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-bold py-3 px-4 rounded-lg flex items-center justify-between hover:shadow-lg transition">
                                        <span class="text-base sm:text-lg">📦 {{ $categoryData['category'] }}</span>
                                        <span class="text-2xl" id="toggle-icon-{{ $loop->parent->index }}-{{ $loop->index }}">▼</span>
                                    </button>

                                    <div id="category-{{ $loop->parent->index }}-{{ $loop->index }}" class="category-content hidden mt-2 border border-gray-200 rounded-lg overflow-hidden">
                                        <div class="overflow-x-auto">
                                            <table class="min-w-max w-full text-[11px] sm:text-xs">
                                                <thead class="bg-gray-100 border-b border-gray-300">
                                                    <tr>
                                                        <th class="sticky left-0 z-10 bg-gray-100 px-3 py-3 text-left font-semibold text-gray-700 min-w-[44px]">NO</th>
                                                        <th class="sticky left-[44px] z-10 bg-gray-100 px-4 py-3 text-left font-semibold text-gray-700 min-w-[250px]">KETERANGAN</th>
                                                        <th class="sticky left-[294px] z-10 bg-gray-100 px-4 py-3 text-left font-semibold text-gray-700 min-w-[70px]">STN</th>
                                                        <th class="sticky left-[364px] z-10 bg-gray-100 px-4 py-3 text-left font-semibold text-gray-700 min-w-[90px]">STD</th>
                                                        @foreach($dayMatrix['hours'] as $hour)
                                                            <th class="px-3 py-3 text-center font-semibold text-gray-700 min-w-[74px]">{{ $hour }}</th>
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($categoryData['rows'] as $row)
                                                        <tr class="border-b border-gray-100 hover:bg-gray-50 bg-white">
                                                            <td class="sticky left-0 z-10 bg-inherit px-3 py-2 font-bold text-gray-900">{{ $loop->iteration }}</td>
                                                            <td class="sticky left-[44px] z-10 bg-inherit px-4 py-2 font-semibold text-gray-900">
                                                                <div>{{ $row['parameter_name'] }}</div>
                                                                <div class="text-[10px] text-gray-500 font-medium">{{ $row['sampling_frequency'] ?? '-' }}</div>
                                                            </td>
                                                            <td class="sticky left-[294px] z-10 bg-inherit px-4 py-2 text-gray-600">{{ $row['unit'] ?: '-' }}</td>
                                                            <td class="sticky left-[364px] z-10 bg-inherit px-4 py-2 text-gray-600">{{ $row['standard_text'] ?? '-' }}</td>
                                                            @foreach($row['cells'] as $cell)
                                                                <td class="px-2 py-2 text-center align-middle">
                                                                    @if($cell)
                                                                        <div class="rounded-lg border px-2 py-1 {{ $cell['is_abnormal'] ? 'border-rose-200 bg-rose-50' : 'border-emerald-200 bg-emerald-50' }}">
                                                                            <div class="text-[11px] font-black {{ $cell['is_abnormal'] ? 'text-rose-700' : 'text-gray-900' }}">
                                                                                {{ $cell['measured_value'] ?? '-' }}
                                                                            </div>
                                                                            <div class="text-[10px] text-gray-500">{{ $cell['measured_at'] }}</div>
                                                                            <div class="mt-1 inline-flex rounded-full px-2 py-0.5 text-[9px] font-bold {{ $cell['verification_badge'] }}">
                                                                                {{ $cell['verification_label'] }}
                                                                            </div>
                                                                        </div>
                                                                    @else
                                                                        <div class="flex min-h-[58px] items-center justify-center rounded-lg border border-dashed border-gray-200 bg-white text-gray-300 text-xs font-bold">-</div>
                                                                    @endif
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @else
                    <div class="bg-gray-100 border border-gray-300 p-6 rounded text-gray-600 italic">
                        Tidak ada data tersedia untuk periode yang dipilih.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- ApexCharts Library -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    function switchTab(tabName) {
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(tab => tab.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('bg-emerald-600', 'text-white');
            btn.classList.add('bg-white/80', 'text-gray-700', 'border', 'border-white/20');
        });

        // Show selected tab
        document.getElementById(tabName + '-tab').classList.remove('hidden');
        event.target.classList.remove('bg-white/80', 'text-gray-700', 'border', 'border-white/20');
        event.target.classList.add('bg-emerald-600', 'text-white');

        // Initialize charts if switching to analytics tab
        if (tabName === 'analytics') {
            setTimeout(() => {
                initTrendChart();
                initShiftChart();
            }, 100);
        }
    }

    function toggleCategory(categoryId) {
        const content = document.getElementById(categoryId);
        const icon = document.getElementById('toggle-icon-' + categoryId.split('-')[1]);
        
        content.classList.toggle('hidden');
        icon.textContent = content.classList.contains('hidden') ? '▼' : '▲';
    }

    function initTrendChart() {
        const trendData = @json($trendData);
        
        if (!trendData || trendData.length === 0) {
            document.getElementById('trendChart').innerHTML = '<p class="text-gray-600 p-4">Tidak ada data tren tersedia.</p>';
            return;
        }

        const options = {
            chart: { type: 'line', height: 320, toolbar: { show: true } },
            series: [
                {
                    name: 'FFA (%)',
                    data: trendData.map(d => d.ffa),
                    color: '#3b82f6'
                },
                {
                    name: 'Kelosohan Minyak (%)',
                    data: trendData.map(d => d.losses),
                    color: '#f59e0b'
                },
                {
                    name: 'Kelembaban (%)',
                    data: trendData.map(d => d.moisture),
                    color: '#10b981'
                }
            ],
            xaxis: {
                categories: trendData.map(d => d.hour),
                title: { text: 'Waktu (Jam)' }
            },
            yaxis: {
                title: { text: 'Nilai (%)' }
            },
            stroke: { width: 2 },
            tooltip: { shared: true }
        };

        new ApexCharts(document.getElementById('trendChart'), options).render();
    }

    function initShiftChart() {
        const shiftData = @json($shiftComparison);
        
        if (!shiftData || shiftData.length === 0) {
            document.getElementById('shiftChart').innerHTML = '<p class="text-gray-600 p-4">Tidak ada data shift tersedia.</p>';
            return;
        }

        const options = {
            chart: { type: 'bar', height: 320, toolbar: { show: true } },
            series: [
                {
                    name: 'Total Kelosohan Minyak (%)',
                    data: shiftData.map(d => d.oil_losses)
                }
            ],
            xaxis: {
                categories: shiftData.map(d => d.shift)
            },
            yaxis: {
                title: { text: 'Persentase (%)' }
            },
            colors: ['#38a169'],
            tooltip: { y: { formatter: val => val.toFixed(2) + '%' } }
        };

        new ApexCharts(document.getElementById('shiftChart'), options).render();
    }

    function exportToExcel() {
        alert('Fitur Export Excel akan diimplementasikan dengan library SheetJS atau phpspreadsheet');
        // TODO: Implement Excel export
    }

    function exportToPDF() {
        alert('Fitur Export PDF akan diimplementasikan dengan dompdf atau mPDF');
        // TODO: Implement PDF export
    }
</script>
@endsection
