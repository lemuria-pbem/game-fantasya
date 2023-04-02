/* Lemuria Development & Debugging */
document.addEventListener('readystatechange', () => {
    let isDebugEnabled = false;
    let meta = {};
    let party = {uuid: ''};
    let age = '', creationMessage;

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
        if (meta.created) {
            const now = new Date();
            const created = new Date(meta.created);
            let difference = Math.round((now[Symbol.toPrimitive]('number') - created[Symbol.toPrimitive]('number')) / 1000);
            creationMessage = 'Report age is ' + difference + ' seconds (created at ' + meta.created + ').';
            if (difference < 86400) {
                if (difference < 3600) {
                    if (difference < 60) {
                        age = difference + ' Sekunden';
                    } else {
                        const minutes = Math.round(difference / 60);
                        age = minutes + ' Minuten';
                    }
                } else {
                    const hours = Math.round(difference / 3600);
                    age = hours + ' Stunden';
                }
            } else {
                age  = created.toLocaleDateString(undefined, {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'});
                age += ', ' + created.toLocaleTimeString(undefined, {hour: "2-digit", minute: "2-digit"}) + ' Uhr';
            }
        }
    };

    const createDebugSection = function() {
        const eventHeader = document.getElementById('header').querySelector('h3');
        if (eventHeader) {
            const debugHeader = document.createElement('h3');
            debugHeader.appendChild(document.createTextNode('Debugging'));
            eventHeader.before(debugHeader);

            const debugDiv = document.createElement('p');
            debugDiv.classList.add('debugging');
            eventHeader.before(debugDiv);

            const uuidLabel = document.createElement('strong');
            uuidLabel.appendChild(document.createTextNode('UUID'));
            debugDiv.appendChild(uuidLabel)
            const uuid = document.createElement('pre');
            uuid.appendChild(document.createTextNode(party.uuid));
            debugDiv.appendChild(uuid);

            const ageLabel = document.createElement('strong');
            ageLabel.appendChild(document.createTextNode('Alter:'));
            debugDiv.appendChild(ageLabel);
            debugDiv.appendChild(document.createTextNode(' ' + age));
        }
    };

    getMetaTags();
    initVariablesFromMeta();
    isDebugEnabled && console.log('Development & debugging mode enabled.');
    isDebugEnabled && console.log('This is a report for party ' + party.uuid + '.');
    isDebugEnabled && console.log(creationMessage);
    isDebugEnabled && createDebugSection();
});
