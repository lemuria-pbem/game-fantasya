# Lemuria

Lemuria ist ein „Play-by-eMail", wird also über E-Mail gespielt, und ist
inspiriert durch Eressea und Fantasya.

Der Programmcode ist in Bibliotheken aufgeteilt. Diese Bibliothek beinhaltet
ein Testspiel mit einer kleinen Spielwelt und wenigen Parteien und dient als
Testumgebung für die Entwicklung sowie als Beispielimplementierung für eine
Spielinstanz.

## Entwicklungsmodell

Mit dem Release der Version 1.0 ändert sich die Organisation der Git-Branches.

### Module

- Änderungen werden im Branch „master“ des jeweiligen Moduls umgesetzt.
- Aufwändigere Features werden in eigenen Branches umgesetzt, um „master“ für
  Fehlerkorrekturen freizuhalten.
- Releases der jeweiligen Module erfolgen über ein Git-Versionstag.

### Spiel

- Die Entwicklung erfolgt im Branch „master“.
- Neue Features werden versioniert in einem Branch „release/1.x“ veröffentlicht
  und mit einem Git-Versionstag versehen.
