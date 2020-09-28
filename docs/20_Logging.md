# Logging

![image](https://user-images.githubusercontent.com/700119/94452628-097d2380-01b0-11eb-8a0a-87f2a08c9d4f.png)

The SocialDataBundle comes with a dedicated log table.

## Clean Up Task
Logs will be removed after `30 days`. Change the expiration via configuration:
 
```yaml
social_data:
    log_expiration_days: 10
```

## Custom Clean-Up

### Wall
TBD
  
### Global Log Flush
There is also a global log flush workflow. Go to the global connector configuration panel and hit the "Flush all logs"
button at the left top corner.

> Be aware this will truncate the logs tables and can't be undone!