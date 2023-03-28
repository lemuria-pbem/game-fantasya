/* Lemuria Development & Debugging */
document.addEventListener('readystatechange', () => {
    let isDebugEnabled = false;
    let meta = {};
    let party = {uuid: ''};

    const getMetaTags = function() {
        const list = document.head.getElementsByTagName('meta');
        for (let i = 0; i < list.length; i++) {
            const tag = list[i];
            if (tag.name) {
                meta[tag.name] = tag.content;
            }
        }
    };

    const initVariablesFromMeta = function() {
        if (meta.audience) {
            meta.audience.split(',').forEach((audience) => {
                audience = audience.trim();
                if (audience === 'debug') {
                    isDebugEnabled = true;
                }
            });
        }
        if (meta['fantasya-party']) {
            meta['fantasya-party'].split(',').forEach((property) => {
                property = property.trim().split('=');
                if (property.length === 2) {
                    party[property[0]] = property[1];
                }
            })
        }
    };

    const createDebugSection = function() {
        const eventHeader = document.getElementById('header').querySelector('h3');
        if (eventHeader) {
            const debugHeader = document.createElement('h3');
            debugHeader.appendChild(document.createTextNode('Debugging'));
            eventHeader.before(debugHeader);

            const partyDiv = document.createElement('p');
            eventHeader.before(partyDiv);
            const uuidLabel = document.createElement('strong');
            uuidLabel.appendChild(document.createTextNode('UUID'));
            partyDiv.appendChild(uuidLabel)
            const uuid = document.createElement('pre');
            uuid.appendChild(document.createTextNode(party.uuid));
            partyDiv.appendChild(uuid);
        }
    };

    getMetaTags();
    initVariablesFromMeta();
    isDebugEnabled && console.log('Development & debugging mode enabled.');
    isDebugEnabled && console.log('This is a report for party ' + party.uuid + '.');
    isDebugEnabled && createDebugSection();
});
