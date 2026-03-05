# CineMatch - What do we watch?

## Grundidee
Jeder kennt die Situation das man mit Freunden einen Film shauen möchte und endlos diverse Streaming Plattformen durchsucht und sich einfach nicht einigen kann. CineMatch ist die Lösung auf dieses Problem. Man kann sich mit freunden zusammen setzen und durch Filme die man zuvor schon als interresant gekennzeichnet durch schauen und zum schauen vorschlagen. So kommt man schnell und einfach auf einen Film einfach indem geschaut wird auf was die meisten Leute Lust haben.


## USP
 - spezifische Lösung für ein Alltagsproblem
 - Plattformübergreifend
 - man wählt aus eigenen Listen was Heißt das man nicht mit uninterresanten Filmen beschäftigt ist

## UI & UX
Die Website soll primär fürs Handy entwickelt sein, einfach weil das in der praxis der praktischste und häufigste Anwendungsfall sein wird.

Die Website soll clean und modern sein. Dunkel gehalten mit fokus auf den Nutzen.

Grob hab ich mir gedacht das ein benutzer ein *Watchparty* erstellen kann, in die er dann entweder leute hinzufügt oder Freunde über einen Code joinen können. Wenn man einer *Watchparty* joined kann man eine Liste von Filmen wie zB. seine *Watchlist* hinzufügen. Wenn alle bereit sind werden alle filme aus den verschiedene Listen genommen und durchgemischt. Die User bekommen nun einen Film nach dem anderen angezeigt und können entscheiden ob sie diesen *Liken* also gerne schauen würden oder nicht. Sobald jeder alle filme durchgearbeitet hat sieht man eine Auswahl der am meisten gelikten Filme und kann isch nun einen Aussuchen. Falls man sich jz immernoch nicht entscheiden kann hilft ein zufallsgenerator.

GgFs. kann man den Film nach dem anschauen bewerten und diese Bewertung später einsehen. Falls dafür die Zeit bleibt.

Es wäre vorteilhaft wenn man nach einer Streaming plattform filtern könnte.

Man kann sich inspirieren lassen durch verweise auf andere Websites die filme vorstellen. Weiters kann man durch Filme Browsen und schauen was freunde geschaut haben.

## Coder Plan

Die daten für die einzelnen Filme hole ich mir von det TMDB-API, da würde ich gerne noch mit ihnen besprechen wie ich das umsetzte das das zum projekt am besten passt.

Geplante Technologien sind sonst der gesamte bereits vorgegebene Techstack und möglicher Weise GSAP falls ich das für zB. swipe animationen verwenden werde.

Als Tabellen der Datenbank hätte ich mir ungefähr folgende vorgestellt:
  - User
  - List
  - Movie
  - UserList (zwischen Tabelle)
  - ListMovie (zwischen Tabelle)
  - WatchParty
  - PartyMember (zwischen Tabelle)
  - PartyList (zwischen Tabelle)
  - PartyMovie/Swipe

  Alle Entscheidungen der einzelnden User werden wärend dem "entscheidungs" Teil in der Datenbank gespeichert, um am Ende die Auswertung korrekt machen zu können. Das führt glaube ich zu sehr schnell sehr viel Daten in der Datenbank + den ganzen gespeicherten Filmen, ich hoffe das ich damit nicht an technische Grenzen stoße.

Ich glaub ich kriege alle Funktionen so wie Lsten erstellen und einsehen, Durch filme browsern, Freunde hinzufügen, Watchparty starten/beitreten  Profil bearbeiten in 4-6 Pages hin.

