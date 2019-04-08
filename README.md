# EventCommands
[![](https://poggit.pmmp.io/shield.state/EventCommands)](https://poggit.pmmp.io/p/EventCommands)
[![](https://poggit.pmmp.io/shield.api/EventCommands)](https://poggit.pmmp.io/p/EventCommands)
[![](https://poggit.pmmp.io/shield.dl.total/EventCommands)](https://poggit.pmmp.io/p/EventCommands)
[![HitCount](http://hits.dwyl.io/Zedstar16/EventCommands.svg)](http://hits.dwyl.io/Zedstar16/EventCommands)

This is a plugin that allows you to setup commands to run when certain ingame events occur

The commands must be configured in the config.yml, which will appear in the plugins folder when the server is restarted after the plugin is added.

You can configure commands for:
- Player Join
- Player Death
- Player Gamemode Change
- Player Level Change (world)
- Player Respawn
- Player Exhaust (run out of food)


# Command Tags & executors

 You can execute the command as:

- @p (the player without special priveledges)
- @op (runs command like the player is op)
- @console (runs command as console

 You can use these tags in the command:

- {player} (the players name)
- {x}, {y}, {z} (the players X, Y and Z coordinates
- {tag} (the players tag, nickname/display name)
- {level} (the world the player is currently in)
 
# Enhancements

If there are any problems or you would like anything additional to be added to the plugin such as more events, or more command tags, 
simply create an issue
