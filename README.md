HighRoller for Zend Framework 2
=======
Package of Highroller by [@jmaclabs](https://twitter.com/#!/JMACLABS) ready for Zend Framework 2 integration via Composer.


Introduction
------------
HighRoller is an object-oriented PHP Wrapper for the Highcharts JavaScript Library.
HighRoller gets Highcharts up and running in your PHP project fast.
* HighRoller [Home Page](http://highroller.io)
* Gravity [Home Page](http://gravity.com)
* Highcharts [Home Page](http://www.highcharts.com/)

This package is a my own [fork](https://github.com/jfloff/HighRoller) ready for Zend Framework 2 integration.


Installation
------------

### Main Setup

#### By cloning project

1. Install the [highroller-zf2](https://github.com/jfloff/highroller-zf2) module
   by cloning it into `./vendor/`.
2. Clone this project into your `./vendor/` directory.

#### With composer

1. Add this project in your composer.json:

    ```json
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/jfloff/highroller-zf2"
        }
    ],

    "require": {
        "jfloff/highroller-zf2": "dev-master",
    }
    ```

2. Now tell composer to download **highroller-zf2** by running the command:

    ```bash
    $ composer update
    ```

#### Post installation

1. Enabling it in your `application.config.php`file.

    ```php
    <?php
    return array(
        'modules' => array(
            // ...
            'HighRoller',
        ),
        // ...
    );
    ```

Licensing
------------
HighRoller is licensed by Gravity.com under the Apache 2.0 license, see the LICENSE file for more details.

Highcharts is licensed by Highsoft Solutions AS and can be obtained here:

[http://www.highcharts.com/products/highcharts] (http://www.highcharts.com/products/highcharts).

Highcharts is licensed for free for any personal or non-profit projects under the [Creative Commons Attribution-NonCommercial
3.0 License] (http://creativecommons.org/licenses/by-nc/3.0/).

[See the license and pricing details directly on the Highcharts.com site for more details.] (http://www.highcharts.com/license)