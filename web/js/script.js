/* Lemuria Alpha 0.12 */
$(function() {
    const toggleButton = $('#toggle-responsive');
    const toggleClass = 'non-responsive';
    const toggleItem = 'LemuriaAlphaIsResponsive';
    const gotoButton = $('#toggle-goto');
    const gotoModal = $('#modal-goto');
    const gotoId = $('#modal-goto-id');
    const navButton = $('#navbar-toggle');
    const statistics = '#statistics';
    const spellBook = $('#spell-book');
    const herbalBook = $('#herbal-book');
    const messagesButton = $('#messages-button');
    const messages = $('ul.report[class!="party report"] span.badge-info, ul.report[class!="party report"] span.badge-warning').get();

    let enableKeys = true;
    let messageIndex = 0;

    const buttonHandled = function(event, button) {
        event.preventDefault();
        button.click();
    };

    const gotoHandled = function(event) {
        event.preventDefault();
        gotoModal.modal('show');
    };

    const locationHandled = function(event, location) {
        event.preventDefault();
        document.location.href = location;
    };

    const initToggleState = function() {
        let body = $('body');
        const current = body.hasClass(toggleClass);
        const last = (window.localStorage.getItem(toggleItem) === '1');
        if (last && current) {
            body.removeClass(toggleClass);
            return;
        }
        if (!last && !current){
            body.addClass(toggleClass);
        }
    };

    toggleButton.click(function () {
        let body = $('body');
        if (body.hasClass(toggleClass)) {
            body.removeClass(toggleClass);
            window.localStorage.setItem(toggleItem, '1');
        } else {
            body.addClass(toggleClass);
            window.localStorage.setItem(toggleItem, '0');
        }
    });

    gotoModal.on('show.bs.modal', function() {
        enableKeys = false;
    });

    gotoModal.on('shown.bs.modal', function() {
        gotoId.focus();
    });

    gotoModal.on('hide.bs.modal', function() {
        enableKeys = true;
    });

    gotoModal.on('hidden.bs.modal', function() {
        gotoId.val('');
    });

    gotoId.on('change', function() {
        const id = 'unit-' + gotoId.val();
        if (document.getElementById(id)) {
            gotoModal.modal('hide');
            document.location.href = '#' + id;
        }
    });

    const initMessagesButton = function () {
        if (messages.length) {
            $('#messages-button-count').html(messages.length);
            if (messages.length < 2) {
                $('#messages-button-text').html('weiteres Ereignis');
            } else {
                $('#messages-button-text').html('weitere Ereignisse');
            }
            messagesButton.removeClass('d-none');
        }
    };

    messagesButton.click(function() {
        if (messages.length) {
            if (messageIndex >= messages.length) {
                messageIndex = 0;
            }
            messages[messageIndex++].scrollIntoView({block: 'center'});
        }
    });

    $(document).keydown(function(event) {
        if (!enableKeys) {
            return;
        }
        if (event.key === '#') {
            return buttonHandled(event, toggleButton);
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
            return locationHandled(event, herbalBook.attr('href'));
        }
        if (event.key === 's') {
            return locationHandled(event, statistics);
        }
        if (event.key === 'z') {
            return locationHandled(event, spellBook.attr('href'));
        }
    });

    initToggleState();
    initMessagesButton();
});
