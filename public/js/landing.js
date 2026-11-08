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

    // Active Section Scrollspy for Landing Page
    var navLinksWithSection = navigation ? navigation.querySelectorAll('a[data-nav]') : [];
    if (navLinksWithSection.length > 0) {
        var sections = [];
        navLinksWithSection.forEach(function (link) {
            var sectionId = link.getAttribute('data-nav');
            var el = document.getElementById(sectionId);
            if (el) {
                sections.push({ id: sectionId, el: el, link: link });
            }
        });

        if (sections.length > 0) {
            function updateActiveNavOnScroll() {
                var scrollPosition = window.scrollY + 120;
                var currentSectionId = '';

                for (var i = 0; i < sections.length; i++) {
                    var s = sections[i];
                    var top = s.el.offsetTop;
                    var height = s.el.offsetHeight;
                    if (scrollPosition >= top && scrollPosition < top + height) {
                        currentSectionId = s.id;
                        break;
                    }
                }

                // If scrolled to near the bottom of the page
                if ((window.innerHeight + window.scrollY) >= document.documentElement.scrollHeight - 50) {
                    var last = sections[sections.length - 1];
                    currentSectionId = last.id;
                }

                if (currentSectionId) {
                    navLinksWithSection.forEach(function (link) {
                        if (link.getAttribute('data-nav') === currentSectionId) {
                            link.classList.add('current');
                        } else {
                            link.classList.remove('current');
                        }
                    });
                }
            }

            window.addEventListener('scroll', updateActiveNavOnScroll, { passive: true });
            window.addEventListener('load', updateActiveNavOnScroll);
            updateActiveNavOnScroll();
        }
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