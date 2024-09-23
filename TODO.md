# Version 1.5

Lemuria 1.5 wurde am 2. März 2024 veröffentlicht.

## Fehler/Verbesserungen

- Notice: tempnam(): file created in the system's temporary directory (website/src/Service/TempDirService.php:22)
- Kampfzauber (Vorbereitung) trotz Friedenslied
- Kampffortsetzung (nur Magie) ohne Kämpfer
- Kampfzauber-Meldung stoppen, wenn Aura fehlt
- Übersichtsseite für HTML-Auswertung
- Ersetzen der Zugriffe zwischen Webseite und Spiel durch APIs
- MACHEN Temp erzeugt zu hohe Nummern
- …

## Ideen

### Einheiten

- GEBEN <unit> entfernen
- PRÄFIX einführen
- ANREDE (Pronomen) einführen
  - NPC-Grammatik für Gerüchte verbessern
- Helden als Einpersoneneinheiten
  - Unikate als Ausrüstung im Kampf (legendäre Gegenstände)
- Einheiten gefangennehmen und Lösegeld fordern

### Ereignisse

- Ereignis "Tierangriff" wandelt Ressourcen-Tiere in Monstergruppen um
  - Elefantenaufruhr
  - Greifenwacht
  - Wespenangriff
- Umweltereignisse
  - „Trockenheit“ lässt Flüsse und Kanäle austrocknen
  - „Erdrutsch“ zerstört Straße
  - Erdbeben, Vulkanausbruch

### Parteien

- BANNER erweitern, um verschiedene Parteibanner zu definieren
- Banner auf Gebäuden und Schiffen hissen
  - Durchreisende und Leuchttürme sehen Banner statt Parteinummer
- Einheiten sammeln Banner anderer Parteien bei Begegnungen
- Banner-Grafiken für HTML-Report hinterlegen
  - Spieler können Grafiken für eigene Banner hochladen

### Reiche

- Erweiterung über Außenposten und Straßen
- Stationierung von TK in den Außenposten
- Zwischenlagerung der Waren in den Außenposten
- Ernte durch Fremdeinheiten verbieten

### Gebäude

- Steinkreis als Portal für Magier

### Unikate

- Magische Quelle
  - wird in Ebene gebaut und von Magiern aufgeladen
  - zieht Pegasi an und verwandelt Pferde
  - heilt Einheiten
  - vergrößert Mallorn-Chance
  - hält Bauern gesund, verringert Hunger, beschleunigt Heilung
- Standarte

### Handel und Wirtschaft

- Silber für Unterhalt mit Nahrung ersetzen
- Rohstoffe müssen zuerst erkundet werden (Prospektion mittels Bergbau)
- neue Rohstoffe
  - Kohle als Hilfsstoff für Schmelze
- Rohstoffe als Vorstufe der Weiterverarbeitung einführen
  - Bäume für Holz, Kohle (in Sägewerk, Köhlerei)
  - Mallorn als Holzfäller-Fund (in Sägewerken)
  - Felsen für Stein (in Steinbruch)
  - Erz für Eisen (in Schmelze)
  - Argent für Silber (in Schmelze)
  - Schwefel für Schwarzpulver (in Vulkanen)
  - Kohle für Schwarzpulver (in Hochländern)
  - Mithril als Bergbau-Fund (in Bergwerk)
- neue Luxuswaren
  - Goldnuggets (produziert in Gletscherregionen)
  - Perlen (produziert in Küstenebenen an Buchten)
  - Tabak (produziert in Ebenen/Hochländern)
  - Whisky (produziert in Hochländern mit Wald)
  - Bernstein (produziert in Wüsten/Hochländern an der Küste)
  - Salz (produziert in Gebirgen/Gletschern)
  - Honig (produziert in Wäldern neben Ebenen)
- neue Gebäude
  - Schmelze
    - verarbeitet Goldnuggets zu Gold; braucht Holz
    - verarbeitet Erz/Argent zu Eisen/Silber; braucht Kohle
- neue Talente
  - Metallurgie für Schmelze
- neue Gegenstände
  - Fackel (per BENUTZEN Fackel; als Waffe oder für Dungeons)
  - Öllampe (benötigt Öl; für Dungeons)
  - Schwarzpulver (benötigt Kohle, Schwefel, Salz; für Kanonen)
- Luxuswarenangebot neu definieren, wenn der erste Bauer eine Region besiedelt
- Besteuerung bei Nutzung fremder Infrastruktur
  - Handelssteuer an den Besitzer der größten Burg
  - Maut für Wagengespanne an Grenzblockierer

### Kampf

- neue Waffen
  - Dolch (notwendig für Attentate)
  - Kampfelch
  - Kanone (für Schiffe)
- Taktik ermöglicht Strategien im Kampf, STRATEGIE
- Spionage + Tarnung nutzen
  - Eindringen in befestigte Gebäude für Angriff
  - Attentate auf Einzelpersonen
- Effekt "Vergiftung" durch Monster wie Spinnen oder Skorpione oder Waffen
- Gebäudekampf: Eindringen in unbefestigte Gebäude ermöglichen
  - Abteilungsgröße durch freien Platz beschränkt, mindestens ein Kämpfer
