(function () {
    var toggle = document.querySelector('.nav-toggle');
    var navigation = document.getElementById('public-navigation');

    if (toggle && navigation) {
        toggle.addEventListener('click', function () {
            var isOpen = toggle.getAttribute('aria-expanded') === 'true';
            toggle.setAttribute('aria-expanded', String(!isOpen));
            navigation.classList.toggle('is-open', !isOpen);
        });

        navigation.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                toggle.setAttribute('aria-expanded', 'false');
                navigation.classList.remove('is-open');
            });
        });
    }
})();

(function () {
    var form = document.querySelector('.application-form[data-options-url]');
    if (!form) return;

    var dateInput = form.querySelector('#tanggal_kunjungan');
    var poliSelect = form.querySelector('#poli');
    var submitButton = form.querySelector('button[type="submit"]');
    var closedMessage = form.querySelector('#registration-closed');

    dateInput.addEventListener('change', function () {
        poliSelect.disabled = true;
        submitButton.disabled = true;
        fetch(form.getAttribute('data-options-url') + '&tanggal=' + encodeURIComponent(dateInput.value))
            .then(function (response) { return response.json(); })
            .then(function (data) {
                poliSelect.replaceChildren(new Option('Pilih poli', ''));
                data.options.forEach(function (poli) {
                    poliSelect.add(new Option(poli, poli));
                });
                var closed = data.options.length === 0;
                poliSelect.disabled = closed;
                submitButton.disabled = closed;
                closedMessage.textContent = data.closedMessage;
                closedMessage.hidden = !closed;
            })
            .catch(function () {
                poliSelect.replaceChildren(new Option('Jadwal tidak tersedia', ''));
                closedMessage.textContent = 'Jadwal belum dapat diperiksa. Coba pilih tanggal lain.';
                closedMessage.hidden = false;
            });
    });
})();