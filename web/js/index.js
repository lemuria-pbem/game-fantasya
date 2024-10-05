/* Lemuria 1.5 */

document.addEventListener('readystatechange', () => {
    let href = document.location.href;
    href = href.substring(0, href.lastIndexOf('/'));
    href = href.substring(0, href.lastIndexOf('/'));

    const navigation = document.querySelectorAll('#navigation > a');
    navigation.forEach((link) => {
        const round = link.dataset['round'];
        link.setAttribute('href', href + '/' + round + '/index.html');
    });

    const popoverTriggers = document.querySelectorAll('[data-bs-toggle="popover"]');
    [...popoverTriggers].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl, {
        html: true
    }));
});
