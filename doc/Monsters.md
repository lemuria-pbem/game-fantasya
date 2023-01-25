# Monster

Es gibt verschiedene Monsterarten, die jeweils ein spezifisches Verhalten
zeigen.

## Monsterarten

- Bär - Wildtier, normalerweise friedlich
- Baumhirte - baumähnliches intelligentes Wesen
- Elementar - verschiedene Unterarten, kann durch Zauber beschworen werden
- Ghoul - abstoßende untote Kreatur, normalerweise friedlich
- Greif - lebt in größeren Gruppen zurückgezogen in Gletscherregionen 
- Kobold - hinterlistiges Wesen, bekannt für ihr diebisches Verhalten
- Krake - aggressiver Meeresbewohner
- Skelett – untoter Humanoider, kann bewaffnet sein
- Wolf – Wildtier, im Winter angriffslustig, ansonsten scheu
- Zombie - untote Kreatur mit Verlangen nach Frischfleisch

### Monsterparteien

Alle Monsterarten gehören derselben Partei [m] an. Zombies bilden eine Ausnahme:
Damit sie andere Monster wie Wildtiere oder Kobolde angreifen können, gehören
sie einer eigenen Partei an, den Zombies [z].

## Monsterverhalten

Alle Monster haben ein typisches arteigenes Verhalten, das sich aus einer oder
mehreren Aktionen zusammensetzt.

### Aktionen

- Angreifen - entdeckte Feinde werden angegriffen
- Ausschau halten - zufälliges Bewachen einer Region
- Bewachen - dauerhaftes Bewachen einer Region
- Bewohnen - dauerhaftes Aufhalten in einer Heimatregion
- Feindsuche - Suche nach feindlichen Einheiten
- Jagen - Suche nach leichter Angriffsbeute
- Vermehren - Erzeugen neuer Monstergruppen
- Taschendiebstahl - entdeckte Feinde werden bestohlen
- Umherstreifen - umherwandern in der Region oder in eine Nachbarregion

### Verhalten

#### Bär

_Umherstreifen - Vermehren_

Ein Bär streift stetig umher und wandert auf diese Weise im Laufe der Zeit über
den ganzen Kontinent. Dabei bevorzugt er Wälder, betritt aber gelegentlich auch
Gebirge, Hochländer oder Ebenen. Solange man ihn in Ruhe lässt, bleibt er
friedlich.

#### Baumhirte

_Umherstreifen - Ausschau halten_

Baumhirten sind scheue Wesen. Man trifft relativ selten auf sie, meistens in den
dichten Wäldern. Aber auch in angrenzenden Ebenen und Sümpfen kann man sie
beobachten. Sie entfernen sich nie weit von ihrer Heimat, den Waldgebieten, und
hin und wieder machen sie einige Wochen Rast, währenddessen sie Wache halten und
die Einheiten der Spieler bei deren Arbeit stören. Solange man sie nicht
angreift, hat man ansonsten jedoch nichts zu befürchten.

#### Elementarwesen

Magier können mit dem Kampfzauber „Elementarwesen” ein ebensolches herbeirufen.
Dabei erscheint je nach Umgebung, in der der Magier sich befindet, ein Exemplar
der jeweiligen Unterart bzw. des vorherrschenden Elementes:

- Ebene, Gebirge: Erdelementar
- Hochland, Wüste: Feuerelementar
- Gletscher, Wald: Luftelementar
- Ozean, Sumpf: Wasserelementar

In der Natur wurden noch nie Elementare beobachtet. Es ist daher fraglich, ob
sie überhaupt den Wunsch verspüren, ihr Element zu verlassen, wenn sie nicht
durch einen Zauber dazu gezwungen werden.

#### Ghoul

_Bewohnen_

Ghoule sind wirklich sehr abstoßende kleine Kreaturen, aber ansonsten harmlos.
Sie leben in den Sümpfen und man sieht sie nur selten. Man sagt ihnen nach, dass
sie in kalten, mondlosen Nächten durch die Dörfer der Menschen streifen und
kleine Kinder stehlen, aber im Gegensatz zu Kobolden bleiben sie entgegen aller
Gerüchte unter sich.

#### Greif

_Bewohnen_

