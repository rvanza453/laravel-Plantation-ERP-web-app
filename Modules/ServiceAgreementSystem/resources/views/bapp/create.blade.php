<x-serviceagreementsystem::layouts.master :title="'Buat BAPP Baru'">
    @push('styles')
    <style>
        .wizard-card {
            background: white; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); padding: 32px; border: 1px solid #f1f5f9;
        }
        .form-label { font-weight: 600; color: #334155; margin-bottom: 8px; display: block; font-size: 14px;}
        .form-control { width: 100%; border: 1px solid #cbd5e1; border-radius: 10px; padding: 12px 16px; font-size: 15px; transition: 0.2s; background: #fafafa;}
        .form-control:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); background: white;}
        .section-title { font-weight: 800; font-size: 18px; color: #0f172a; border-bottom: 2px solid #f1f5f9; padding-bottom: 12px; margin-bottom: 24px; display: flex; align-items: center; gap: 10px;}
        
        /* Checkbox styling */
        .uspk-card-select {
            border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; display: flex; align-items: center; gap: 16px; cursor: pointer; transition: 0.2s; background: white;
        }
        .uspk-card-select:hover { border-color: #94a3b8; background: #f8fafc; }
        .uspk-card-select.active { border-color: #3b82f6; background: #eff6ff; box-shadow: 0 4px 6px -1px rgba(59,130,246,0.1); }
        .custom-checkbox { width: 24px; height: 24px; border-radius: 6px; border: 2px solid #cbd5e1; display: flex; align-items: center; justify-content: center; transition: 0.2s; background: white;}
        .uspk-card-select.active .custom-checkbox { background: #3b82f6; border-color: #3b82f6; }
        .custom-checkbox i { color: white; display: none; font-size: 12px; }
        .uspk-card-select.active .custom-checkbox i { display: block; }
        
        .loader-shimmer {
            animation: pulse 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            background: #e2e8f0; height: 60px; border-radius: 12px; margin-bottom: 10px;
        }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }
    </style>
    @endpush

    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <a href="{{ route('sas.bapp.index') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-semibold mb-6 transition">
            <i class="fas fa-arrow-left"></i> Kembali ke Arsip
        </a>

        @if($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200">
            <div class="flex items-center gap-2 text-red-600 font-bold mb-2">
                <i class="fas fa-exclamation-circle"></i> Terdapat Kesalahan:
            </div>
            <ul class="list-disc pl-5 text-red-500 text-sm">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('sas.bapp.store') }}" method="POST" class="wizard-card">
            @csrf

            <div class="section-title">
                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-sm"><i class="fas fa-info"></i></div>
                1. Informasi Dasar BAPP
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="form-label">Nomor BAPP</label>
                    <input type="text" name="bapp_number" class="form-control" required placeholder="Contoh: BAPP-001/ENG/2026" value="{{ old('bapp_number') }}">
                </div>
                <div>
                    <label class="form-label">Tanggal BAPP</label>
                    <input type="date" name="bapp_date" class="form-control" required value="{{ old('bapp_date') }}">
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Link File Dokumen (G-Drive / OneDrive dsb)</label>
                    <input type="url" name="document_link" class="form-control" required placeholder="https://..." value="{{ old('document_link') }}">
                </div>
            </div>

            <div class="section-title mt-10">
                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm"><i class="fas fa-filter"></i></div>
                2. Filter Pekerjaan & Penarikan Data SPK
            </div>
            <p class="text-sm text-gray-500 mb-6">Pilih Jenis Pekerjaan. BAPP bisa mencakup multi blok dan multi kontraktor selama pekerjaan (job) sama.</p>

            <div class="grid grid-cols-1 gap-6 mb-8">
                <div>
                    <label class="form-label">Pekerjaan (Job Type)</label>
                    <select name="job_id" id="job_select" class="form-control" required>
                        <option value="">-- Pilih Pekerjaan --</option>
                        @foreach($jobs as $job)
                            <option value="{{ $job->id }}" {{ old('job_id') == $job->id ? 'selected' : '' }}>{{ $job->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div id="spk_container_wrapper" style="display: none;">
                <div class="section-title mt-10">
                    <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center text-sm"><i class="fas fa-check-square"></i></div>
                    3. Pilih SPK yang Masuk di BAPP Ini
                </div>
                
                <div id="loading_indicator" style="display: none;">
                    <div class="loader-shimmer"></div>
                    <div class="loader-shimmer"></div>
                </div>

                <div id="no_data_indicator" style="display: none;" class="text-center py-10 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                    <i class="fas fa-battery-empty text-4xl text-gray-400 mb-3"></i>
                    <p class="text-gray-600 font-medium">Tidak ada SPK yang memenuhi syarat.</p>
                    <p class="text-gray-400 text-sm mt-1">Pastikan SPK pada pekerjaan ini sudah selesai di lapangan, memiliki pemenang tender, dan belum masuk BAPP lain.</p>
                </div>

                <div id="spk_list" class="grid gap-3">
                    <!-- Checkboxes injected via js -->
                </div>
            </div>

            <div class="mt-10 border-t border-gray-200 pt-6 flex justify-end">
                <button type="submit" id="submitBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition transform hover:-translate-y-1 disabled:opacity-50 disabled:cursor-not-allowed" style="display: none;">
                    <i class="fas fa-save mr-2"></i> Simpan BAPP & Tandai Selesai
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const jobSelect = document.getElementById('job_select');
            const wrapper = document.getElementById('spk_container_wrapper');
            const loader = document.getElementById('loading_indicator');
            const noData = document.getElementById('no_data_indicator');
            const spkList = document.getElementById('spk_list');
            const submitBtn = document.getElementById('submitBtn');

            function fetchUspk() {
                const jobId = jobSelect.value;

                if(!jobId) {
                    wrapper.style.display = 'none';
                    submitBtn.style.display = 'none';
                    return;
                }

                wrapper.style.display = 'block';
                loader.style.display = 'block';
                noData.style.display = 'none';
                spkList.innerHTML = '';
                submitBtn.style.display = 'none';

                fetch(`{{ route('sas.api.eligible-uspks') }}?job_id=${jobId}`)
                    .then(res => res.json())
                    .then(data => {
                        loader.style.display = 'none';
                        if(data.length === 0) {
                            noData.style.display = 'block';
                        } else {
                            submitBtn.style.display = 'block';
                            data.forEach(item => {
                                const checkedAttr = ""; // keep simple implementation
                                
                                const html = `
                                    <label class="uspk-card-select" for="uspk_${item.id}">
                                        <input type="checkbox" name="uspk_ids[]" value="${item.id}" id="uspk_${item.id}" class="hidden-checkbox" style="display:none;">
                                        <div class="custom-checkbox"><i class="fas fa-check"></i></div>
                                        <div class="flex-grow">
                                            <div class="font-bold text-gray-900">${item.uspk_number}</div>
                                            <div class="text-sm text-gray-500">${item.title}</div>
                                            <div class="text-xs text-gray-500 mt-1">Kontraktor: ${item.contractor_name}</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xs text-gray-400">Selesai: ${item.completion_date}</div>
                                            <div class="font-bold text-emerald-600">${item.tender_value}</div>
                                        </div>
                                    </label>
                                `;
                                spkList.insertAdjacentHTML('beforeend', html);
                            });

                            // Add event listeners for styling
                            document.querySelectorAll('.hidden-checkbox').forEach(cb => {
                                cb.addEventListener('change', function() {
                                    if(this.checked) {
                                        this.parentElement.classList.add('active');
                                    } else {
                                        this.parentElement.classList.remove('active');
                                    }
                                });
                            });
                        }
                    })
                    .catch(e => {
                        console.error('Error fetching USPK', e);
                        loader.style.display = 'none';
                        noData.style.display = 'block';
                    });
            }

            jobSelect.addEventListener('change', fetchUspk);
            
            // if triggered by old inputs from Validation Fail
            if(jobSelect.value) {
                fetchUspk();
            }
        });
    </script>
    @endpush
</x-serviceagreementsystem::layouts.master>
