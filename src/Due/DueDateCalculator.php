<?php

namespace Shakahl\Due;

use Shakahl\Due\Exceptions\DueDateCalculatorException;
use Shakahl\Due\Exceptions\InvalidDateTimeFormatException;

/**
 * DueDate
 */
class DueDateCalculator
{
    /**
     * @var string
     */
    const HOUR = 'G';

    /**
     * @var string
     */
    const WEEKDAY = 'N';

    /**
     * Beginning hour of a business day
     * @var integer
     */
    protected $dayStart = 9;

    /**
     * Ending hour of a business day
     * @var integer
     */
    protected $dayEnd = 17;

    /**
     * Constructor. We wont use this.
     */
    public function __construct()
    {
        //
    }

    /**
     * Static creator method
     */
    public static function make(): DueDateCalculator
    {
        return new self;
    }

    /**
     * Magic method to allow invoking class instance.
     *
     * Takes the submit date and turnaround time in hours as an input
     * and returns the date and time when the issue is to be resolved.
     * Returns "null" if submited date is outside of working hours.
     *
     * @param  string|\DateTimeInterface $date
     * @param  int $hours
     * @return \DateTime|null
     */
    public function __invoke($date, int $hours)
    {
        return $this->calculate($date, $hours);
    }

    /**
     * Set starting hour of a business day.
     * @param int $hour
     */
    public function setDayStart(int $hour): DueDateCalculator
    {
        $this->dayStart = $hour;
        return $this;
    }

    /**
     * Get starting hour of a business day.
     *
     * @return int Hour.
     */
    public function getDayStart(): int
    {
        return $this->dayStart;
    }

    /**
     * Set ending hour of a business day.
     *
     * @param int $hour
     */
    public function setDayEnd(int $hour): DueDateCalculator
    {
        $this->dayEnd = $hour;
        return $this;
    }

    /**
     * Get ending hour of a business day.
     *
     * @return int Hour.
     */
    public function getDayEnd(): int
    {
        return $this->dayEnd;
    }

    /**
     * Get timestamp from variable input formats.
     * @param  string|\DateTimeInterface $datetime
     * @return int
     * @throws InvalidDateTimeFormatException
     */
    protected function parseDate($datetime): int
    {
        if (empty($datetime)) {
            throw new InvalidDateTimeFormatException("Empty datetime input.");
        }

        if (is_int($datetime)) {
            return $datetime;
        }

        if (is_string($datetime)) {
            $timestamp = strtotime($datetime);
            if ($timestamp === false) {
                throw new InvalidDateTimeFormatException("Invalid datetime format specified: $datetime");
            }
            return $timestamp;
        }

        if ($datetime instanceof \DateTimeInterface) {
            return $datetime->getTimestamp();
        }

        throw new InvalidDateTimeFormatException("Invalid datetime format specified.");
    }

    /**
     * Takes the submit date and turnaround time in hours as an input
     * and returns the date and time when the issue is to be resolved.
     * Returns "null" if submited date is outside of working hours.
     *
     * @param  string|\DateTimeInterface $date
     * @param  int $hours
     * @return \DateTime|null "null" if submited date is outside of working hours
     */
    public function calculate($date, int $hours)
    {
        if ($hours <= 0) {
            throw new DueDateCalculatorException('Turnaround hours must be positive integer.');
        }

        $time = $this->parseDate($date);

        if ($this->isOutsideWorkingHour($time)) {
            return null;
        }

        while ($hours > 0) {
            $hours--;
            $time += 3600;

            $hour = $this->numHour($time);

            // We have to add missing hours if time is
            // before or after business hours.
            if ($hour >= $this->dayEnd) {
                // After business hours.
                $time += ($this->dayStart + (24 - $hour)) * 3600;
            } elseif ($hour < $this->dayStart) {
                // Before business hours.
                $time += ($this->dayStart - $hour) * 3600;
            }

            // Skip weekends
            if ($this->isWeekend($time)) {
                $time += (8 - $this->weekDay($time)) * 24 * 3600;
            }
        }

        return (new \DateTime())->setTimestamp($time);
    }

    /**
     * Checks if time is outside of working hours-
     * @param  int     $time
     * @return boolean
     */
    private function isOutsideWorkingHour(int $time)
    {
        $h = $this->numHour($time);

        if ($this->isWeekend($time)) {
            return true;
        }

        if ($h >= $this->dayEnd) {
            return true;
        }

        if ($h < $this->dayStart) {
            return true;
        }

        return false;
    }

    /**
     * Checks if timestamp is at weekend.
     * @param  int     $time
     * @return boolean
     */
    private function isWeekend(int $time)
    {
        return $this->weekDay($time) > 5;
    }

    /**
     * Returns week day number.
     * @param  int    $time
     * @return int
     */
    private function weekDay(int $time)
    {
        return (int)date(self::WEEKDAY, $time);
    }

    /**
     * Returns the number of current hour
     * @param  int    $time
     * @return int
     */
    private function numHour(int $time)
    {
        return (int)date(self::HOUR, $time);
    }
}
