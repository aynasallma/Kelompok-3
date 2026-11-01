// Konfirmasi sebelum submit form yang memiliki data-confirm
document.querySelectorAll('form[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
        if (!confirm(form.getAttribute('data-confirm'))) {
            e.preventDefault();
        }
    });
});

// Form resep: tambah / hapus baris obat dan hitung estimasi total
(function () {
    var container = document.getElementById('resep-items');
    if (!container) return;

    var addBtn = document.getElementById('add-row');
    var totalEl = document.getElementById('estimasi-total');

    function formatRupiah(n) {
        return 'Rp ' + Math.round(n).toLocaleString('id-ID');
    }

    function hitungTotal() {
        var total = 0;
        container.querySelectorAll('.resep-row').forEach(function (row) {
            var select = row.querySelector('.obat-select');
            var jumlah = parseInt(row.querySelector('.jumlah-input').value, 10) || 0;
            var opt = select.options[select.selectedIndex];
            var harga = opt ? parseFloat(opt.getAttribute('data-harga')) || 0 : 0;
            total += harga * jumlah;
        });
        totalEl.textContent = formatRupiah(total);
    }

    addBtn.addEventListener('click', function () {
        var first = container.querySelector('.resep-row');
        var clone = first.cloneNode(true);
        clone.querySelectorAll('input').forEach(function (i) { i.value = ''; });
        clone.querySelector('select').selectedIndex = 0;
        container.appendChild(clone);
    });

    container.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row')) {
            var rows = container.querySelectorAll('.resep-row');
            if (rows.length > 1) {
                e.target.closest('.resep-row').remove();
                hitungTotal();
            }
        }
    });

    container.addEventListener('input', hitungTotal);
    container.addEventListener('change', hitungTotal);
})();

(function () {
    document.querySelectorAll('.visit-availability-form').forEach(function (form) {
        var dateInput = form.querySelector('[name="tanggal_kunjungan"]');
        var poliSelect = form.querySelector('[name="poli"]');
        var doctorSelect = form.querySelector('[name="id_dokter"]');
        var submitButton = form.querySelector('button[type="submit"]');
        var message = form.querySelector('[data-availability-message]');
        var availability = {};

        function setDoctors() {
            var doctors = availability.doctorsByPoli[poliSelect.value] || [];
            doctorSelect.replaceChildren(new Option('Pilih dokter', ''));
            doctors.forEach(function (doctor) {
                doctorSelect.add(new Option(doctor.nama, doctor.id_user));
            });
            doctorSelect.disabled = doctors.length === 0;
            submitButton.disabled = doctors.length === 0;
        }

        function loadAvailability() {
            poliSelect.disabled = true;
            doctorSelect.disabled = true;
            submitButton.disabled = true;
            fetch(form.getAttribute('data-options-url') + '&tanggal=' + encodeURIComponent(dateInput.value))
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    availability = data;
                    poliSelect.replaceChildren(new Option('Pilih poli', ''));
                    data.options.forEach(function (poli) {
                        poliSelect.add(new Option(poli, poli));
                    });
                    poliSelect.disabled = data.options.length === 0;
                    message.textContent = data.closedMessage;
                    setDoctors();
                })
                .catch(function () {
                    availability = { options: [], doctorsByPoli: {} };
                    poliSelect.replaceChildren(new Option('Jadwal tidak tersedia', ''));
                    doctorSelect.replaceChildren(new Option('Pilih dokter', ''));
                    message.textContent = 'Jadwal belum dapat diperiksa. Coba kembali.';
                });
        }

        dateInput.addEventListener('change', loadAvailability);
        poliSelect.addEventListener('change', setDoctors);
        loadAvailability();
    });
})();
