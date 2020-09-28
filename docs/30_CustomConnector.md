# Custom Connector
It's very easy to add your own connector, in fact there are only two services and two configuration classes to define.
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

## Connector Definition
Your connector definition needs to implement the `ConnectorDefinitionInterface`.

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

## Social Post Builder
Your social post builder needs to implement the `SocialPostBuilderInterface`.

| Method | Description
|------|----------------------|
| `configureFetch` | TBD |
| `fetch` | TBD |
| `configureFilter` | TBD |
| `filter` | TBD |
| `configureTransform` | TBD |
| `transform` | TBD |
