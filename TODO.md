# Version 1.0

Lemuria 1.0 wurde am 6. August 2022 veröffentlicht.

## Fehler/Verbesserungen

- …

## Feature: Märkte

In Regionen mit einem Turm kann ein Markt gebaut werden. Händlereinheiten können
den Markt betreten und preisen damit automatisch ihre Waren an. Um als Kunde mit
einem Händler zu handeln, muss der Markt nicht betreten werden.

Der Herrscher der Region legt die Bedingungen für den Handel auf Märkten fest:

- Er kann eine Marktgebühr für Händler festlegen.
- Er kann verbieten, dass bestimmte Waren gehandelt werden.
- Er kann über das Allianzrecht "Betreten" Händler vom Markt ausschließen.
- Er kann über das Allianzrecht "Markt" Kunden vom Handel ausschließen.

### Angebot und Nachfrage

Händler können Waren anbieten und Gesuche veröffentlichen. Dabei kann eine fixe
Menge zu einem festen Preis oder in einem Preisbereich bestimmt werden, oder
es wird ein Stückpreis für eine maximale Menge definiert. Angebote und Gesuche
erhalten beim Erstellen eine Nummer, die beim Handel und zum Beenden verwendet
wird.

- BEENDEN Nummer

#### Fix-Angebot

Es wird eine feste Menge an Ware gegen einen nicht verhandelbaren Preis
gehandelt.

- ANGEBOT n Ware p Preis
- NACHFRAGE n Ware p Preis

#### Preisbereich mit Feilschen

Eine feste Menge wird mit einer Preisvorstellung festgelegt, der Kunde kann
versuchen zu handeln.

- ANGEBOT n Ware p-q Preis
- NACHFRAGE n Ware p-q Preis

#### Stückpreis

Wenn die Menge variabel und der Preis fest gewählt wird, handelt es sich um ein
Stückpreis-Angebot.

- ANGEBOT n-m Ware p Preis
- NACHFRAGE n-m Ware p Preis

#### Stückpreis mit Feilschen

Werden Menge und Preis variabel gewählt, kann der Kunde versuchen, die
gewünschte Menge zu einem Wunschpreis zu handeln.

- ANGEBOT n-m Ware p-q Preis
- NACHFRAGE n-m Ware p-q Preis

### Gerüchte austauschen

#### BESUCHEN

Kunden können Händler besuchen, um mit ihnen Gerüchte auszutauschen.

#### GERÜCHT

Händler können mehrere kurze Texte festlegen, die sie den Besuchern erzählen.
(Kunden können mit den besuchten Händlern über das herkömmliche BOTSCHAFT in
Kontakt treten.)

BESUCHEN und GERÜCHT funktionieren auch dann, wenn ihnen der Handel verboten
ist. Auf diese Weise kann ein Schwarzmarkt organisiert werden, um die
Handelsbeschränkungen zu unterlaufen und über GEBEN-Befehle dennoch Waren
auszutauschen.

### Handel mit NPC

Auf Basis diese Märkte-Features können NPC-Händler umgesetzt werden. 

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
