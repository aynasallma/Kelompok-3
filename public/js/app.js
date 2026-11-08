/**
 * Klinik Sehat - Core Application Frontend JavaScript
 * Handles:
 * - Mobile sidebar drawer & backdrop
 * - Top navbar user dropdown
 * - Searchable medicine dropdown & dynamic row addition
 * - Real-time stock validation (accumulated across rows)
 * - Confirmation dialogs
 * - Dynamic booking availability
 */

document.addEventListener('DOMContentLoaded', function () {
    // -------------------------------------------------------------
    // 1. Mobile Sidebar Toggle & Backdrop
    // -------------------------------------------------------------
    var mobileToggle = document.getElementById('mobile-menu-toggle');
    var sidebar = document.getElementById('sidebar');
    var backdrop = document.getElementById('sidebar-backdrop');
    var sidebarClose = document.getElementById('sidebar-close');

    function openSidebar() {
        if (sidebar) sidebar.classList.add('open');
        if (backdrop) backdrop.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        if (sidebar) sidebar.classList.remove('open');
        if (backdrop) backdrop.classList.remove('show');
        document.body.style.overflow = '';
    }

    if (mobileToggle) mobileToggle.addEventListener('click', openSidebar);
    if (sidebarClose) sidebarClose.addEventListener('click', closeSidebar);
    if (backdrop) backdrop.addEventListener('click', closeSidebar);

    // -------------------------------------------------------------
    // 2. Top Navbar User Profile Dropdown
    // -------------------------------------------------------------
    var userMenuBtn = document.getElementById('user-menu-btn');
    var profileDropdown = document.getElementById('profile-dropdown');

    if (userMenuBtn && profileDropdown) {
        userMenuBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            var isOpen = profileDropdown.style.display === 'block';
            profileDropdown.style.display = isOpen ? 'none' : 'block';
            userMenuBtn.setAttribute('aria-expanded', !isOpen);
        });

        document.addEventListener('click', function (e) {
            if (!profileDropdown.contains(e.target) && e.target !== userMenuBtn) {
                profileDropdown.style.display = 'none';
                userMenuBtn.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // -------------------------------------------------------------
    // 3. Form Confirmation Dialogs
    // -------------------------------------------------------------
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            var msg = form.getAttribute('data-confirm');
            if (msg && !confirm(msg)) {
                e.preventDefault();
                return false;
            }
        });
    });

    document.querySelectorAll('button[data-confirm]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            var msg = btn.getAttribute('data-confirm');
            if (msg && !confirm(msg)) {
                e.preventDefault();
                return false;
            }
        });
    });

    // -------------------------------------------------------------
    // 4. Form Resep: Searchable Dropdown & Multi-Row Stock Validation
    // -------------------------------------------------------------
    var resepContainer = document.getElementById('resep-items');
    if (resepContainer && window.__DATA_OBAT__) {
        var obatList = window.__DATA_OBAT__;
        var addBtn = document.getElementById('add-row');
        var totalEl = document.getElementById('estimasi-total');
        var submitBtn = document.getElementById('btn-submit-resep');
        var warningBanner = document.getElementById('stock-warning-banner');
        var warningMessage = document.getElementById('stock-warning-message');

        function formatRupiah(n) {
            return 'Rp ' + Math.round(n).toLocaleString('id-ID');
        }

        // Initialize searchable combobox on a single row
        function initSearchableRow(row) {
            var searchInput = row.querySelector('.obat-search-input');
            var hiddenSelect = row.querySelector('.obat-select');
            var dropdownList = row.querySelector('.searchable-dropdown-list');
            var stockIndicator = row.querySelector('.obat-stock-indicator');
            if (!searchInput || !hiddenSelect || !dropdownList) return;

            // Pre-populate search input if select already has value (e.g. from old input)
            var currentVal = parseInt(hiddenSelect.value, 10);
            if (currentVal) {
                var found = obatList.find(function (o) { return o.id === currentVal; });
                if (found) {
                    searchInput.value = found.nama + ' (' + found.satuan + ')';
                    updateIndicator(found);
                }
            }

            function updateIndicator(item) {
                if (!stockIndicator) return;
                if (!item) {
                    stockIndicator.innerHTML = '';
                    return;
                }
                var stockClass = item.stok <= 0 ? 'text-danger' : (item.stok <= 5 ? 'text-warning' : 'text-success');
                stockIndicator.innerHTML = '<span class="' + stockClass + '">Tersedia: <strong>' + item.stok + '</strong> ' + item.satuan + '</span> &bull; <span class="muted">' + formatRupiah(item.harga) + ' / ' + item.satuan + '</span>';
            }

            function renderDropdown(filterText) {
                var q = (filterText || '').toLowerCase().trim();
                var matches = obatList.filter(function (o) {
                    return !q || o.nama.toLowerCase().includes(q) ||
                                 o.kode.toLowerCase().includes(q) ||
                                 (o.kategori && o.kategori.toLowerCase().includes(q));
                });

                if (matches.length === 0) {
                    dropdownList.innerHTML = '<div class="search-item muted" style="padding: 8px 12px; font-size: 13px;">Obat tidak ditemukan</div>';
                } else {
                    dropdownList.innerHTML = matches.map(function (o) {
                        var isHabis = o.stok <= 0;
                        return '<div class="search-item' + (isHabis ? ' item-disabled' : '') + '" data-id="' + o.id + '" style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #f1f5f9; font-size: 13px;">' +
                            '<strong style="font-size: 13.5px; font-weight: 600; color: #0f172a; display: inline-block;">' + o.nama + '</strong> <small class="muted" style="font-size: 11.5px;">[' + o.kode + ']</small>' +
                            '<div style="font-size: 12px; display: flex; justify-content: space-between; margin-top: 2px;">' +
                                '<span class="' + (isHabis ? 'text-danger' : 'text-success') + '">Stok: ' + o.stok + ' ' + o.satuan + '</span>' +
                                '<span class="muted">' + formatRupiah(o.harga) + '</span>' +
                            '</div>' +
                        '</div>';
                    }).join('');
                }
                dropdownList.style.display = 'block';
            }

            searchInput.addEventListener('focus', function () {
                renderDropdown(searchInput.value);
            });

            searchInput.addEventListener('input', function () {
                renderDropdown(searchInput.value);
            });

            dropdownList.addEventListener('mousedown', function (e) {
                var itemEl = e.target.closest('.search-item');
                if (!itemEl) return;
                var obatId = parseInt(itemEl.getAttribute('data-id'), 10);
                if (!obatId) return;

                var selectedObat = obatList.find(function (o) { return o.id === obatId; });
                if (selectedObat) {
                    hiddenSelect.value = selectedObat.id;
                    searchInput.value = selectedObat.nama + ' (' + selectedObat.satuan + ')';
                    updateIndicator(selectedObat);
                    dropdownList.style.display = 'none';
                    validateAllStockAndCalculateTotal();
                }
            });

            searchInput.addEventListener('blur', function () {
                setTimeout(function () {
                    dropdownList.style.display = 'none';
                }, 200);
            });
        }

        // Validate all rows against available stock and aggregate duplicate drugs
        function validateAllStockAndCalculateTotal() {
            var rows = resepContainer.querySelectorAll('.resep-row');
            var totalCost = 0;
            var accumulatedNeeds = {}; // { id_obat: totalRequested }
            var hasStockError = false;
            var errorMessages = [];

            // Pass 1: Sum quantities per medicine ID
            rows.forEach(function (row) {
                var select = row.querySelector('.obat-select');
                var jumlahInput = row.querySelector('.jumlah-input');
                var obatId = parseInt(select ? select.value : 0, 10);
                var qty = parseInt(jumlahInput ? jumlahInput.value : 0, 10) || 0;

                if (obatId > 0 && qty > 0) {
                    accumulatedNeeds[obatId] = (accumulatedNeeds[obatId] || 0) + qty;
                }
            });

            // Pass 2: Validate each row and calculate total price
            rows.forEach(function (row) {
                var select = row.querySelector('.obat-select');
                var jumlahInput = row.querySelector('.jumlah-input');
                var rowAlert = row.querySelector('.row-stock-alert');
                var obatId = parseInt(select ? select.value : 0, 10);
                var qty = parseInt(jumlahInput ? jumlahInput.value : 0, 10) || 0;

                if (rowAlert) rowAlert.textContent = '';
                if (jumlahInput) jumlahInput.classList.remove('is-invalid');

                if (obatId > 0) {
                    var obatData = obatList.find(function (o) { return o.id === obatId; });
                    if (obatData) {
                        totalCost += obatData.harga * qty;
                        var totalRequested = accumulatedNeeds[obatId] || 0;

                        if (totalRequested > obatData.stok) {
                            hasStockError = true;
                            if (jumlahInput) jumlahInput.classList.add('is-invalid');
                            var alertMsg = 'Melebihi stok! Diminta: ' + totalRequested + ', Tersedia: ' + obatData.stok + ' ' + obatData.satuan;
                            if (rowAlert) rowAlert.textContent = alertMsg;

                            var errMsg = 'Obat "' + obatData.nama + '" diminta total ' + totalRequested + ' ' + obatData.satuan + ', namun stok farmasi hanya ' + obatData.stok + ' ' + obatData.satuan + '.';
                            if (!errorMessages.includes(errMsg)) {
                                errorMessages.push(errMsg);
                            }
                        }
                    }
                }
            });

            // Update estimated total
            if (totalEl) totalEl.textContent = formatRupiah(totalCost);

            // Update warning banner and disable submit button if invalid
            if (warningBanner && warningMessage) {
                if (hasStockError) {
                    warningMessage.innerHTML = errorMessages.join('<br>');
                    warningBanner.style.display = 'block';
                } else {
                    warningBanner.style.display = 'none';
                    warningMessage.innerHTML = '';
                }
            }

            if (submitBtn) {
                submitBtn.disabled = hasStockError;
            }
        }

        // Initialize existing rows
        resepContainer.querySelectorAll('.resep-row').forEach(function (r) {
            initSearchableRow(r);
        });

        // Add new row dynamically
        if (addBtn) {
            addBtn.addEventListener('click', function () {
                var first = resepContainer.querySelector('.resep-row');
                var clone = first.cloneNode(true);

                // Reset inputs and indicators
                clone.querySelectorAll('input').forEach(function (i) {
                    i.value = '';
                    i.classList.remove('is-invalid');
                });
                var hiddenSelect = clone.querySelector('.obat-select');
                if (hiddenSelect) hiddenSelect.selectedIndex = 0;
                var stockIndicator = clone.querySelector('.obat-stock-indicator');
                if (stockIndicator) stockIndicator.innerHTML = '';
                var rowAlert = clone.querySelector('.row-stock-alert');
                if (rowAlert) rowAlert.textContent = '';
                var dropdownList = clone.querySelector('.searchable-dropdown-list');
                if (dropdownList) dropdownList.style.display = 'none';

                resepContainer.appendChild(clone);
                initSearchableRow(clone);
                validateAllStockAndCalculateTotal();
            });
        }

        // Remove row
        resepContainer.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-row')) {
                var rows = resepContainer.querySelectorAll('.resep-row');
                if (rows.length > 1) {
                    e.target.closest('.resep-row').remove();
                    validateAllStockAndCalculateTotal();
                } else {
                    alert('Resep harus memiliki minimal satu baris obat.');
                }
            }
        });

        // Listen for input changes in quantity or selection
        resepContainer.addEventListener('input', validateAllStockAndCalculateTotal);
        resepContainer.addEventListener('change', validateAllStockAndCalculateTotal);

        // Initial validation
        validateAllStockAndCalculateTotal();
    }

    // -------------------------------------------------------------
    // 5. Booking Availability Dynamic Filter (Pasien & Pendaftaran)
    // -------------------------------------------------------------
    document.querySelectorAll('.visit-availability-form').forEach(function (form) {
        var dateInput = form.querySelector('[name="tanggal_kunjungan"]');
        var poliSelect = form.querySelector('[name="poli"]');
        var doctorSelect = form.querySelector('[name="id_dokter"]');
        var submitButton = form.querySelector('button[type="submit"]');
        var message = form.querySelector('[data-availability-message]');
        var availability = {};

        function setDoctors() {
            if (!doctorSelect) return;
            var doctors = (availability.doctorsByPoli && availability.doctorsByPoli[poliSelect.value]) || [];
            doctorSelect.replaceChildren(new Option('Pilih dokter', ''));
            doctors.forEach(function (doctor) {
                doctorSelect.add(new Option(doctor.nama, doctor.id_user));
            });
            doctorSelect.disabled = doctors.length === 0;
            if (submitButton) submitButton.disabled = doctors.length === 0;
        }

        function loadAvailability() {
            if (!dateInput || !poliSelect) return;
            poliSelect.disabled = true;
            if (doctorSelect) doctorSelect.disabled = true;
            if (submitButton) submitButton.disabled = true;

            var url = form.getAttribute('data-options-url') + '&tanggal=' + encodeURIComponent(dateInput.value);
            fetch(url)
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    availability = data;
                    poliSelect.replaceChildren(new Option('Pilih poli', ''));
                    data.options.forEach(function (poli) {
                        poliSelect.add(new Option(poli, poli));
                    });
                    poliSelect.disabled = data.options.length === 0;
                    if (message) message.textContent = data.closedMessage || '';
                    setDoctors();
                })
                .catch(function () {
                    availability = { options: [], doctorsByPoli: {} };
                    poliSelect.replaceChildren(new Option('Jadwal tidak tersedia', ''));
                    if (doctorSelect) doctorSelect.replaceChildren(new Option('Pilih dokter', ''));
                    if (message) message.textContent = 'Jadwal belum dapat diperiksa. Coba kembali.';
                });
        }

        if (dateInput) dateInput.addEventListener('change', loadAvailability);
        if (poliSelect) poliSelect.addEventListener('change', setDoctors);
    });
});
