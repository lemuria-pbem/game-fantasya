# Version 1.1

Lemuria 1.1 wurde am 23. September 2022 veröffentlicht.

## Fehler/Verbesserungen

- …

## Version 1.2

### Kampf

- Kampf in Gebäuden und auf Schiffen begrenzen

### Seefahrt

- Hafenbenutzungsgebühr an den Hafenmeister
- Hafen erlaubt Ablegen in benachbarte Richtungen
- Kanalbenutzungsgebühr an den Besitzer
- Kanal erlaubt Ablegen in beliebige Richtungen

### Umgebung/Welt

- leere Regionen benennen
- Weltumseglung ermöglichen

### Verschiedenes

- Reise-Berechnung vereinfachen

### Update PHP 8.2
 
- Enums in Konstanten
- Konstanten in Traits
- schreibgeschützte Klassen
- Random
- ksort/krsort prüfen

### Update CSS

- IDs in Report ohne Serifen ausgeben
- Statistik-Badges verbessern

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
