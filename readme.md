#WebAutomator

Automation framework for web requests

##Description

Coming soon...

##Requirements

- PHP >= 5.5
- Guzzle >= 5.0.3

##Installation

The recommended way to install WebAutomator is through [Composer](http://getcomposer.org/).

    curl -sS https://getcomposer.org/installer | php

Next, update your project's composer.json file to include WebAutomator:

    {
        "repositories": [
            {
                "type": "git",
                "url": "https://github.com/paslandau/WebAutomator.git"
            }
        ],
        "require": {
             "paslandau/WebAutomator": "~0"
        }
    }

After installing, you need to require Composer's autoloader:
```php

require 'vendor/autoload.php';
```