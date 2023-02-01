/* Lemuria 1.2 */
document.addEventListener('readystatechange', () => {
    const toggleClass = [
        ['non-responsive'],
        [],
        ['non-responsive', 'fantasya-font'],
        ['fantasya-font']
    ];

    let messagesConfig = {
        'battle': true,
        'guard': true,
        'movement': true
    };

    const loadingIndicator = document.getElementById('loading-indicator');
    const lemuriaReport = document.getElementById('lemuria-report');
    const toggleHelp = document.getElementById('toggle-help');
    const helpModal = document.getElementById('modal-help');
    const helpModalBs = helpModal ? new bootstrap.Modal(helpModal) : null;
    const toggleButton = document.getElementById('toggle-responsive');
    const toggleItem = 'LemuriaReportState';
    const gotoButton = document.getElementById('toggle-goto');
    const gotoModal = document.getElementById('modal-goto');
    const gotoModalBs = gotoModal ? new bootstrap.Modal(gotoModal) : null;
    const gotoId = document.getElementById('modal-goto-id');
    const navButton = document.getElementById('navbar-toggle');
    const mapButton = document.getElementById('toggle-map');
    const mapModal = document.getElementById('modal-map');
    const statistics = '#statistics';
    const talentStatistics = document.querySelectorAll('.talent-statistics.modal');
    const alliances = '#alliances';
    const spellBook = document.getElementById('spell-book');
    const herbalBook = document.getElementById('herbal-book');
    const messagesButton = document.getElementById('messages-button');
    const messagesConfigurator = document.querySelectorAll('#messages-button-config .dropdown-item');
    const messagesConfigItem = 'LemuriaMessagesConfig';
    const messagesSelector = document.querySelectorAll(
        '#world ul.report span.badge.text-bg-info, #world ul.report span.badge.text-bg-warning, #world ul.report span.badge.text-bg-danger'
    );
    let messages;

    let classIndex = 0;
    let enableKeys = true;
    let messageIndex = 0;
    let talentStatisticsTarget = null;

    const buttonHandled = function(event, button) {
        event.preventDefault();
        button?.click();
    };

    const helpHandled = function(event) {
        event.preventDefault();
        helpModalBs?.show();
    };

    const gotoHandled = function(event) {
        event.preventDefault();
        gotoModalBs?.show();
    };

    const locationHandled = function(event, location) {
        event.preventDefault();
        document.location.href = location;
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
        loadingIndicator?.classList.add('d-none');
        lemuriaReport?.classList.remove('visually-hidden');
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
            gotoModalBs?.hide();
            document.location.href = '#' + id;
        }
    });

    const closeAllDialogs = function () {
        if (mapModal) {
            const modal = bootstrap.Modal.getInstance(mapModal);
            modal?.hide();
        }
        if (helpModal) {
            const modal = bootstrap.Modal.getInstance(helpModal);
            modal?.hide();
        }
    };

    const isAllMessages = function () {
        let isAll = true;
        Object.values(messagesConfig).forEach((config) => {
            isAll &= config;
        });
        return isAll;
    };

    const initMessagesButton = function () {
        if (isAllMessages()) {
            messages = messagesSelector;
        } else {
            messages = [];
            messagesSelector.forEach((message) => {
                const section = message.dataset.section;
                if (section) {
                    if (messagesConfig[section]) {
                        messages.push(message);
                    }
                } else {
                    messages.push(message);
                }
            });
        }

        if (messages.length) {
            document.getElementById('messages-button-count').innerText = messages.length.toString();
            document.getElementById('messages-button-text').innerText = messages.length === 1 ? 'weiteres Ereignis' : 'weitere Ereignisse';
        } else {
            document.getElementById('messages-button-count').innerText = '';
            document.getElementById('messages-button-text').innerText = 'keine weiteren Ereignisse';
        }
    };

    const setMessagesConfig = function (configOption) {
        const key = configOption.dataset.option;
        const value = messagesConfig[key];
        if (value) {
            configOption.classList.add('option-set');
        } else {
            configOption.classList.remove('option-set');
        }
    };

    const initMessagesConfig = function () {
        const stored = window.localStorage.getItem(messagesConfigItem);
        if (typeof stored === 'string') {
            const parsed = JSON.parse(stored);
            typeof parsed === 'object' && Object.keys(messagesConfig).forEach((key) => {
                if (typeof parsed[key] === 'boolean') {
                    messagesConfig[key] = parsed[key];
                }
            });
        }
        messagesConfigurator.forEach((config) => {
            const key = config.dataset.option;
            if (key && typeof messagesConfig[key] === 'boolean') {
                setMessagesConfig(config);
                config.addEventListener('click', (event) => {
                    event.preventDefault();
                    const config = event.target, key = config.dataset.option;
                    messagesConfig[key] = !messagesConfig[key];
                    setMessagesConfig(config);
                    window.localStorage.setItem(messagesConfigItem, JSON.stringify(messagesConfig));
                    initMessagesButton();
                });
            }
        });
    };

    messagesButton.addEventListener('click', () => {
        if (messages.length) {
            if (messageIndex >= messages.length) {
                messageIndex = 0;
            }
            messages[messageIndex++].scrollIntoView({block: 'center'});
        }
    });

    const initTalentStatistics = function() {
        for (const a of document.querySelectorAll('.talent-statistics.modal table a')) {
            a.addEventListener('mousedown', event => {
                const unitId = event.target.getAttribute('data-unit');
                if (unitId) {
                    talentStatisticsTarget = 'unit-' + unitId;
                } else {
                    const regionId = event.target.getAttribute('data-region');
                    if (regionId) {
                        talentStatisticsTarget = 'region-' + regionId;
                    }
                }
            });
        }
        for (const modal of talentStatistics) {
            modal.addEventListener('hidden.bs.modal', () => {
                if (talentStatisticsTarget) {
                    window.setTimeout(function() {
                        document.location.href = '#' + talentStatisticsTarget;
                        talentStatisticsTarget = null;
                    }, 0);
                }
            });
        }
    }

    const moveMapToTarget = function (target) {
        if (target && !target.startsWith('#location-')) {
            let location;
            if (target.startsWith('#continent-')) {
                const parent = document.getElementById(target.substring(1))?.parentElement;
                location = parent?.dataset.firstLocation;
            } else {
                let parent = document.getElementById(target.substring(1));
                do {
                    parent = parent.parentElement;
                    if (parent.localName === 'article' && parent.classList.contains('region')) {
                        break;
                    }
                } while (parent);
                location = parent?.dataset.id;
            }
            target = location ? '#' + location : null;
        }
        if (target) {
            const id = target.substring(10);
            const mapTile = document.getElementById('map-' + id);
            mapTile?.scrollIntoView({behavior: "smooth", block: "center", inline: "center"});
        }
    };

    window.addEventListener('hashchange', () => {
        moveMapToTarget(document.location.hash);
    });

    mapModal?.addEventListener('shown.bs.modal', () => {
        moveMapToTarget(document.location.hash);
    });

    document.addEventListener('keydown', (event) => {
        if (!enableKeys) {
            return;
        }
        if (event.key === '?') {
            return helpHandled(event, toggleHelp);
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
        if (event.key === 'w') {
            return buttonHandled(event, mapButton);
        }
        if (event.key === 'z') {
            return locationHandled(event, spellBook.href);
        }
        if (event.key === 'Escape') {
            return closeAllDialogs();
        }
    });

    initToggleState();
    initTalentStatistics();
    initMessagesConfig();
    initMessagesButton();
});
