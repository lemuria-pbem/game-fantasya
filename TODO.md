# Version 1.2

Lemuria 1.2 wurde am 24. Dezember 2022 veröffentlicht.

## Fehler/Verbesserungen

- …

### Version 1.3

- Ereignisse
  - Ereignis "Koboldseuche" hebt Tarnung auf
  - Herumstreunende Zombies infizieren Bauern, die zu Zombies werden
  - Alte Tiermonster sterben und hinterlassen verwesende Kadaver
    - Kraken werden an den Strand gespült
    - Bären verenden, Reißzähne können erbeutet werden
+ Ausdauer erhöht Tragkraft um Faktor ∜TW
+ Kampfausrüstung sinnvoller verteilen (z.B. gemischte Waffen)
+ Handelserweiterungen
  + HANDEL x <Anzahl> ohne Angabe des Guts
  + HANDEL x * / HANDEL x Alles (alles verfügbare handeln)
  + HANDEL x 10-20 (bis 20 Stück, mindestens aber 10)
  + WIEDERHOLEN [Alles]
  + MENGE, PREIS verändern Handel
+ Mehr Regionsstatistik
  + Anzahl Einheiten
  + Anzahl Personen
  + Versorgungsreserve (Silberpool)
+ Auswertung: in Talentliste Einheiten-TW in Klammern anzeigen
+ ID-Änderung durch NUMMER an Defaultbefehle weitergeben
- Übersetzungsgrammatik vereinheitlichen
+ Magellan-Konfiguration flexibilisieren
  + Konfigurationsoptionen recherchieren
+ Report-Download konfigurierbar machen (über Webseite)

## Ideen

### Ereignisse

- Ereignis "Juwelen gefunden" beim Bergbau in Gebirgen/Gletschern ohne Luxuswaren
- Ereignis "Tierangriff" wandelt Ressourcen-Tiere in Monstergruppen um
  - Elefantenaufruhr
  - Greifenwacht
  - Wespenangriff
- Umweltereignisse

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

- neue Luxuswaren
  - Goldnuggets (produziert in Gletscherregionen)
  - Perlen (produziert in Küstenebenen an Buchten)
  - Tabak (produziert in Ebenen/Hochländern)
  - Whisky (produziert in Hochländern mit Wald)
- neue Gebäude
  - Schmelze (verarbeitet Goldnuggets zu Gold; braucht Holz)
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
- Räuber
- Söldner, kann angeheuert werden
- Piraten
- Quest-Auftraggeber

### Quests

- mit bestimmtem Ziel
  - geraubtes Unikum wiederbeschaffen
  - entführte Person befreien
  - Wespennest ausräuchern
- offenes Ende
  - Dungeon erkunden/Schatzsuche
  - Monster bekämpfen, Trophäen eintauschen

### Seefahrt

- Gepanzerte Schiffe, Eisenschiffe, Dampfmaschine

### Statistik

- Einkommensprognose am Rundenende
- automatisches Verfolgen von Monstern und Fremdeinheiten
  - Meldung bei Wiederentdeckung markierter Einheiten
  - Parteimeldung für gesichtete Monster

### Umgebung/Welt

- Flüsse als natürliche Kanäle modellieren
  - Schleuse als Zollgebäude

### Verschiedenes

- VORLAGE-Variante für Rotation von Befehlen
- Einstellen der erwünschten Reportformate
  - Textreport im Markdown-Format

## Weiterentwicklung des Spielkonzepts

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

### Handel und Wirtschaft

- Silber für Unterhalt mit Nahrung ersetzen
- Rohstoffe als Vorstufe der Weiterverarbeitung einführen
  - Bäume für Holz, Kohle (in Sägewerk, Köhlerei)
  - Felsen für Stein (in Steinbruch)
  - Erz für Eisen (in Schmelze)
  - Argent für Silber (in Schmelze)
- neue Rohstoffe
  - Kohle als Hilfsstoff für Schmelze
- Schmelze verarbeitet Erz/Argent zu Eisen/Silber; braucht Kohle)
