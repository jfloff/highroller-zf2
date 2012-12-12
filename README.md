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
        "jfloff/highroller-zf2": "dev-master"
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
            'AssetManager',
        ),
        // ...
    );
    ```

Quick-Guide for ZF2 Skeleton Application
------------
In this quick guide, we will place a very humble line chart in the ZF2 Skeleton Application.
**Before starting** make sure you are using a clean [ZF2 Skeleton Application](), and that you already set up **highroller-zf2** using the instructions above.

Open `module/Application/src/Application/Controller/IndexController.php`.

1. Include HighRoller files:
    ```php
    use HighRollerLineChart;
    use HighRollerSeriesData;
    ```

2. Inside `indexAction` function create a new line chart:
    ```php
    $linechart = new HighRollerLineChart();
    $linechart->title->text = 'Line Chart';

    $series = new HighRollerSeriesData();
    $series->name = 'myData';

    $chartData = array(5324, 7534, 6234, 7234, 8251, 10324);
    foreach ($chartData as $value)
        $series->addData($value);

    $linechart->addSeries($series);
    ```

3. Pass the your HighRoller object `$linechart` to the view:
    ```php
    return new ViewModel(array('highroller' => $linechart));
    ```

Open `module/Application/view/application/index/index.phtml`

1. Include highcharts.js file (you could also do this in your layout):
    ```phtml
    <?php echo $this->headScript()->prependFile($this->basePath() . '/js/highcharts.js'); ?>
    ```

2. At the top of the file:
    * Add a HTML div where your chart will be rendered to,
    * Set the div id in the HighRoller object,
    * Finally append the render script.

    ```phtml
    <div id="highroller"></div>
    <?php
        $this->highroller->chart->renderTo = "highroller";
        echo $this->headScript()->appendScript($this->highroller->renderChart());
    ?>
    ```

3. You should now see a beautiful simple line chart in your main page, just like this one:

![linechart](http://i.imgur.com/IXGd7.png)


Licensing
------------
HighRoller is licensed by Gravity.com under the Apache 2.0 license, see the LICENSE file for more details.

Highcharts is licensed by Highsoft Solutions AS and can be obtained here:

[http://www.highcharts.com/products/highcharts](http://www.highcharts.com/products/highcharts).

Highcharts is licensed for free for any personal or non-profit projects under the [Creative Commons Attribution-NonCommercial
3.0 License] (http://creativecommons.org/licenses/by-nc/3.0/).

[See the license and pricing details directly on the Highcharts.com site for more details.] (http://www.highcharts.com/license)
