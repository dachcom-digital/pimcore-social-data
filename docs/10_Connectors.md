# Connectors

![image](https://user-images.githubusercontent.com/700119/79234884-3096a180-7e6b-11ea-8956-bf58969817c7.png)

Every connector has at least two stages: `Install` and `Enabled`.

## Installation
After pressing the Install button, the SocialDataBundle will generate a database entry, a so called "Connector Engine".
This engine stores additional configuration fields, depending on each connector.

> **Warning:** If you want to uninstall a connector, all related data will be lost! 

## Enabling/Disabling
Enable or disable a connector. There is no data loss if a connector gets disabled.

## Connect
Not every Connector requires the connection feature. 
The [Facebook Connector](./Connectors/01_Facebook.md) for example, requires a valid access token which will be created, after you hit this button. 

***

## Available Connector
- [Facebook](./Connectors/01_Facebook.md)