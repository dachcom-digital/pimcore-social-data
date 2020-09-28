# Walls and Feeds

![image](https://user-images.githubusercontent.com/700119/94450410-8a86eb80-01ad-11eb-9060-162b08ae61c6.png)

## Walls
Each Wall represents a set of feeds. 
You can add one or more wall and each wall can hold one or more feeds.

### Configuration

| Name | Description
|------|----------------------|
| `name` | Set a Name for your Wall |
| `Data Storage Path` | Add a DataObject-Folder via Drag'n'Drop to define a storage folder. New social posts will be stored there |
| `Asset Storage Path` | Add a Asset-Folder via Drag'n'Drop to define a storage folder. New social post assets will be stored there |
| `Statistics` | Get some statistics about wall-related social posts |
| `Logs` | All related logs will be stored there |
| `Feeds` | Add at least one feed |

## Feeds
Every Feed contains some system fields but also a connector related configuration section.


### Configuration

| Name | Description
|------|----------------------|
| `Store Media as Assets` | If checked, the SocialDataBundle will store a poster assets which needs to be prepopulated by each connector (`posterUrl`) |
| `Immediately publish Social Post` | If checked, every new imported post will be published instantly |
