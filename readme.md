#web-automator
[![Build Status](https://travis-ci.org/paslandau/web-automator.svg?branch=master)](https://travis-ci.org/paslandau/web-automator)

Automation framework for web requests

##Description

Coming soon...

##Requirements

- PHP >= 5.5
- Guzzle >= 5.0.3

##Installation

The recommended way to install web-automator is through [Composer](http://getcomposer.org/).

    curl -sS https://getcomposer.org/installer | php

Next, update your project's composer.json file to include web-automator:

    {
        "repositories": [ { "type": "composer", "url": "http://packages.myseosolution.de/"} ],
        "minimum-stability": "dev",
        "require": {
             "paslandau/web-automator": "dev-master"
        }
        "config": {
            "secure-http": false
        }
    }

_**Caution:** You need to explicitly set `"secure-http": false` in order to access http://packages.myseosolution.de/ as repository. 
This change is required because composer changed the default setting for `secure-http` to true at [the end of february 2016](https://github.com/composer/composer/commit/cb59cf0c85e5b4a4a4d5c6e00f827ac830b54c70#diff-c26d84d5bc3eed1fec6a015a8fc0e0a7L55)._


After installing, you need to require Composer's autoloader:
```php

require 'vendor/autoload.php';
```