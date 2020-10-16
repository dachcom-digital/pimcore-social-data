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

### III. Enable dynamic Fetch Mode

#### Via CronJob
To crawl periodically your newest social data, you need to install a new cron task.
In this example we dispatch our processor every six hours.
You can of course adjust this to your needs, but you should always keep in mind that limit-timeouts may occur if the crawl intervals are very short.  

```bash
0 */6 * * * /usr/bin/php PATH/TO/bin/console social-data:fetch:social-posts
```

#### Via Maintenance
If you don't want to install additional cronjobs, you may want to enable the import process via maintenance task.
This task is **disabled by default**, so you need to enable it first:

```yaml
# app/config/config.yml
social_data:
    maintenance:
        fetch_social_post: 
            enabled: true           # enable task, default false
            interval_in_hours: 3    # dispatch every 3 hours, default 6
```

### IV. Optional: Clean up old posts
Sometimes you may want to remove outdated posts. It's possible to delete posts (and optionally also the poster) by maintenance task.
This task is **disabled by default**, so you need to enable it first:

```yaml
# app/config/config.yml
social_data:
    maintenance:
        clean_up_old_posts: 
            enabled: true           # enable task, default false
            expiration_days: 10     # delete posts older than 10 days, default 150
            delete_poster: true     # also deletes poster asset if given, default false
```

### V. Configure some Walls and Feeds
After you have successfully configured your required connectors you're now able to finally add some walls.
Ready? Read more about it [here](./11_WallsAndFeeds.md)

***

## Available Connector
- [Facebook](https://github.com/dachcom-digital/pimcore-social-data-facebook-connector)
- [Instagram](https://github.com/dachcom-digital/pimcore-social-data-instagram-connector)
- [Twitter](https://github.com/dachcom-digital/pimcore-social-data-twitter-connector)
- [WeChat](https://github.com/dachcom-digital/pimcore-social-data-wechat-connector)
