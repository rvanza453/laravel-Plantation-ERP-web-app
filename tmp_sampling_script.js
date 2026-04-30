
        // Logika Pindah Tab
        function switchTab(tabId) {
            try {
                const tabSatuan = document.getElementById('tab-satuan');
                const tabBatch = document.getElementById('tab-batch');
                const btnSatuan = document.getElementById('btn-satuan');
                const btnBatch = document.getElementById('btn-batch');
                const actionBatch = document.getElementById('action-batch');

                if (!tabSatuan || !tabBatch || !btnSatuan || !btnBatch || !actionBatch) {
                    console.warn('Some tabs or buttons not found');
                    return;
                }

                if (tabId === 'satuan') {
                    tabSatuan.classList.remove('hidden');
                    tabBatch.classList.add('hidden');
                    actionBatch.classList.add('hidden');
                    btnSatuan.className = "flex-1 lg:flex-none px-4 sm:px-6 py-2 sm:py-2.5 rounded-full text-xs sm:text-sm font-extrabold transition-all bg-[#4FA5F5] text-white shadow-md";
                    btnBatch.className = "flex-1 lg:flex-none px-4 sm:px-6 py-2 sm:py-2.5 rounded-full text-xs sm:text-sm font-bold transition-all text-gray-500 hover:text-gray-900 hover:bg-gray-50";
                } else {
                    tabBatch.classList.remove('hidden');
                    tabSatuan.classList.add('hidden');
                    actionBatch.classList.remove('hidden');
                    btnBatch.className = "flex-1 lg:flex-none px-4 sm:px-6 py-2 sm:py-2.5 rounded-full text-xs sm:text-sm font-extrabold transition-all bg-[#4FA5F5] text-white shadow-md";
                    btnSatuan.className = "flex-1 lg:flex-none px-4 sm:px-6 py-2 sm:py-2.5 rounded-full text-xs sm:text-sm font-bold transition-all text-gray-500 hover:text-gray-900 hover:bg-gray-50";
                }
            } catch (err) {
                console.error('Error switching tabs:', err);
            }
        }

        // Logika Pop-Up Form (Mobile Bottom Sheet)
        function toggleMobileForm(show) {
            const backdrop = document.getElementById('mobile-backdrop');
            const formContainer = document.getElementById('form-container');
            
            if (show) {
                backdrop.classList.remove('pointer-events-none');
                backdrop.classList.remove('opacity-0');
                formContainer.classList.remove('translate-y-full');
                document.body.style.overflow = 'hidden';
            } else {
                backdrop.classList.add('opacity-0');
                backdrop.classList.add('pointer-events-none');
                formContainer.classList.add('translate-y-full');
                document.body.style.overflow = '';
            }
        }

        // FUNGSI UMUM UNTUK PARSING ANGKA
        function parseSamplingNumber(value) {
            const normalized = String(value ?? '').replace(',', '.').trim();
            if (normalized === '') return null;
            const number = Number(normalized);
            return Number.isFinite(number) ? number : null;
        }

        function formatSamplingNumber(value, decimals = 4) {
            if (value === null || value === undefined || Number.isNaN(value) || !Number.isFinite(value)) return '';
            return Number(value).toFixed(decimals).replace(/\.0+$/, '').replace(/(\.[0-9]*?)0+$/, '$1');
        }

        // FUNGSI UNTUK MODE BATCH (Bawaan Anda sebelumnya)
        function calculateSamplingCards() {
            // Kalkulasi Formula FFA & Dirt di Tab Batch
            document.querySelectorAll('#tab-batch [data-parameter-card]').forEach((card) => {
                const resultInput = card.querySelector('[data-formula-result]');
                const isFfa = !!card.querySelector('[data-ffa-a]');
                const isDirt = !!card.querySelector('[data-dirt-l]');

                if (isFfa && resultInput) {
                    const sampleWeight = parseSamplingNumber(card.querySelector('[data-ffa-a]')?.value);
                    const titrationVolume = parseSamplingNumber(card.querySelector('[data-ffa-b]')?.value);
                    const normality = parseSamplingNumber(card.querySelector('[data-ffa-c]')?.value);

                    let finalValue = null;
                    if (sampleWeight && sampleWeight !== 0 && titrationVolume !== null && normality !== null) {
                        finalValue = (titrationVolume * normality * 25.6) / sampleWeight;
                    }
                    resultInput.value = formatSamplingNumber(finalValue, 4);
                }

                if (isDirt && resultInput) {
                    const containerWeight = parseSamplingNumber(card.querySelector('[data-dirt-l]')?.value);
                    const withSampleWeight = parseSamplingNumber(card.querySelector('[data-dirt-m]')?.value);
                    const crucibleEmpty = parseSamplingNumber(card.querySelector('[data-dirt-o]')?.value);
                    const crucibleWithDirt = parseSamplingNumber(card.querySelector('[data-dirt-p]')?.value);
                    const sampleOutput = card.querySelector('[data-dirt-n]');
                    const dirtOutput = card.querySelector('[data-dirt-q]');

                    const sampleWeight = (withSampleWeight ?? 0) - (containerWeight ?? 0);
                    const dirtWeight = (crucibleWithDirt ?? 0) - (crucibleEmpty ?? 0);

                    if (sampleOutput) sampleOutput.textContent = formatSamplingNumber(sampleWeight, 4) || '0.00';
                    if (dirtOutput) dirtOutput.textContent = formatSamplingNumber(dirtWeight, 4) || '0.00';

                    let finalValue = null;
                    if (sampleWeight !== 0 && withSampleWeight !== null && containerWeight !== null && crucibleEmpty !== null && crucibleWithDirt !== null) {
                        finalValue = (dirtWeight / sampleWeight) * 100;
                    }
                    resultInput.value = formatSamplingNumber(finalValue, 4);
                }
            });

            // Auto-Sum di Tab Batch
            document.querySelectorAll('#tab-batch [data-total-result]').forEach((totalInput) => {
                const categoryKey = totalInput.dataset.totalCategory || '';
                const categoryCards = document.querySelectorAll(`#tab-batch [data-parameter-card][data-category-key="${categoryKey}"]`);

                let total = 0;
                categoryCards.forEach((card) => {
                    if (card.dataset.isTotal === '1') return;
                    const input = card.querySelector('input[name^="measurements["]');
                    if (!input) return;
                    
                    const numericValue = parseSamplingNumber(input.value);
                    if (numericValue !== null) total += numericValue;
                });
                totalInput.value = formatSamplingNumber(total, 4);
            });
        }

        // ==========================================
        // FUNGSI BARU: DINAMIS UNTUK MODE SATUAN
        // ==========================================
        function handleSatuanSelectChange() {
            const select = document.getElementById('satuan-parameter-select');
            const selectedOption = select.options[select.selectedIndex];
            const category = selectedOption ? selectedOption.getAttribute('data-category-key') : '';

            // Ambil container input
            const genericContainer = document.getElementById('satuan-input-generic');
            const ffaContainer = document.getElementById('satuan-input-ffa');
            const dirtContainer = document.getElementById('satuan-input-dirt');

            // Fungsi untuk enable/disable input agar data tidak bocor saat disubmit
            const toggleContainer = (container, isVisible) => {
                if (isVisible) {
                    container.classList.remove('hidden');
                    container.classList.add('block');
                    // Enable semua input di dalam form ini yang tadinya di disable
                    container.querySelectorAll('input').forEach(input => {
                        // Jangan hilangkan attribute readonly untuk input hasil akhir
                        if (!input.hasAttribute('readonly')) input.disabled = false;
                    });
                } else {
                    container.classList.remove('block');
                    container.classList.add('hidden');
                    // Disable semua input yang disembunyikan agar tidak ikut disubmit
                    container.querySelectorAll('input').forEach(input => {
                        input.disabled = true;
                    });
                }
            };

            // Reset UI
            toggleContainer(genericContainer, false);
            toggleContainer(ffaContainer, false);
            toggleContainer(dirtContainer, false);

            if (category === 'ffa') {
                toggleContainer(ffaContainer, true);
                calculateSatuanFfa(); // Kalkulasi ulang jika user edit select dropdown
            } else if (category === 'dirt') {
                toggleContainer(dirtContainer, true);
                calculateSatuanDirt();
            } else {
                toggleContainer(genericContainer, true);
            }
        }

        // Kalkulasi khusus untuk Form Mode Satuan - FFA
        function calculateSatuanFfa() {
            const a = parseSamplingNumber(document.getElementById('satuan_ffa_a').value);
            const b = parseSamplingNumber(document.getElementById('satuan_ffa_b').value);
            const c = parseSamplingNumber(document.getElementById('satuan_ffa_c').value);
            const result = document.getElementById('satuan_ffa_result');

            if (a && a !== 0 && b !== null && c !== null) {
                result.value = formatSamplingNumber((b * c * 25.6) / a, 4);
            } else {
                result.value = '';
            }
        }

        // Kalkulasi khusus untuk Form Mode Satuan - Dirt
        function calculateSatuanDirt() {
            const l = parseSamplingNumber(document.getElementById('satuan_dirt_l').value);
            const m = parseSamplingNumber(document.getElementById('satuan_dirt_m').value);
            const o = parseSamplingNumber(document.getElementById('satuan_dirt_o').value);
            const p = parseSamplingNumber(document.getElementById('satuan_dirt_p').value);
            
            const nDisplay = document.getElementById('satuan_dirt_n');
            const qDisplay = document.getElementById('satuan_dirt_q');
            const result = document.getElementById('satuan_dirt_result');

            const sampleWeight = (m ?? 0) - (l ?? 0);
            const dirtWeight = (p ?? 0) - (o ?? 0);

            nDisplay.textContent = formatSamplingNumber(sampleWeight, 4) || '0.00';
            qDisplay.textContent = formatSamplingNumber(dirtWeight, 4) || '0.00';

            if (sampleWeight !== 0 && l !== null && m !== null && o !== null && p !== null) {
                result.value = formatSamplingNumber((dirtWeight / sampleWeight) * 100, 4);
            } else {
                result.value = '';
            }
        }

        // Misi / Countdown Timer
        function formatCountdown(ms) {
            const totalSeconds = Math.max(0, Math.floor(ms / 1000));
            const hours = Math.floor(totalSeconds / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;
            return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }

        function initializeMissionCountdowns() {
            const countdownNodes = Array.from(document.querySelectorAll('[data-mission-reset-countdown]'));
            if (countdownNodes.length === 0) return;

            const resetTimes = [];
            const updateCountdowns = () => {
                const now = Date.now();
                countdownNodes.forEach((node) => {
                    const resetAt = new Date(node.dataset.resetAt || '');
                    if (Number.isNaN(resetAt.getTime())) return;

                    const remaining = resetAt.getTime() - now;
                    resetTimes.push(resetAt.getTime());

                    const prefix = node.dataset.missionPrefix || 'Tersedia';
                    if (remaining <= 0) {
                        node.textContent = `${prefix} • reset sekarang`;
                    } else {
                        node.textContent = `${prefix} • reset ${formatCountdown(remaining)}`;
                    }
                });
            };

            updateCountdowns();

            const refreshAt = countdownNodes
                .map((node) => new Date(node.dataset.resetAt || '').getTime())
                .filter((time) => Number.isFinite(time) && time > Date.now())
                .sort((a, b) => a - b)[0];

            if (refreshAt) {
                window.setTimeout(() => { window.location.reload(); }, Math.max(1000, refreshAt - Date.now() + 500));
            }
            window.setInterval(updateCountdowns, 1000);
        }

        // Pemasangan Event Listeners setelah DOM Siap
        document.addEventListener('DOMContentLoaded', function () {
            const satuanForm = document.getElementById('satuan-form');
            const satuanSelect = document.getElementById('satuan-parameter-select');

            calculateSamplingCards();
            initializeMissionCountdowns();

            document.querySelectorAll('#tab-batch [data-ffa-a], #tab-batch [data-ffa-b], #tab-batch [data-ffa-c], #tab-batch [data-dirt-l], #tab-batch [data-dirt-m], #tab-batch [data-dirt-o], #tab-batch [data-dirt-p], #tab-batch input[name^="measurements["]').forEach((input) => {
                input.addEventListener('input', calculateSamplingCards);
            });

            if (satuanSelect) {
                satuanSelect.addEventListener('change', handleSatuanSelectChange);
                handleSatuanSelectChange();
            }

            document.querySelectorAll('#satuan_ffa_a, #satuan_ffa_b, #satuan_ffa_c').forEach((input) => {
                input.addEventListener('input', calculateSatuanFfa);
            });
            document.querySelectorAll('#satuan_dirt_l, #satuan_dirt_m, #satuan_dirt_o, #satuan_dirt_p').forEach((input) => {
                input.addEventListener('input', calculateSatuanDirt);
            });

            if (satuanForm) {
                satuanForm.addEventListener('submit', function () {
                    const timeInput = document.getElementById('hidden_measured_time');
                    if (!timeInput) return;

                    const now = new Date();
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    timeInput.value = `${hours}:${minutes}`;
                });
            }

            document.querySelectorAll('[data-mission-shortcut]').forEach((missionCard) => {
                const missionDone = missionCard.querySelector('[data-mission-done]')?.getAttribute('data-mission-done') === '1';
                if (missionDone) return;

                missionCard.addEventListener('click', function () {
                    const parameterName = missionCard.getAttribute('data-parameter-name') || '';
                    const parameterId = missionCard.getAttribute('data-parameter-id') || '';
                    const paramSelect = document.getElementById('satuan-parameter-select');
                    if (!paramSelect) return;

                    let selectedOption = null;
                    if (parameterId) {
                        selectedOption = Array.from(paramSelect.options).find((option) => option.value === parameterId) || null;
                    }
                    if (!selectedOption && parameterName) {
                        selectedOption = Array.from(paramSelect.options).find((option) => option.textContent.includes(parameterName)) || null;
                    }
                    if (!selectedOption) return;

                    paramSelect.value = selectedOption.value;
                    paramSelect.dispatchEvent(new Event('change', { bubbles: true }));

                    const inputField = document.getElementById('satuan_generic_value') || document.getElementById('satuan_ffa_a') || document.getElementById('satuan_dirt_l');
                    if (inputField) {
                        inputField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        window.setTimeout(() => inputField.focus(), 250);
                    }
                });
            });
        });
    