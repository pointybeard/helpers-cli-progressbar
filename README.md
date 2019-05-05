# PHP Helpers: Command-line Progress Bar

-   Version: v1.0.2
-   Date: May 05 2019
-   [Release notes](https://github.com/pointybeard/helpers-cli-progressbar/blob/master/CHANGELOG.md)
-   [GitHub repository](https://github.com/pointybeard/helpers-cli-progressbar)

A simple, yet powerful, class for rendering progress bars to the command-line.

## Installation

This library is installed via [Composer](http://getcomposer.org/). To install, use `composer require pointybeard/helpers-cli-progressbar` or add `"pointybeard/helpers-cli-progressbar": "~1.0"` to your `composer.json` file.

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

### Requirements

This library makes use of the [PHP Helpers: Sliding Average](https://github.com/pointybeard/helpers-statistics-slidingaverage) (`pointybeard/helpers-statistics-slidingaverage`), [PHP Helpers: Command-line Colours](https://github.com/pointybeard/helpers-cli-colour) (`pointybeard/helpers-cli-colour`), and [PHP Helpers: Time Functions](https://github.com/pointybeard/helpers-time-functions) (`pointybeard/helpers-time-functions`) packages. They are installed automatically via composer.

To include all the [PHP Helpers](https://github.com/pointybeard/helpers) packages on your project, use `composer require pointybeard/helpers` or add `"pointybeard/helpers": "~1.0"` to your composer file.

## Usage

Include this library in your PHP files with `use pointybeard\Helpers\Cli\ProgressBar` and instanciate the `ProgressBar\ProgressBar` class like so:

    <?php
    use pointybeard\Helpers\Cli\ProgressBar

    $progress = (new ProgressBar\ProgressBar(100))
        ->length(30)
        ->foreground(Cli\Colour::FG_GREEN)
        ->background(Cli\Colour::BG_RED)
    ;

    // Optional. Seeds the start time of the progress bar. time() is used
    // if omitted.
    $progress->start();

    for($ii = 0; $ii < 100; $ii++) {
        usleep(500000);

        // This moves the progress forward (default is 1 unit) and redraws it
        $progress->advance();
    }

### Placeholders

The format of the progress bar can be modified using the `format` method. The default format is `{{PROGRESS_BAR}} {{PERCENTAGE}}% {{COMPLETED}}/{{TOTAL}} ({{ELAPSED_TIME}} elapsed, approx. {{REMAINING_TIME}} remaining)`.

Placeholders available are:

-   PROGRESS_BAR
-   PERCENTAGE
-   COMPLETED
-   TOTAL
-   ELAPSED_TIME
-   REMAINING_TIME

## Support

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/pointybeard/helpers-cli-progressbar/issues),
or better yet, fork the library and submit a pull request.

## Contributing

We encourage you to contribute to this project. Please check out the [Contributing documentation](https://github.com/pointybeard/helpers-cli-progressbar/blob/master/CONTRIBUTING.md) for guidelines about how to get involved.

## License

"PHP Helpers: Command-line Progress Bar" is released under the [MIT License](http://www.opensource.org/licenses/MIT).
