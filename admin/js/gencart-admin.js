(function () {
    'use strict';

    function copyText(text) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            return navigator.clipboard.writeText(text);
        }

        return new Promise(function (resolve, reject) {
            var temp = document.createElement('input');
            temp.type = 'text';
            temp.value = text;
            temp.setAttribute('readonly', 'readonly');
            temp.style.position = 'absolute';
            temp.style.left = '-9999px';
            document.body.appendChild(temp);
            temp.select();
            try {
                var successful = document.execCommand('copy');
                document.body.removeChild(temp);
                if (successful) {
                    resolve();
                } else {
                    reject();
                }
            } catch (err) {
                document.body.removeChild(temp);
                reject(err);
            }
        });
    }

    function showNotice(container, message) {
        if (!container) {
            return;
        }
        container.textContent = message;
        container.classList.add('visible');
        setTimeout(function () {
            container.classList.remove('visible');
        }, 4000);
    }

    document.addEventListener('click', function (event) {
        var trigger = event.target.closest('.gencart-copy-link');
        if (!trigger) {
            return;
        }

        event.preventDefault();

        var text = trigger.getAttribute('data-gencart-target');
        var noticeMessage = trigger.getAttribute('data-notice') || '';
        var noticeContainer = trigger.closest('.gencart-button-group');
        if (noticeContainer) {
            noticeContainer = noticeContainer.nextElementSibling;
        }

        copyText(text)
            .then(function () {
                showNotice(noticeContainer, noticeMessage);
            })
            .catch(function () {
                showNotice(noticeContainer, (window.gencartAdmin && window.gencartAdmin.fallbackNotice) || '');
            });
    });
})();
