# Setup
After you have enabled this Bundle, there are some global steps to define.

### I. Data Class
If you don't have any pimcore data object to manage your social posts, you need to create it first.
After that, you need to tell SocialDataBundle about it:

```yaml
# app/config/config.yml
social_data:
    social_post_data_class: SocialPost
```

The Data Class needs to provide some predefined fields and needs to implement the `SocialPostInterface`.
Just import the preconfigured [SocialPost Definition](https://github.com/dachcom-digital/pimcore-social-data/blob/master/src/SocialDataBundle/Resources/install/class/SocialPost.class.json)
to kickstart your data class. 

You can check the Health state by visiting the Social Data Menu `"Settings"` => `"Social Data"` => `"Connector Configuration"`.
Watch out for this information:   

![image](https://user-images.githubusercontent.com/700119/94448777-9c678f00-01ab-11eb-9a72-bae59620335e.png)

### II. The Connector Configuration
This is the final step: Setup your Connectors. Each connector has its own configuration and strategies.
Let's checkout the [Connector](./10_Connectors.md) Guide to learn how to use and install them. 

### III. CronJob
To crawl periodically your newest social data, you need to install a new cron task.
In this example we dispatch our processor every six hours.
You can of course adjust this to your needs, but you should always keep in mind that limit-timeouts may occur if the crawl intervals are very short.  

```bash
0 */6 * * * /usr/bin/php PATH/TO/bin/console social-data:fetch:social-posts
```

### III. Configure some Walls and Feeds
After you have successfully configured your required connectors you're now able to finally add some walls.
Ready? Read more about it [here](./11_WallsAndFeeds.md)

***

## Available Connector
- [Facebook](./Connectors/01_Facebook.md)
