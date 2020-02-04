# WarpUI
A warp and world teleport plugin that supports rotation and pitch.
## Features
- Warps and Worlds can be listed in an UI, and players can teleport to them by clicking the buttons.
- Colored warp names are supported
- Rotation of the player (yaw + pitch) is saved for warps
- There is a command to teleport to warps: `/warpui teleport <warpname>`
- Clickable items that can open the warp & world ui
- You can choose what items to use, rename them & disable them 
## Permissions
Since Version 3.2.0 permissions for warps have been added. Those are in the following style: `warpui.warp.` + lowercase warp name, without colors. Same goes for worlds: `warpui.world.` + lowercase folder name without color codes
- Warp `Spawn`: `warpui.warp.spawn`
- World `Plots`: `warpui.world.plots`
Command permissions:
- WarpUI command: `warpui.command.warpui`
- WorldUI command: `warpui.command.world`
Check plugin.yml for further permission information
## Changes
- Version 3.3.0: Permissions are now logically nested. If you used the plugin with an earlier version, you might have to fix your permission sets!  