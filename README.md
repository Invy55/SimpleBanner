[![](https://poggit.pmmp.io/shield.state/SimpleBanner)](https://poggit.pmmp.io/p/SimpleBanner)
[![](https://poggit.pmmp.io/shield.api/SimpleBanner)](https://poggit.pmmp.io/p/SimpleBanner)
[![](https://poggit.pmmp.io/shield.dl.total/SimpleBanner)](https://poggit.pmmp.io/p/SimpleBanner)

# SimpleBanner
A simple banner generator for Pocketmine-MP.

This plugin allows you to generate custom banners with all the existing patterns and colors, using a simple user-friendly in game interface.

## Commands
 - /banner _[color tag](https://github.com/Invy55/SimpleBanner#color-tags)_ -  Open a simple menu to create the banner
 
   Permission: _simplebanner.command.banner_
   
## Config
You can edit the "config.yml" in plugin data folder:
 - _banner-number_ The number of given banners, 1 to 16 default: 16
 - _banner-timeout_ Time to pass between running of /banner, default 0. Add permission _simplebanner.command.notimeout_ to a user to bypass the limit.
   
## Color Tags
Also visible with _/banner_

 - black
 - dark_green
 - dark_aqua
 - dark_purple
 - orange
 - gray
 - dark_gray
 - blue
 - green
 - aqua
 - red
 - light_purple
 - yellow
 - white
### Knowed issues
 - Received banner isn't rendered.
