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

### Dispatch Processor
There are two ways to execute the import process.

#### Command
To dispatch all wall builds at once, use this command:

```bash
php bin/console social-data:fetch:social-posts
```

To dispatch a specific wall build only, use this command (723 would be the wallId):

```bash
php bin/console social-data:fetch:social-posts -b 723
```

#### Manually
On each wall panel you'll find a button in the top right corner: `"Dispatch Social Post Import for this Wall"`. 
Click on it to execute the build process for this very specific wall only.

***

## Feeds
Every Feed contains some system fields but also a connector related configuration section.

### Configuration

| Name | Description
|------|----------------------|
| `Store Media as Assets` | If checked, the SocialDataBundle will store a poster assets which needs to be prepopulated by each connector (`posterUrl`) |
| `Immediately publish Social Post` | If checked, every new imported post will be published instantly |
