<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verifier Dashboard - Lab Sampling</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#E6F0F9] font-sans antialiased selection:bg-[#4FA5F5] selection:text-white">
    <div class="fixed top-0 left-0 h-80 w-80 rounded-full bg-[#D4E8F9] opacity-70 blur-3xl pointer-events-none"></div>
    <div class="fixed right-0 bottom-0 h-80 w-80 rounded-full bg-[#D4E8F9] opacity-70 blur-3xl pointer-events-none"></div>

    <div class="relative z-10 mx-auto max-w-[1500px] px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-4 rounded-[28px] border border-white bg-white/75 p-5 shadow-[0_14px_50px_rgba(0,0,0,0.06)] backdrop-blur-xl sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-gray-900">Verifier Dashboard</h1>
                <p class="mt-1 text-sm font-semibold text-gray-500">Maker-Checker Approval dengan Analisa Kualitatif untuk publikasi data manajemen.</p>
            </div>
            <a href="{{ route('lab.dashboard') }}" class="inline-flex items-center justify-center rounded-full bg-white px-5 py-2.5 text-sm font-extrabold text-gray-700 shadow-sm transition hover:bg-gray-50">
                Kembali ke Dashboard
            </a>
        </div>

        @if(session('success'))
            <div class="mb-5 rounded-[20px] border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-5 rounded-[20px] border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
                <p class="mb-1 font-extrabold">Periksa input berikut:</p>
                <ul class="list-disc space-y-1 pl-5 font-medium">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $pendingReports = collect($pendingReports ?? []);
        @endphp

        @if($pendingReports->isEmpty())
            <div class="rounded-[28px] border border-white bg-white/80 p-10 text-center shadow-[0_10px_40px_rgba(0,0,0,0.05)] backdrop-blur-xl">
                <p class="text-lg font-black text-gray-900">Tidak ada laporan pending verifikasi</p>
                <p class="mt-2 text-sm font-semibold text-gray-500">Semua laporan sudah ditinjau atau belum ada End Shift yang mengirim data.</p>
            </div>
        @endif

        <div class="space-y-6">
            @foreach($pendingReports as $report)
                <div class="rounded-[30px] border border-white bg-white/85 p-5 shadow-[0_18px_60px_rgba(0,0,0,0.07)] backdrop-blur-xl sm:p-7">
                    <div class="mb-5 flex flex-col gap-3 border-b border-gray-100 pb-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-xl font-black text-gray-900">Laporan {{ $report->batch_code }}</h2>
                            <p class="mt-1 text-xs font-bold uppercase tracking-wider text-gray-500">
                                {{ \Carbon\Carbon::parse($report->sampling_date)->format('d M Y') }} • Shift {{ $report->shift }} • Operator: {{ $report->sampler_name ?? '-' }}
                            </p>
                            <p class="mt-1 text-sm font-semibold text-gray-600">Sumber: {{ $report->source_unit ?: '-' }}</p>
                        </div>
                        <div class="flex gap-2 text-xs font-black">
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-700">{{ $report->rows_count }} Parameter</span>
                            <span class="rounded-full {{ $report->abnormal_count > 0 ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-700' }} px-3 py-1">
                                {{ $report->abnormal_count }} Abnormal
                            </span>
                            <span class="rounded-full bg-blue-100 px-3 py-1 text-blue-700">Pending</span>
                        </div>
                    </div>

                    @if(!empty($report->batch_notes))
                        <div class="mb-5 rounded-[18px] border border-blue-100 bg-blue-50/70 px-4 py-3 text-sm text-blue-800">
                            <span class="font-extrabold">Catatan Operator:</span> {{ $report->batch_notes }}
                        </div>
                    @endif

                    <div class="space-y-5">
                        @foreach($report->grouped_rows as $category => $rows)
                            <div class="overflow-hidden rounded-[20px] border border-gray-100">
                                <div class="bg-slate-50 px-4 py-3 text-sm font-extrabold text-gray-800">{{ $category }}</div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-sm">
                                        <thead class="bg-white text-xs font-black uppercase tracking-wider text-gray-500">
                                            <tr>
                                                <th class="px-4 py-3 text-left">Parameter</th>
                                                <th class="px-4 py-3 text-left">STD</th>
                                                <th class="px-4 py-3 text-left">Hasil</th>
                                                <th class="px-4 py-3 text-left">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($rows as $row)
                                                <tr class="border-t border-gray-100 {{ $row->is_abnormal ? 'bg-rose-50/80' : 'bg-white' }}">
                                                    <td class="px-4 py-3 font-bold text-gray-900">{{ $row->parameter_name }}</td>
                                                    <td class="px-4 py-3 font-semibold text-gray-700">{{ $row->standard_text ?: '-' }}</td>
                                                    <td class="px-4 py-3 font-black {{ $row->is_abnormal ? 'text-rose-700' : 'text-gray-900' }}">
                                                        {{ $row->measured_text ?? $row->measured_value ?? '-' }} {{ $row->unit ?: '' }}
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-extrabold {{ $row->is_abnormal ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-700' }}">
                                                            {{ $row->standard_status_text }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        <form method="POST" action="{{ route('lab.sampling.approve', $report->batch_id) }}" class="space-y-4" id="approve-form-{{ $report->batch_id }}">
                            @csrf
                            <label class="mb-2 block text-sm font-black text-gray-800">Hasil Analisa & To-Do (Rekomendasi Tindakan)</label>
                            <textarea
                                name="verifier_notes"
                                id="verifier-notes-{{ $report->batch_id }}"
                                rows="5"
                                required
                                class="w-full rounded-[22px] border-none bg-[#F3F6F9] px-5 py-4 text-sm font-semibold text-gray-900 shadow-[inset_0_2px_4px_rgba(0,0,0,0.03)] focus:outline-none focus:ring-4 focus:ring-[#4FA5F5]/20"
                                placeholder="Tuliskan hasil analisa mutu hari ini dan apa yang harus dilakukan (To-Do). Contoh: Mutu FFA stabil, namun Dirt sedikit tinggi. To-do: Lakukan pengecekan filter saringan besok pagi..."
                            >{{ old('verifier_notes') }}</textarea>

                            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                                <button type="button" onclick="openRejectModal({{ $report->batch_id }})" class="inline-flex items-center justify-center rounded-full bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-3 text-sm font-extrabold text-white shadow-lg shadow-orange-500/30 transition hover:brightness-110">
                                    Reject / Need Revision
                                </button>
                                <button type="submit" class="inline-flex items-center justify-center rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 px-6 py-3 text-sm font-extrabold text-white shadow-lg shadow-emerald-500/30 transition hover:brightness-110">
                                    Approve & Publish
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div id="rejectModal" class="pointer-events-none fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 opacity-0 transition-all duration-200">
        <div class="w-full max-w-lg rounded-[28px] border border-white bg-white/95 p-6 shadow-2xl backdrop-blur-xl">
            <h3 class="text-xl font-black text-gray-900">Reject / Need Revision</h3>
            <p class="mt-1 text-sm font-semibold text-gray-500">Jelaskan alasan penolakan agar operator dapat memperbaiki data.</p>

            <form method="POST" id="rejectForm" class="mt-4 space-y-4">
                @csrf
                <input type="hidden" name="verifier_notes" id="rejectVerifierNotes">
                <div>
                    <label class="mb-2 block text-sm font-black text-gray-800">Alasan Revisi</label>
                    <textarea name="reject_reason" rows="4" required class="w-full rounded-[18px] border-none bg-[#F3F6F9] px-4 py-3 text-sm font-semibold text-gray-900 focus:outline-none focus:ring-4 focus:ring-rose-400/20" placeholder="Contoh: Nilai Moisture Produksi tidak sesuai tren, mohon validasi ulang sampel dan input ulang hasil dengan bukti pengukuran."></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeRejectModal()" class="rounded-full bg-slate-100 px-5 py-2.5 text-sm font-extrabold text-slate-700">Batal</button>
                    <button type="submit" class="rounded-full bg-gradient-to-r from-rose-500 to-orange-500 px-5 py-2.5 text-sm font-extrabold text-white">Kirim Revisi</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRejectModal(batchId) {
            const modal = document.getElementById('rejectModal');
            const rejectForm = document.getElementById('rejectForm');
            const rejectVerifierNotes = document.getElementById('rejectVerifierNotes');
            const notesField = document.getElementById(`verifier-notes-${batchId}`);

            if (!modal || !rejectForm) return;

            rejectForm.action = `{{ url('lab/sampling') }}/${batchId}/reject`;
            if (rejectVerifierNotes && notesField) {
                rejectVerifierNotes.value = notesField.value;
            }

            modal.classList.remove('opacity-0', 'pointer-events-none');
        }

        function closeRejectModal() {
            const modal = document.getElementById('rejectModal');
            if (!modal) return;

            modal.classList.add('opacity-0', 'pointer-events-none');
        }
    </script>
</body>
</html>
