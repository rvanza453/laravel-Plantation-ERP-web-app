<x-systemispo::layouts.hr-master title="Audit ISPO Records">
    <style>
        .page-header-flex { display: flex; align-items: center; justify-content: space-between; margin-bottom: 40px; }
        .page-title-main { font-family: 'Outfit', sans-serif; font-size: 32px; font-weight: 800; color: #0f172a; letter-spacing: -0.02em; }

        .table-premium { width: 100%; border-collapse: separate; border-spacing: 0 12px; }
        .table-premium th { padding: 12px 24px; text-align: left; font-size: 11px; font-weight: 800; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.1em; border: none !important; }
        .table-premium tr td { background: #fff; padding: 24px; border-top: 1px solid #edf2f7; border-bottom: 1px solid #edf2f7; transition: var(--transition); }
        .table-premium tr td:first-child { border-left: 1px solid #edf2f7; border-top-left-radius: 16px; border-bottom-left-radius: 16px; }
        .table-premium tr td:last-child { border-right: 1px solid #edf2f7; border-top-right-radius: 16px; border-bottom-right-radius: 16px; }
        .table-premium tr:hover td { background: #f8fafc; border-color: #e2e8f0; }

        .status-pill { padding: 6px 14px; border-radius: 10px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; }
        
        .creation-card {
            background: linear-gradient(135deg, var(--hr-primary) 0%, #818cf8 100%);
            border-radius: 24px;
            padding: 32px;
            color: #fff;
            box-shadow: 0 20px 40px rgba(99, 102, 241, 0.15);
        }
        .form-input-white {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
            width: 100%;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 14px;
            transition: var(--transition);
        }
        .form-input-white:focus { background: rgba(255,255,255,0.2); border-color: #fff; outline: none; }
        .form-input-white option { color: #333; }
    </style>

    <div class="page-header-flex">
        <div>
            <h1 class="page-title-main">Kepatuhan ISPO</h1>
            <p class="text-slate-500 font-medium">Monitoring dokumen dan riwayat audit Indonesian Sustainable Palm Oil.</p>
        </div>
        <div class="flex items-center gap-3">
             <div class="relative hidden sm:block">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" placeholder="Cari dokumen..." class="premium-input !pl-11 !w-64">
             </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        {{-- List Area --}}
        <div class="lg:col-span-8">
            <div class="card-inner-body p-0">
                <table class="table-premium">
                    <thead>
                        <tr>
                            <th>Nomor Ref</th>
                            <th>Estate / Origin</th>
                            <th class="text-center">Tahun</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documents as $doc)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 border border-slate-100">
                                            <i class="fas fa-shield-alt text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-slate-800 tracking-tight">{{ $doc->document_number ?? 'DRAFT_UNASSIGNED' }}</div>
                                            <div class="text-[9px] font-extrabold text-indigo-500 uppercase tracking-widest mt-0.5">Audit Record</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="font-bold text-slate-700 text-sm">{{ $doc->site->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-bold uppercase">{{ $doc->site->code ?? 'N/A' }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="px-2.5 py-1 bg-slate-100 rounded-lg font-bold text-xs text-slate-600">{{ $doc->year }}</span>
                                </td>
                                <td>
                                    @php
                                        $status = strtolower($doc->status);
                                        $class = 'bg-slate-100 text-slate-500';
                                        if($status == 'submitted') $class = 'bg-cyan-100 text-cyan-600';
                                        if($status == 'approved' || $status == 'verified') $class = 'bg-emerald-100 text-emerald-600';
                                        if($status == 'amber') $class = 'bg-amber-100 text-amber-600';
                                    @endphp
                                    <span class="status-pill {{ $class }}">{{ $doc->status }}</span>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('ispo.show', $doc->id) }}" class="action-btn inline-flex">
                                        <i class="fas fa-pencil-alt text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-24 text-center">
                                    <div class="opacity-10 mb-6"><i class="fas fa-folder-open fa-5x"></i></div>
                                    <p class="text-slate-400 font-bold uppercase tracking-widest text-xs">Belum ada riwayat audit</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Action Area --}}
        @if(Auth::user()->hasModuleRole('ispo', ['HR Admin', 'HR ISPO Officer', 'ISPO Admin']))
        <div class="lg:col-span-4">
            <div class="creation-card">
                <div class="mb-8">
                    <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-white mb-6 border border-white/30">
                        <i class="fas fa-plus text-lg"></i>
                    </div>
                    <h2 class="text-2xl font-bold mb-2" style="font-family: 'Outfit', sans-serif;">Inisiasi Audit</h2>
                    <p class="text-sm text-indigo-50 font-medium opacity-80">Generate lembar kendali audit ISPO baru untuk estate tertentu.</p>
                </div>

                <form action="{{ route('ispo.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="text-[10px] font-bold text-indigo-100 uppercase tracking-widest block mb-2">Lokasi Estate</label>
                        <select name="site_id" class="form-input-white">
                            <option value="" disabled selected>-- Pilih Estate --</option>
                            @foreach($sites as $site)
                                <option value="{{ $site->id }}">{{ $site->name }} ({{ $site->code }})</option>
                            @endforeach
                        </select>
                        @error('site_id') <span class="text-[10px] text-red-200 mt-2 block font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-indigo-100 uppercase tracking-widest block mb-2">Tahun Audit</label>
                        <input type="number" name="year" value="{{ date('Y') }}" min="2020" max="2030" class="form-input-white font-bold">
                        @error('year') <span class="text-[10px] text-red-200 mt-2 block font-bold">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="w-full py-4 bg-white text-indigo-600 font-bold rounded-2xl shadow-xl hover:bg-slate-50 transition-all active:scale-95 flex items-center justify-center gap-3">
                        GENERATE MASTER <i class="fas fa-arrow-right text-xs"></i>
                    </button>
                </form>
            </div>

            <div class="hr-card mt-10 p-8 border-dashed border-2 border-slate-200 bg-slate-50/50">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-slate-400 border border-slate-100">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="text-[11px] font-bold text-slate-500 leading-relaxed uppercase tracking-tighter">
                        Audit Cycle mengikuti standard P&K ISPO.
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</x-systemispo::layouts.hr-master>
