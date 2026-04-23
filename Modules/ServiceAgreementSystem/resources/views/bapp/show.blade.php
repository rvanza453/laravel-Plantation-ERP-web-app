<x-serviceagreementsystem::layouts.master :title="'Detail BAPP'">
    @push('styles')
    <style>
        .detail-card {
            background: white; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; overflow: hidden;
            margin-bottom: 24px;
        }
        .header-gradient {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9); padding: 32px; border-bottom: 1px solid #e2e8f0;
        }
        .dt-label { font-size: 13px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .dt-value { font-size: 16px; color: #0f172a; font-weight: 700; }
        
        .spk-item {
            padding: 20px 32px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between;
            transition: 0.2s;
        }
        .spk-item:hover { background: #f8fafc; }
        .spk-item:last-child { border-bottom: none; }
    </style>
    @endpush

    <div class="max-w-5xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <a href="{{ route('sas.bapp.index') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-semibold transition">
                <i class="fas fa-arrow-left"></i> Kembali ke Arsip
            </a>
            
            <a href="{{ $bapp->document_link }}" target="_blank" class="bg-red-50 text-red-600 border border-red-200 font-bold py-2 px-4 rounded-lg shadow hover:bg-red-100 transition inline-flex items-center gap-2">
                <i class="fas fa-file-pdf"></i> Lihat File BAPP Fisik
            </a>
        </div>

        <div class="detail-card">
            <div class="header-gradient">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                    <div>
                        <div class="dt-label">No. BAPP</div>
                        <div class="dt-value text-blue-600">{{ $bapp->bapp_number }}</div>
                    </div>
                    <div>
                        <div class="dt-label">Tanggal</div>
                        <div class="dt-value">{{ $bapp->bapp_date->format('d F Y') }}</div>
                    </div>
                    <div>
                        <div class="dt-label">Pekerjaan / Job</div>
                        <div class="dt-value">{{ $bapp->job->name ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="dt-label">Kontraktor Pelaksana</div>
                        <div class="dt-value">{{ $bapp->contractor->name ?? 'Multi Kontraktor' }}</div>
                    </div>
                </div>
                <div class="mt-6 text-sm text-gray-500">
                    <i class="fas fa-user-clock mr-1"></i> Diarsipkan oleh {{ $bapp->uploader->name ?? '-' }} pada {{ $bapp->created_at->format('d M Y, H:i') }}
                </div>
            </div>

            <div class="p-0">
                <div class="px-8 py-4 bg-gray-50 border-b border-gray-200 font-bold text-gray-700 flex items-center gap-2">
                    <i class="fas fa-layer-group text-blue-500"></i> SPK yang Termasuk dalam Pembayaran Ini
                </div>
                
                <div class="flex flex-col">
                    @forelse($bapp->submissions as $uspk)
                    <div class="spk-item">
                        <div>
                            <div class="flex items-center gap-3 mb-1">
                                <a href="{{ route('sas.uspk.show', $uspk->id) }}" class="font-bold text-lg text-gray-900 hover:text-blue-600 transition">{{ $uspk->uspk_number }}</a>
                                <span class="bg-emerald-100 text-emerald-700 text-xs px-2 py-1 rounded-full font-bold">SELESAI</span>
                            </div>
                            <div class="text-sm text-gray-500">{{ $uspk->title }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-semibold text-gray-700"><i class="fas fa-building text-gray-400 mr-1"></i> {{ $uspk->department->name ?? '-' }}</div>
                            <div class="text-xs text-gray-500 mt-1">Estimasi Target: {{ $uspk->estimated_duration }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-gray-500">
                        Tidak ada detail SPK yang ditemukan.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-serviceagreementsystem::layouts.master>