- Seekampf/Piraterie
  - VERSENKEN (Schiff vor Entern bewahren)
  - EROBERN (fremden Hafen einnehmen)
- SABOTIEREN (Spion versenkt Schiff)

### Magie und Alchemie

- Kumulierbare Zauber prüfen
- neue Zauber
  - Gegengift (Kampf)
  - Quelle aufladen (lädt eine Magische Quelle auf)
- neue Tränke
  - Fackel (aus Holz, Öl; erzeugt Waffe)
  - Schlaftrunk (lässt Wachen oder Bewaffnete im Gebäude/Schiff einschlafen, erlaubt Reise/Betreten)
  - Waffengift (für Kampf), benötigt Giftblase
- Amulette

### Monster

- neue Monsterrassen
  - Riesenskorpion (Wüste, Hochland), Trophäe: Stachel
  - Riesenspinne (Wald), Trophäe: Giftblase
  - Wespen (Ebene, Wüste), mit Feuer zu bekämpfen, sterben im Winter

### NPC

- Bankiers
- Spedition übernimmt Transporte für Silber
- Fährleute
- Söldner, kann angeheuert werden
- Räuber
- Piraten
- Quest-Auftraggeber
- Lehrmeister und Spezialisten
- Prospektoren für die Rohstoffsuche
- Gefangene können nicht besucht werden
- BESUCHEN <Einheit> <Thema> für Informationsauskunft
  - NPC merken sich Besucher, auf Nachfrage mit Thema Begegnung erzählen

### Quests

- mit bestimmtem Ziel
  - geraubte Beute wiederbeschaffen, Provision 
  - entführte Person befreien
  - Räuberbande bekämpfen
  - Wespennest ausräuchern
  - Warentransport
    - Vertrauen aufbauen, um wertvolle Transporte zu bekommen
  - seltene Tiere beschaffen (Pegasi)
- offenes Ende
  - Dungeon erkunden/Schatzsuche
  - Monster bekämpfen, Trophäen eintauschen
- Dienstleistungen
  - Bankgeschäfte (Silber leihen/zurückzahlen, sparen/abheben)
  - Anheuern (NPC folgt Auftraggeber)
  - Fähren

### Reisen und Seefahrt

- Gepanzerte Schiffe, Eisenschiffe, Dampfmaschine
- Fliegende Einheiten können auf Schiffen landen
- Leuchttürme erhöhen Geschwindigkeit von Schiffen (analog Straßen)

### Statistik

- Einkommensprognose am Rundenende
- automatisches Verfolgen von Monstern und Fremdeinheiten
  - Meldung bei Wiederentdeckung markierter Einheiten
  - Parteimeldung für gesichtete Monster

### Umgebung/Welt

- Vulkane
- Kanäle analog zu Straßen modellieren (Gebäude „Kanal“ ersetzen)
- Flüsse als natürliche Kanäle modellieren
  - Schleuse als Zollgebäude

### Verschiedenes

- PHP 8.4
  - mb_ucfirst, mb_lcfirst, mb_trim verwenden
- Umbau/Verbesserung des Auswertungsablaufs
  - Befehlsparser/Delegates/Immediates in LemuriaTurn vereinfachen
  - Zweite Vorbereitungsphase einführen, um Abhängigkeiten besser zu lösen
  - Erstellung der Default-Befehle in eigener Phase
  - Komplex Aktivität/Alternative/Faulenzen/Simulation vereinfachen
  - Befehlsgruppen definieren, um Einheiten zu kategorisieren
    - Kampf/Kampfbeteiligung
    - Lehren/Lernen
    - Produktionsbefehl
    - Reisebefehl
  - Möglichkeit zur Stornierung von Befehlen für NPCs
- Refactoring
  - Reassignment-Methoden spezifischer benennen (Kollision)
  - Konzept von Entity mit Singleton-Logik vereinheitlichen (Unikate + Quests)
    - in lemuria-pbem/lemuria formalisieren
  - Event-Dispatcher einführen, allgemein mehr Observer-Patterns
    - PHP-Attribut zur Kennzeichnung, welche Events erzeugt werden
  - Befehlsstruktur vereinheitlichen
    - Voraussetzungen und Bedingungen für die Ausführung
    - Trigger und Folge-Events
  - Kampfinitialisierungslogik vereinfachen
  - Enum „Numerus“ für singular/plural einführen
  - NUMMER am Anfang auswerten, um neue IDs in Befehlsvorlagen zu verwenden
    - Globaler ID-Mapper für Engine und Scenario
    - ID in NPC-Befehlen für neu erstellte Einheiten anpassen
- Spielleitungsübersicht
  - Verwendung der Befehle - neue Features besser überprüfen
  - Parteigröße vergleichen
  - Welt-Situation
- Simulationsausgabe und -filterung verbessern
- BEWACHEN mit mehreren Richtungen teilt Einheit auf
- VORLAGE-Variante für Rotation von Befehlen
- TRANSPORTIEREN
  - für schnelle Einheitentransporte mit Fuhrwerken
  - für Nekromanten: beschworene Untote mitnehmen
- Aktivitätskapazität einführen - unterschiedliche Teilzeit-Aktivitäten erlauben
- Textreport im Markdown-Format
- Report im XML-Format für Fanalytics³

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