In den zerklüfteten Gebirgen, dort wo es Gletscher gibt, kann man häufig größere
Gruppen von Greifen beobachten, die sich das ganze Jahr über dort aufhalten und
ihre Horste errichten. Greife sind riesige Vögel, die messerschafte Krallen an
ihren Klauen haben und ihre Beute mit dem Schnabel in Nullkommanichts in Fetzen
schneide – man will ihnen nicht als Gegner im Kampf gegenüberstehen.

_Der Greif ist nicht als Monster, sondern als Tier-Ressource implementiert._

##### Greifeneier

Glücklicherweise leben sie derart zurückgezogen, dass von ihnen keine Gefahr
ausgeht – es sei denn, man versucht ihre Eier zu stehlen, denn Gerüchten zufolge
soll es möglich sein, gerade geschlüpfte Greifenküken abzurichten, um sie als
fliegende Reittiere zu benutzen. Wer sich beim Eierdiebstahl ungeschickt, wird
schnell ihre nächste Beute werden.

_Das Greifenei ist als Ressource implementiert; der Greifenangriff ist als
Ereignis implementiert, bei dem eine temporäre Monstereinheit erzeugt wird._

#### Kobold

_Umherstreifen - Vermehren - Feindsuche - Taschendiebstahl_

Kobolde organisieren sich in Gruppen, oft zu Dutzenden, und wandern in beinahe
alle Regionen auf dem Kontinent. Einzig Gletscher meiden sie, denn dort gibt es
selten etwas zu holen: Ihre Lieblingsbeschäftigung ist nämlich das Bestehlen von
reichen Einheiten, die nicht auf ihre Geldsäckchen achtgeben. Kobolde haben ein
gewisses Talent für Tarnung, und solange sie unentdeckt ihre Opfer bestehlen
können, bleiben sie an ihrem jeweiligen Ort. Gibt es nichts mehr zu holen,
ziehen sie weiter.

#### Krake

_Umherstreifen - Vermehren - Angreifen_

In den endlosen Weiten der Ozeane trifft man manchmal auf einzelne Kraken,
selten auch mal auf kleine Gruppen von ihnen. Diese Begegnungen sind unter den
seefahrenden Völkern gefürchtet, denn dabei kann es vorkommen, dass diese
neugierigen Tiere aufdringlich werden und sich den Schiffen nähern. Wenn man
dabei versucht, sie abzuwehren, werden die Tiere dies als Angriff interpretieren
und sich verteidigen. Kleine Boote scheinen die Kraken dabei nicht zu
interessieren, große Segler oder Ruderschiffe ziehen sie jedoch magisch an.

#### Skelett

_Bewachen - Feindsuche - Angreifen_

Skelette sind Untote. Einst waren sie Menschen oder Angehörige anderer
humanoider Völker, und wurden nach ihrem Tod zu untotem Leben erweckt. Dabei
behalten sie die meisten Fähigkeiten, die sie in ihrem Leben hatten, sie können
also starke Gegner im Kampf sein - und Kampf ist auch das einzige, nach dem es
sie giert. Skelette werden stets den selben Ort bewachen und jede erblickte
Spielereinheit erbarmungslos angreifen.

#### Wolf

_Umherstreifen - Vermehren - Jagen_

Wölfe streifen in Rudeln durch die Wildnis. Dabei meiden sie Sümpfe, Gletscher
oder bewohnte Gegenden, sind scheu und halten sich bevorzugt in den Wäldern auf.
Im Winter treibt sie der Hunger aber manchmal auch in die Nähe der Siedlungen,
und dann greifen sie auf der Jagd nach Beute Alleinreisende oder kleine Gruppen
an, solange sie in der Überzahl sind.

#### Zombie

_Umherstreifen - Feindsuche - Angreifen_

Zombies sind Untote ähnlich den Skeletten, doch haben sie noch verrottendes
Fleisch auf den Knochen und sind normalerweise nicht mehr intelligent. Sie
werden einzig von einem unstillbaren Hunger getrieben und streifen durch die
Ebenen, Hochländer, Sümpfe und Wüsten, auf der Suche nach ihren nächsten Opfern,
die sie ausnahmelos angreifen. Sie greifen sogar Kobolde, Bären und andere Tiere
an, um ihren Hunger zu stillen. Gebirgige Gegenden und Wälder meiden sie
dagegen.
