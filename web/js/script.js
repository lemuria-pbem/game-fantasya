/* Lemuria Alpha 0.12 */
$(function() {
    const toggleClass = 'non-responsive';

    $('#toggle-responsive').click(function () {
        let body = $('body');
        if (body.hasClass(toggleClass)) {
            body.removeClass(toggleClass);
        } else {
            body.addClass(toggleClass);
        }
    });
});
