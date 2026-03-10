# CityBuildPlugin (PocketMine API 5)

Dieses Plugin ist für einen kleinen **CityBuild-Server (ca. 5 Spieler, 3 GB RAM)** optimiert und läuft auf **PocketMine API 5**.

## Enthaltene Features

- Plotwelt mit fester Größe **2500 x 2500**.
- Plotgröße **25 x 25**.
- Plot-Commands:
  - `/plot auto` → freies Plot bekommen + Teleport.
  - `/plot claim` → aktuelles Plot claimen.
  - `/plot home [plotId]` → zu eigenem Plot teleportieren.
  - `/plot info` → Infos zum aktuellen Plot.
  - `/plot trust <Spieler>` und `/plot untrust <Spieler>`.
  - `/plot list` → eigene Plots.
- Rang-System mit Prefix im Chat + NameTag.
- Rang-Commands:
  - `/rang create <name> <prefix> [maxPlots]`
  - `/rang setprefix <rang> <prefix>`
  - `/rang setmaxplots <rang> <anzahl>`
  - `/rang permission <add|remove> <rang> <permission>`
  - `/rang setplayer <spieler> <rang>`
  - `/rang info <rang>`
  - `/rang list`
- Plot-Schutz (Bauen/Abbauen/Interagieren nur wenn erlaubt).

---

## Installation auf AxentHost (idiotensicher erklärt)

1. **Server stoppen** im AxentHost Panel.
2. In deinem Plugin-Projekt einen ZIP bauen:
   - Inhalt: `plugin.yml`, `src/`, `resources/`.
3. Im AxentHost-Dateimanager in den Ordner `plugins/` gehen.
4. ZIP hochladen und entpacken **oder** fertiges Plugin als PHAR hochladen.
5. Server starten.
6. Beim ersten Start erzeugt das Plugin automatisch:
   - `plugin_data/CityBuildPlugin/plots.yml`
   - `plugin_data/CityBuildPlugin/ranks.yml`
   - `plugin_data/CityBuildPlugin/players.yml`
7. In der Konsole prüfen, dass kein roter Fehler erscheint.

### Direkt danach ausführen (als OP)

1. `/rang create admin §c[Admin] 10`
2. `/rang permission add admin citybuild.plot.multiclaim`
3. `/rang setplayer DeinName admin`
4. `/plot auto`

Damit ist dein erster Rang + erstes Plot aktiv.

---

## Hinweise für Performance (3 GB RAM)

- Nicht zu viele weitere schwere Plugins gleichzeitig nutzen.
- View distance moderat halten (z. B. 6–8).
- Regelmäßig Backups machen.
- Bei nur 5 Spielern ist diese Plot-Logik sehr leichtgewichtig (YAML statt Datenbank).

---

## Berechtigungen

- `citybuild.rank.manage` (Standard: OP)
- `citybuild.plot.command` (Standard: alle)
- `citybuild.plot.admin` (optional, bypass für Plot-Schutz)
- `citybuild.plot.multiclaim` (als Rang-Permission für mehr Plots)

## Standard-Ränge

- `default` → Prefix `§7[Spieler]`, max 1 Plot
- `vip` → Prefix `§a[VIP]`, max 2 Plots

Du kannst alles über `/rang ...` anpassen.
