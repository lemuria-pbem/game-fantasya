/* Lemuria Alpha 0.12 */
$(function() {
    const toggleButton = $('#toggle-responsive');
    const toggleClass = 'non-responsive';
    const navButton = $('#navbar-toggle');
    const statistics = '#statistics';
    const spellBook = $('#spell-book');
    const herbalBook = $('#herbal-book');

    toggleButton.click(function () {
        let body = $('body');
        if (body.hasClass(toggleClass)) {
            body.removeClass(toggleClass);
        } else {
            body.addClass(toggleClass);
        }
    });

    $(document).keydown(function(event) {
        if (event.key === '#') {
            toggleButton.click();
        }
        if (event.key === 'i') {
            navButton.click();
        }
        if (event.key === 'k') {
            document.location.href = herbalBook.attr('href');
        }
        if (event.key === 's') {
            document.location.hash = statistics;
        }
        if (event.key === 'z') {
            document.location.href = spellBook.attr('href');
        }
    });
});
