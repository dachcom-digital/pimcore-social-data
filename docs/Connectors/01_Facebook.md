# Facebook Connector

This Connector allows you to fetch social posts from Facebook. Before you start be sure you've checked out the [Setup Instructions](../00_Setup.md).

![image](https://user-images.githubusercontent.com/700119/94452916-5f51cb80-01b0-11eb-86b2-026d8b7ef6f7.png)

## Requirements
First things first. To use this connector, this bundle requires some additional packages:
- [facebook/graph-sdk](https://github.com/facebookarchive/php-graph-sdk/blob/5.x/README.md) (Mostly already installed within a Pimcore Installation)

## Enable Connector

```yaml
# app/config/config.yml
social_data:
    social_post_data_class: SocialPost
    available_connectors:
        -   connector_name: facebook
```

## Connector Configuration
![image](https://user-images.githubusercontent.com/700119/94451768-164d4780-01af-11eb-9e52-3132ea02d714.png)

Now head back to the backend (`System` => `Social Data` => `Connector Configuration`) and checkout the facebook tab.
- Click on `Install`
- Click on `Enable`
- Before you hit the `Connect` button, you need to fill you out the Connector Configuration. After that, click "Save".
- Click `Connect`
  
## Connection
![image](https://user-images.githubusercontent.com/700119/79236998-f37fde80-7e6d-11ea-8b94-7bc015f50be0.png)

This will guide you through the facebook token generation. 
After hitting the "Connect" button, a popup will open to guide you through facebook authentication process. 
If everything worked out fine, the connection setup is complete after the popup closes.
Otherwise, you'll receive an error message. You may then need to repeat the connection step.

## Done!
You're done. No head back to the [wall section](./../11_WallsAndFeeds.md) and add your first facebook feed.

## Feed Configuration

| Name | Description
|------|----------------------|
| `Page Id` | Defines which page entries should be imported |
| `Limit` | Define a limit to restrict the amount of social posts to import (Default: 50) |