# Pimcore Social Data Bundle
[![Software License](https://img.shields.io/badge/license-GPLv3-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Latest Release](https://img.shields.io/packagist/v/dachcom-digital/social-data.svg?style=flat-square)](https://packagist.org/packages/dachcom-digital/social-data)
[![Travis](https://img.shields.io/travis/com/dachcom-digital/pimcore-social-data/master.svg?style=flat-square)](https://travis-ci.com/dachcom-digital/pimcore-social-data)
[![PhpStan](https://img.shields.io/badge/PHPStan-level%202-brightgreen.svg?style=flat-square)](#)

TBD

#### Requirements
* Pimcore >= 6.6.0

## Installation

```json
"require" : {
    "dachcom-digital/social-data" : "~1.0.0",
}
```

### Installation via Extension Manager
After you have installed the Social Data Bundle via composer, open pimcore backend and go to `Tools` => `Extension`:
- Click the green `+` Button in `Enable / Disable` row
- Click the green `+` Button in `Install/Uninstall` row

### Installation via CommandLine
After you have installed the Social Data Bundle via composer:
- Execute: `$ bin/console pimcore:bundle:enable SocialDataBundle`
- Execute: `$ bin/console pimcore:bundle:install SocialDataBundle`

## Upgrading

### Upgrading via Extension Manager
After you have updated the Social Data Bundle via composer, open pimcore backend and go to `Tools` => `Extension`:
- Click the green `+` Button in `Update` row

### Upgrading via CommandLine
After you have updated the Social Data Bundle via composer:
- Execute: `$ bin/console pimcore:bundle:update SocialDataBundle`

### Migrate via CommandLine
Does actually the same as the update command and preferred in CI-Workflow:
- Execute: `$ bin/console pimcore:migrations:migrate -b SocialDataBundle`

## Usage
- TBD

## Copyright and license
Copyright: [DACHCOM.DIGITAL](http://dachcom-digital.ch)  
For licensing details please visit [LICENSE.md](LICENSE.md)  

## Upgrade Info
Before updating, please [check our upgrade notes!](UPGRADE.md)
