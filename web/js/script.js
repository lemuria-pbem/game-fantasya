/* Lemuria 1.2 */
const toggleButton = document.getElementById('toggle-responsive');
const toggleClass = 'non-responsive';
const toggleItem = 'LemuriaIsResponsive';
const gotoButton = document.getElementById('toggle-goto');
const gotoModal = document.getElementById('modal-goto');
const gotoId = document.getElementById('modal-goto-id');
const navButton = document.getElementById('navbar-toggle');
const statistics = '#statistics';
const alliances = '#alliances';
const spellBook = document.getElementById('spell-book');
const herbalBook = document.getElementById('herbal-book');
const messagesButton = document.getElementById('messages-button');
const messages = document.querySelectorAll('#world ul.report span.badge-info, #world ul.report span.badge-warning');

let enableKeys = true;
let messageIndex = 0;

const buttonHandled = function(event, button) {
    event.preventDefault();
    button.click();
};

const gotoHandled = function(event) {
    event.preventDefault();
    new bootstrap.Modal(gotoModal).show();
};

const locationHandled = function(event, location) {
    event.preventDefault();
    document.location.href = location;
};

const initToggleState = function() {
    let body = document.body.classList;
    const current = body.contains(toggleClass);
    const last = (window.localStorage.getItem(toggleItem) === '1');
    if (last && current) {
        body.remove(toggleClass);
        return;
    }
    if (!last && !current){
        body.add(toggleClass);
    }
};

toggleButton.addEventListener('click', () => {
    let body = document.body.classList;
    if (body.contains(toggleClass)) {
        body.remove(toggleClass);
        window.localStorage.setItem(toggleItem, '1');
    } else {
        body.add(toggleClass);
        window.localStorage.setItem(toggleItem, '0');
    }
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
        new bootstrap.Modal(gotoModal).hide();
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
