/* Lemuria 1.5 */

document.addEventListener('readystatechange', () => {
    const popoverTriggers = document.querySelectorAll('[data-bs-toggle="popover"]');
    [...popoverTriggers].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl, {
        html: true
    }));
});
