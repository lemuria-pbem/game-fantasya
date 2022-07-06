/* Lemuria Alpha 0.12 */
$(function() {
    const toggleButton = $('#toggle-responsive');
    const toggleClass = 'non-responsive';
    const gotoButton = $('#toggle-goto');
    const gotoModal = $('#modal-goto');
    const gotoId = $('#modal-goto-id');
    const navButton = $('#navbar-toggle');
    const statistics = '#statistics';
    const spellBook = $('#spell-book');
    const herbalBook = $('#herbal-book');

    let enableKeys = true;

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

    toggleButton.click(function () {
        let body = $('body');
        if (body.hasClass(toggleClass)) {
            body.removeClass(toggleClass);
        } else {
            body.addClass(toggleClass);
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

    $(document).keydown(function(event) {
        if (!enableKeys) {
            return;
        }
        if (event.key === '#') {
            return buttonHandled(event, toggleButton);
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
});
