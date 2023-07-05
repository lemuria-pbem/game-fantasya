# Version 1.4

Lemuria 1.4 wurde am 28. Juli 2023 veröffentlicht.

## Fehler/Verbesserungen

- Platzberechnung bei BETRETEN und VERLASSEN verbessern

## Weiterentwicklung des Spielkonzepts

Die angedachte Weiterentwicklung hat das generelle Ziel, Mikromanagement zu
reduzieren. Dabei könnten viele Befehle und Konzepte aus Fantasya verworfen
und stattdessen vom Spieler Rahmenbedingungen vorgegeben werden, aus denen
„Kontingente“ von arbeitenden Bauern berechnet werden, die unter der Anleitung
von „Meistereinheiten“ mit Erfahrung in bestimmten Talenten effektiver arbeiten
können. Transporte können durch Einrichtung von Transportrouten automatisiert
werden, und Handel kann analog über Handelsrouten organisiert werden.

Weiterentwicklungskonzepte könnten auch als optional nutzbare Aufsätze auf die
bestehende Spielmechanik geplant werden.

### Reiche

Die Einführung des Reiche-Konzepts ist für die Version 1.4 vorgesehen.

## Ideen

### Ereignisse

- Ereignis "Juwelen gefunden" beim Bergbau in Gebirgen/Gletschern ohne Luxuswaren
- Ereignis "Tierangriff" wandelt Ressourcen-Tiere in Monstergruppen um
  - Elefantenaufruhr
  - Greifenwacht
  - Wespenangriff
- Umweltereignisse
  - „Trockenheit“ lässt Flüsse und Kanäle austrocknen
  - „Erdrutsch“ zerstört Straße

### Gebäude

- neue Gebäude, die Arbeitsplätze verdoppeln
  - Gebirge, Gletscher: Pilzhöhle
  - Sumpf: Elefantengehege
  - Wald: Jagdhaus
  - Wüste: Oase
- Forsthaus: stoppt Bauernvermehrung, erhöht Wachstum
- Kräuterhütte: +1, stoppt Schrumpfen im Winter, automatisches Forschen
- Gewächshaus: Anbau regionsfremder Kräuter
- besondere Gebäude/Orte wie Ruinen, Höhlen oder Geschäfte
  - BESUCHEN zum Handel mit NPC-Ladenbesitzer
  - Steinkreis als Portal für Magier

### Handel und Wirtschaft

- Silber für Unterhalt mit Nahrung ersetzen
- MACHEN -<Grenze> <Ressource> für Bestandshaltung
- neue Rohstoffe
  - Kohle als Hilfsstoff für Schmelze
- Rohstoffe als Vorstufe der Weiterverarbeitung einführen
  - Bäume für Holz, Kohle (in Sägewerk, Köhlerei)
  - Felsen für Stein (in Steinbruch)
  - Erz für Eisen (in Schmelze)
  - Argent für Silber (in Schmelze)
- neue Luxuswaren
  - Goldnuggets (produziert in Gletscherregionen)
  - Perlen (produziert in Küstenebenen an Buchten)
  - Tabak (produziert in Ebenen/Hochländern)
  - Whisky (produziert in Hochländern mit Wald)
- neue Gebäude
  - Schmelze verarbeitet Goldnuggets zu Gold; braucht Holz
  - Schmelze verarbeitet Erz/Argent zu Eisen/Silber; braucht Kohle
- neue Talente
  - Metallurgie für Schmelze
- neue Gegenstände
  - Fackel (per BENUTZEN Fackel; als Waffe oder für Dungeons)
  - Öllampe (benötigt Öl; für Dungeons)
- Luxuswarenangebot neu definieren, wenn der erste Bauer eine Region besiedelt
- Besteuerung bei Nutzung fremder Infrastruktur
  - Handelssteuer an den Besitzer der größten Burg
  - Maut für Wagengespanne an den Besitzer der größten Burg

### Kampf

- neue Waffen
  - Dolch (notwendig für Attentate)
- Taktik ermöglicht Strategien im Kampf, STRATEGIE
- Spionage + Tarnung ermöglicht Attentate auf Einzelpersonen
- Monsterrasse bestimmt maximalen Übermacht-Faktor
- Effekt "Vergiftung" durch Monster wie Spinnen oder Skorpione oder Waffen
- Seekampf/Piraterie
  - VERSENKEN (Schiff vor Entern bewahren)
  - EROBERN (fremden Hafen einnehmen)
- SABOTIEREN (Spion versenkt Schiff)
- AUSRAUBEN überfällt Gegner ohne Kampfabsicht
- BELAGERN von Straßen, um Reisen zu verhindern

### Magie und Alchemie

- neue Zauber
  - Zombies erwecken (Kampf oder Regionszauber)
  - Ruhe in Frieden (Zombieinfektion verhindern)
  - Gegengift (Kampf)
- neue Tränke
  - Fackel (aus Holz, Öl; erzeugt Waffe)
  - Waffengift (für Kampf), benötigt Giftblase

### Monster

- neue Monsterrassen
  - Riesenskorpion (Wüste, Hochland), Trophäe: Stachel
  - Riesenspinne (Wald), Trophäe: Giftblase
  - Wespen (Ebene, Wüste), mit Feuer zu bekämpfen, sterben im Winter

### NPC

- Standardverhalten kann mit Befehlsliste überschrieben werden
- Gerüchte durch NPC oder Bauern
  - Monstervorkommen in Nachbarregionen
  - Kämpfe in Nachbarregionen
  - Gebühren in Nachbarregionen
- Händlercharaktere mit Geschäft
- fahrende Händler (Alchemisten, Magier, Luxuswaren)
- Spedition übernimmt Transporte für Silber
- Räuber
- Söldner, kann angeheuert werden
- Piraten
- Quest-Auftraggeber

### Quests

- mit bestimmtem Ziel
  - geraubte Beute wiederbeschaffen, Provision 
  - geraubtes Unikum wiederbeschaffen
  - entführte Person befreien
  - Räuberbande bekämpfen
  - Wespennest ausräuchern
  - Schiffspassage
  - Warentransport
    - Vertrauen aufbauen, um wertvolle Transporte zu bekommen
- offenes Ende
  - Dungeon erkunden/Schatzsuche
  - Monster bekämpfen, Trophäen eintauschen

### Reisen und Seefahrt

- VORGABE Erkunden für Reisen in unbekannten Regionen
  - Erkunden Nicht: keine alternativen Richtungen
  - Erkunden: benachbarte Richtungen versuchen
  - Erkunden Anlegen: Schiffe sollen an Land gehen
  - Erkunden Ablegen: Schiff sollen an Land gehen und gleich wieder ablegen
- Gepanzerte Schiffe, Eisenschiffe, Dampfmaschine

### Statistik

- Einkommensprognose am Rundenende
- automatisches Verfolgen von Monstern und Fremdeinheiten
  - Meldung bei Wiederentdeckung markierter Einheiten
  - Parteimeldung für gesichtete Monster

### Umgebung/Welt

- Kanäle analog zu Straßen modellieren (Gebäude „Kanal“ ersetzen)
- Flüsse als natürliche Kanäle modellieren
  - Schleuse als Zollgebäude

### Verschiedenes

- LERNEN <Talent> <Stufe>
- ALTERNATIVE für automatische Alternativbefehle
  - LEHREN ohne Schüler
  - LERNEN <Talent> <Stufe>
  - MACHEN -<Grenze> <Ressource>
- VORLAGE-Variante für Rotation von Befehlen
- TRANSPORTIEREN für schnelle Einheitentransporte mit Fuhrwerken
- Textreport im Markdown-Format
