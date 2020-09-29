# Custom Connector
It's very easy to add your own connector.
In fact there are two services, two configuration classes and two form classes to define.
To get started, you need register them first:

```yaml
services:
    ## Post Builder
    AppBundle\SocialData\Connector\MyConnectorType\Builder\SocialPostBuilder: ~

    ## Definition
    AppBundle\SocialData\Connector\MyConnectorType\ConnectorDefinition:
        tags:
            - {
                name: social_data.connector_definition,
                identifier: my_connector_type,
                socialPostBuilder: AppBundle\SocialData\Connector\MyConnectorType\Builder\SocialPostBuilder
            }
```

Now you need to add them to the global register:

```yaml
social_data:
    social_post_data_class: SocialPost
    available_connectors:
        -   connector_name: my_connector_type
```

After that you're already able to add your connector in the configuration panel in backend.

***

## Service Classes

### Connector Definition
The `Connector Definition` presents all connector configuration.
This class needs to implement the `ConnectorDefinitionInterface`.

| Method | Description
|------|----------------------|
| `isOnline` | TBD |
| `beforeEnable` | TBD |
| `beforeDisable` | TBD |
| `isAutoConnected` | TBD |
| `isConnected` | TBD |
| `connect` | TBD |
| `disconnect` | TBD |
| `needsEngineConfiguration` | TBD |
| `hasLogPanel` | TBD |
| `getEngineConfigurationClass` | TBD |
| `getFeedConfigurationClass` | TBD |
| `getEngineConfiguration` | TBD |

#### Configuration Classes
The `Configuration Classes` presents all connector configuration. 
You need two of them: The Engine configuration and Feed configuration class.

##### Engine Configuration
The engine configuration class needs to implement the `ConnectorEngineConfigurationInterface`.

| Required Methods | Description
|------|----------------------|
| `static function getFormClass` | Returns the configuration form type |

##### Feed Configuration
THe feed configuration class needs to implement the `ConnectorFeedConfigurationInterface`.

| Required Methods | Description
|------|----------------------|
| `static function getFormClass` | Returns the configuration form type |

***

### Social Post Builder
The social post builder class needs to implement the `SocialPostBuilderInterface`.

| Method | Description
|------|----------------------|
| `configureFetch` | TBD |
| `fetch` | TBD |
| `configureFilter` | TBD |
| `filter` | TBD |
| `configureTransform` | TBD |
| `transform` | TBD |
