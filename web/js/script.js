/* Lemuria 1.2 */
document.addEventListener('readystatechange', () => {
    const toggleClass = [
        ['non-responsive'],
        [],
        ['non-responsive', 'fantasya-font'],
        ['fantasya-font']
    ];

    const loadingIndicator = document.getElementById('loading-indicator');
    const lemuriaReport = document.getElementById('lemuria-report');
    const toggleButton = document.getElementById('toggle-responsive');
    const toggleItem = 'LemuriaReportState';
    const gotoButton = document.getElementById('toggle-goto');
    const gotoModal = document.getElementById('modal-goto');
    const gotoModalBs = new bootstrap.Modal(gotoModal);
    const gotoId = document.getElementById('modal-goto-id');
    const navButton = document.getElementById('navbar-toggle');
    const statistics = '#statistics';
    const alliances = '#alliances';
    const spellBook = document.getElementById('spell-book');
    const herbalBook = document.getElementById('herbal-book');
    const messagesButton = document.getElementById('messages-button');
    const messages = document.querySelectorAll('#world ul.report span.badge.text-bg-info, #world ul.report span.badge.text-bg-warning');

    let classIndex = 0;
    let enableKeys = true;
    let messageIndex = 0;

    const buttonHandled = function(event, button) {
        event.preventDefault();
        button.click();
    };

    const gotoHandled = function(event) {
        event.preventDefault();
        gotoModalBs.show();
    };

    const locationHandled = function(event, location) {
        event.preventDefault();
        document.location.href = location;
    };

    const getToggleStateFromCss = function() {
        const body = document.body.classList;
        for (let i = 0; i < toggleClass.length; i++) {
            if (toggleClass[i].length === body.length) {
                let classes = toggleClass[i].slice();
                let n = classes.length;
                for (let c = 0; c < classes.length; c++) {
                    if (body.contains(classes[c])) {
                        classes[c] = null;
                        n--;
                        break;
                    }
                }
                if (n === 0) {
                    return i;
                }
            }
        }
        return 0;
    };

    const setBodyClass = function (index) {
        if (index < toggleClass.length) {
            classIndex = index;
            document.body.setAttribute('class', toggleClass[index].join(' '));
            window.localStorage.setItem(toggleItem, classIndex.toString());
        }
    };

    const initToggleState = function() {
        const stored = parseInt(window.localStorage.getItem(toggleItem));
        const last = isNaN(stored) ? 0 : stored;
        setBodyClass(last);
        loadingIndicator && loadingIndicator.classList.add('d-none');
        lemuriaReport && lemuriaReport.classList.remove('visually-hidden');
    };

    toggleButton.addEventListener('click', () => {
        let i = classIndex + 1;
        if (i >= toggleClass.length) {
            i = 0;
        }
        setBodyClass(i);

    });

    gotoModal.addEventListener('show.bs.modal', () => {
        enableKeys = false;
    });

    gotoModal.addEventListener('shown.bs.modal', () => {
        gotoId.focus();
    });

    gotoModal.addEventListener('hide.bs.modal', () => {
        enableKeys = true;
    });

    gotoModal.addEventListener('hidden.bs.modal', () => {
        gotoId.value = '';
    });

    gotoId.addEventListener('change', () => {
        const id = 'unit-' + gotoId.value;
        if (document.getElementById(id)) {
            gotoModalBs.hide();
            document.location.href = '#' + id;
        }
    });

    const initMessagesButton = function () {
        if (messages.length) {
            document.getElementById('messages-button-count').innerText = messages.length.toString();
            document.getElementById('messages-button-text').innerText = messages.length < 2 ? 'weiteres Ereignis' : 'weitere Ereignisse';
            messagesButton.classList.remove('d-none');
        }
    };

    messagesButton.addEventListener('click', () => {
        if (messages.length) {
            if (messageIndex >= messages.length) {
                messageIndex = 0;
            }
            messages[messageIndex++].scrollIntoView({block: 'center'});
        }
    });

    document.addEventListener('keydown', (event) => {
        if (!enableKeys) {
            return;
        }
        if (event.key === '#') {
            return buttonHandled(event, toggleButton);
        }
        if (event.key === 'a') {
            return locationHandled(event, alliances);
        }
        if (event.key === 'e') {
            return buttonHandled(event, messagesButton);
        }
        if (event.key === 'g') {
            return gotoHandled(event, gotoButton);
        }
        if (event.key === 'i') {
            return buttonHandled(event, navButton);
        }
        if (event.key === 'k') {
            return locationHandled(event, herbalBook.href);
        }
        if (event.key === 's') {
            return locationHandled(event, statistics);
        }
        if (event.key === 'z') {
            return locationHandled(event, spellBook.href);
        }
    });

    initToggleState();
    initMessagesButton();
});
