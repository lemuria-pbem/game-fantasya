# Version 1.0

Lemuria 1.0 wurde am 6. August 2022 veröffentlicht.

## Fehler/Verbesserungen

- Marktgebühren bezahlen
- Einkommensprognose am Rundenende in Statistik aufnehmen
- Parteimeldung für gesichtete Monster

## Version 1.1: Märkte

In Regionen mit einem Turm kann ein Markt gebaut werden. Händlereinheiten können
den Markt betreten und preisen damit automatisch ihre Waren an. Um als Kunde mit
einem Händler zu handeln, muss der Markt nicht betreten werden.

Der Marktaufseher als Besitzer des Marktes legt die Bedingungen für den Handel
fest:

Er kann eine Marktgebühr für Händler festlegen. Diese ist entweder pro Person
in der Händlereinheit zu entrichten oder als Prozentsatz der gehandelten Waren.

- STEUERN 100 Silber
- STEUERN 10 %

Er kann verbieten oder erlauben, dass bestimmte Waren gehandelt werden.

- VERBIETEN Alles
- VERBIETEN Elefant Streitaxt Schilde Rüstungen
- ERLAUBEN Alles
- ERLAUBEN Holzschild Lederrüstung

Er kann über das Allianzrecht "Markt" Händler und Kunden vom Markt ausschließen.
Händler ausgeschlossener Parteien können den Markt nicht betreten und verlassen
diesen, falls sie dort Händler sind. Ausgeschlossene Kunden Können keinen Handel
treiben.

- HELFEN 0 Markt
- HELFEN 123 Markt Nicht

### Angebot und Nachfrage

Händler können Waren anbieten und Gesuche veröffentlichen. Dabei kann eine fixe
Menge zu einem festen Preis oder in einem Preisbereich bestimmt werden, oder
es wird ein Stückpreis für eine maximale Menge definiert. Angebote und Gesuche
erhalten beim Erstellen eine Nummer, die beim Handel und zum Beenden verwendet
wird.

- HANDEL Nummer …
- BEENDEN Nummer

Angebote und Gesuche sind einmalig und werden beim Handel automatisch beendet.
Dies kann man über einen Wiederholen-Befehl verhindern und reaktivieren.

- WIEDERHOLEN Nummer
- WIEDERHOLEN Nummer Nicht

#### Fix-Angebot

Es wird eine feste Menge an Ware gegen einen nicht verhandelbaren Preis
gehandelt.

- ANGEBOT n Ware p Preis
- NACHFRAGE n Ware p Preis

- HANDEL Nummer

#### Preisbereich mit Feilschen

Eine feste Menge wird mit einer Preisvorstellung festgelegt, der Kunde kann
versuchen zu handeln.

- ANGEBOT n Ware p-q Preis
- NACHFRAGE n Ware p-q Preis

- HANDEL Nummer x

#### Stückpreis

Wenn die Menge variabel und der Preis fest gewählt wird, handelt es sich um ein
Stückpreis-Angebot.

- ANGEBOT n-m Ware p Preis
- NACHFRAGE n-m Ware p Preis

- HANDEL Nummer x

#### Stückpreis mit Feilschen

Werden Menge und Preis variabel gewählt, kann der Kunde versuchen, die
gewünschte Menge zu einem Wunschpreis zu handeln.

- ANGEBOT n-m Ware p-q Preis
- NACHFRAGE n-m Ware p-q Preis

- HANDEL Nummer x y

### Gerüchte austauschen

#### Gerüchte

Händler können mehrere kurze Texte festlegen, die sie den Besuchern erzählen.

- GERÜCHT Text…

Kunden können mit den besuchten Händlern über das herkömmliche BOTSCHAFT in
Kontakt treten.

#### Besuchen

Kunden können Händler besuchen, um von ihnen Gerüchte zu erfahren.

- BESUCHEN Einheit

BESUCHEN funktioniert auch dann, wenn den Kunden der Handel verboten ist. Auf
diese Weise kann ein Schwarzmarkt organisiert werden, um die
Handelsbeschränkungen zu unterlaufen und über GEBEN-Befehle dennoch Waren
auszutauschen.

## Version 1.2

- Update PHP 8.2

## Ideen

### Ereignisse

- Ereignis "Juwelen gefunden" beim Bergbau in Gebirgen/Gletschern ohne Luxuswaren
- Ereignis "Tierangriff" wandelt Ressourcen-Tiere in Monstergruppen um
- Umweltereignisse

### Gebäude

- neue Gebäude, die Arbeitsplätze verdoppeln
  - Gebirge: Pilzhöhle
  - Sumpf: Elefantengehege
  - Wald: Jagdhaus
  - Wüste: Oase
- besondere Gebäude/Orte wie Ruinen, Höhlen oder Geschäfte
  - BESUCHEN zum Handel mit NPC-Ladenbesitzer
  - Steinkreis als Portal für Magier

### Handel und Wirtschaft

- fahrende NPC-Händler
- automatischer Beschreibungstext mit Handelsangeboten für Einheit, Gebäude oder Region
- Luxuswarenangebot neu definieren, wenn der erste Bauer eine Region besiedelt
- Besteuerung bei Nutzung fremder Infrastruktur
  - Hafenbenutzungsgebühr an den Hafenmeister
  - Kanalbenutzungsgebühr an den Besitzer
  - Handelssteuer an den Besitzer der größten Burg
  - Maut für Wagengespanne an den Besitzer der größten Burg

### Kampf

- Taktik ermöglicht Strategien im Kampf, STRATEGIE
- Monsterrasse bestimmt maximalen Übermacht-Faktor
- Seekampf/Piraterie
  - VERSENKEN (Schiff vor Entern bewahren)
  - EROBERN (fremden Hafen einnehmen)
- SABOTIEREN (Spion versenkt Schiff)
- AUSRAUBEN überfällt Gegner ohne Kampfabsicht

### Schifffahrt

- Gepanzerte Schiffe, Eisenschiffe, Dampfmaschine

### Umgebung/Welt

- Flüsse als natürliche Kanäle modellieren
  - Schleuse als Zollgebäude
- Weltumseglung ermöglichen

### Verschiedenes

- neue Monsterrassen
- Gerüchte durch NPC oder Bauern
  - Monstervorkommen in Nachbarregionen
  - Kämpfe in Nachbarregionen
- VORLAGE-Variante für Rotation von Befehlen
- Einstellen der erwünschten Reportformate
  - Textreport im Markdown-Format

### Weiterentwicklung des Spielkonzepts

Die angedachte Weiterentwicklung hat das generelle Ziel, Mikromanagement zu
reduzieren. Dabei würden viele Befehle und Konzepte aus Fantasya verworfen und
stattdessen vom Spieler Rahmenbedingungen vorgegeben, aus denen „Kontingente“
von arbeitenden Bauern berechnet werden, die unter der Anleitung von
„Meistereinheiten“ mit Erfahrung in bestimmten Talenten effektiver arbeiten
können. Transporte können durch Einrichtung von Transportrouten automatisiert
werden, und Handel kann analog über Handelsrouten organisiert werden.

- ALTERNATIVE für LERNEN ohne Lehrer
- FORST, HERDE für automatisches Baumfällen und Dressieren
- STEUERN auf Märkten legt einen Steuersatz fest
