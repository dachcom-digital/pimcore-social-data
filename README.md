# Pimcore Social Data Bundle
[![Software License](https://img.shields.io/badge/license-GPLv3-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Software License](https://img.shields.io/badge/license-DCL-white.svg?style=flat-square&color=%23ff5c5c)](LICENSE.md)
[![Latest Release](https://img.shields.io/packagist/v/dachcom-digital/social-data.svg?style=flat-square)](https://packagist.org/packages/dachcom-digital/social-data)
[![Tests](https://img.shields.io/github/actions/workflow/status/dachcom-digital/pimcore-social-data/.github/workflows/codeception.yml?branch=master&style=flat-square&logo=github&label=codeception)](https://github.com/dachcom-digital/pimcore-social-data/actions?query=workflow%3ACodeception+branch%3Amaster)
[![PhpStan](https://img.shields.io/github/actions/workflow/status/dachcom-digital/pimcore-social-data/.github/workflows/php-stan.yml?branch=master&style=flat-square&logo=github&label=phpstan%20level%204)](https://github.com/dachcom-digital/pimcore-social-data/actions?query=workflow%3A"PHP+Stan"+branch%3Amaster)

This Bundles allows you to load social data from different networks like Facebook, Instagram or YouTube.

![image](https://user-images.githubusercontent.com/700119/94448014-bce31980-01aa-11eb-8869-e38bde73d253.png)

### Release Plan
| Release | Supported Pimcore Versions | Supported Symfony Versions | Release Date | Maintained     | Branch                                                                 |
|---------|----------------------------|----------------------------|--------------|----------------|------------------------------------------------------------------------|
| **3.x** | `11.0`                     | `6.2`                      | 07.11.2023   | Feature Branch | master                                                                 |
| **2.x** | `10.1` - `10.6`            | `5.4`                      | 05.01.2022   | Unsupported    | [2.x](https://github.com/dachcom-digital/pimcore-social-data/tree/2.x) |
| **1.x** | `6.0` - `6.9`              | `3.4`, `^4.4`              | 27.04.2020   | Unsupported    | [1.x](https://github.com/dachcom-digital/pimcore-social-data/tree/1.x) |

## Installation

```json
"require" : {
    "dachcom-digital/social-data" : "~3.1.0",
}
```

Add Bundle to `bundles.php`:
```php
return [
    SocialDataBundle\SocialDataBundle::class => ['all' => true],
];
```

- Execute: `$ bin/console pimcore:bundle:install SocialDataBundle`

## Upgrading
- Execute: `$ bin/console doctrine:migrations:migrate --prefix 'SocialDataBundle\Migrations'`

## Usage
This Bundle needs some preparation. Please check out the [Setup](docs/00_Setup.md) guide first.

## Further Information
- [Setup](docs/00_Setup.md)
- [Connectors](./docs/10_Connectors.md)
  - [Facebook](https://github.com/dachcom-digital/pimcore-social-data-facebook-connector)
  - [Instagram](https://github.com/dachcom-digital/pimcore-social-data-instagram-connector)
  - [Twitter](https://github.com/dachcom-digital/pimcore-social-data-twitter-connector)
  - [Youtube](https://github.com/dachcom-digital/pimcore-social-data-youtube-connector)
  - [LinkedIn](https://github.com/dachcom-digital/pimcore-social-data-linkedin-connector)
  - [WeChat](https://github.com/dachcom-digital/pimcore-social-data-wechat-connector) EOL!
- [Walls And Feeds](docs/11_WallsAndFeeds.md)
- [Events](docs/12_Events.md)
- [Frontend Usage](docs/13_FrontendUsage.md)
- [Logging](docs/20_Logging.md)
- [Custom Connector](docs/30_CustomConnector.md)

## Upgrade Info
Before updating, please [check our upgrade notes!](UPGRADE.md)

## License
**DACHCOM.DIGITAL AG**, Löwenhofstrasse 15, 9424 Rheineck, Schweiz  
[dachcom.com](https://www.dachcom.com), dcdi@dachcom.ch  
Copyright © 2024 DACHCOM.DIGITAL. All rights reserved.  

For licensing details please visit [LICENSE.md](LICENSE.md)  
