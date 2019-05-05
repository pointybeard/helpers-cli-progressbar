<?php

namespace pointybeard\Helpers\Cli\ProgressBar;

use pointybeard\Helpers\Statistics\SlidingAverage;
use pointybeard\Helpers\Cli\Colour;
use pointybeard\Helpers\Functions\Time;

class ProgressBar
{
    private $start = null;
    private $length = 30;
    private $completed = 0;
    private $total = null;
    private $charProgress = '▓';
    private $charBlank = '░';
    private $foreground = Colour\Colour::FG_DEFAULT;
    private $background = Colour\Colour::BG_DEFAULT;
    private $format = "{{PROGRESS_BAR}} {{PERCENTAGE}}% {{COMPLETED}}/{{TOTAL}} ({{ELAPSED_TIME}} elapsed, approx. {{REMAINING_TIME}} remaining)";

    private $rateAverage = null;

    /**
     * Initialise the ProgressBar class by setting the total number of
     * work units it will be tracking.
     * @param int $total
     */
    public function __construct($total)
    {
        $this->total($total);
    }

    /**
     * Magic method for getting and setting private variables. If $args is,
     * this method will instead return the value. When setting the value, an
     * instance of this class is returned, allowing method chaining.
     * @param  string $name the name of the class variable to get/set
     * @param  mixed $args value to assign to variable (optional)
     * @return mixed       Will return the value of the variable of $args
     *                     has been omitted, otherwise an instance of $this
     */
    public function __call($name, $args)
    {
        if (empty($args)) {
            return $this->$name;
        }

        $this->$name = $args[0];
        return $this;
    }

    /**
     * Sets the start time which is used internally to calculate the elapsed and
     * remaining time.
     * @param  int $time value to seed $this->start with (optional)
     * @return self       instance of $this
     */
    public function start($time = null)
    {
        $this->start = is_null($time) ? time() : $time;
        return $this;
    }

    /**
     * Convenience method for determining of the start time has been set
     * @return boolean true if it has been set
     */
    private function hasStarted()
    {
        return !is_null($this->start);
    }

    /**
     * Increase the work units completed by $units (Default is 1), then redraw
     * the progress bar.
     * @param  integer $units A value to increase the progress bar by.
     * @return void
     */
    public function advance($units = 1)
    {
        if (!$this->hasStarted()) {
            $this->start();
        }

        $this->completed += $units;
        $this->draw();
    }

    public function rate()
    {
        $instantaneousRate = $this->completed / (time() - $this->start);

        if (!($this->rateAverage instanceof SlidingAverage\SlidingAverage)) {
            $this->rateAverage = new SlidingAverage\SlidingAverage(
                floor($this->total * 0.10), // Total samples is 10%
                $instantaneousRate
            );
        } else {
            $this->rateAverage->push($instantaneousRate);
        }

        return $this->rateAverage->sample();
    }

    public function remaining()
    {
        return $this->total - $this->completed;
    }

    public function elapsedTime()
    {
        return time() - $this->start;
    }

    public function remainingTime()
    {
        return $this->remaining() / $this->rate();
    }

    public function percentageCompleted()
    {
        return (double)$this->completed / (double)$this->total;
    }

    private function buildProgressBar()
    {

        // This prevents broken characters
        $realLength = $this->length() * strlen($this->charBlank());

        $completedLength = floor(
            $this->percentageCompleted() * (
                $realLength * (1 / strlen($this->charProgress()))
            )
        );

        $result = str_repeat($this->charProgress(), $completedLength);

        if ($completedLength < $realLength) {
            $result = str_pad(
                $result,
                $realLength,
                $this->charBlank()
            );
        } else {
            $result .= $this->charProgress();
        }

        return Colour\Colour::colourise(
            $result,
            $this->foreground(),
            $this->background()
        );
    }

    private function buildArgs()
    {
        return [
            'PROGRESS_BAR' => $this->buildProgressBar(),
            'PERCENTAGE' => number_format($this->percentageCompleted() * 100.0, 2),
            'COMPLETED' => $this->completed,
            'TOTAL' => $this->total,
            'ELAPSED_TIME' => Time\human_readable_time($this->elapsedTime(), true),
            'REMAINING_TIME' => (
                $this->elapsedTime() < 3
                    ? '?'
                    : Time\human_readable_time($this->remainingTime(), true)
            ),
        ];
    }

    private static function replacePlaceholdersInString(array $placeholders, array $replacements, $string)
    {
        $search = array_map(
            function ($value) {
                return "{{{$value}}}";
            },
            $placeholders
        );

        return str_replace($search, $replacements, $string);
    }

    public function draw()
    {
        $args = $this->buildArgs();

        printf(
            "\r%s",
            self::replacePlaceholdersInString(
                array_keys($args),
                array_values($args),
                $this->format
            )
        );
    }
}
