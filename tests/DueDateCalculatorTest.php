<?php

namespace Shakahl\Due\TestCases;

use Shakahl\Due\DueDateCalculator;
use Shakahl\Due\Exception;
use Shakahl\Due\Exceptions\InvalidDateTimeFormatException;
use Shakahl\Due\Exceptions\DueDateCalculatorException;
use PHPUnit\Framework\TestCase;

/**
 *
 * @category  \Shakahl\Due\TestCases
 * @package   \Shakahl\Due\TestCases
 */
class DueDateCalculatorTest extends TestCase
{
    public function testCalculateOnEmpty(): void
    {
        $this->expectException(InvalidDateTimeFormatException::class);

        $this->create()->calculate('', 1);
    }

    public function testCalculateOnInvalidString(): void
    {
        $this->expectException(InvalidDateTimeFormatException::class);

        $this->create()->calculate('fsdfsdfs', 1);
    }

    public function testCalculateOnInvalidObject(): void
    {
        $this->expectException(InvalidDateTimeFormatException::class);

        $this->create()->calculate(new \stdClass, 1);
    }

    public function testCalculateOnZeroTurninghour(): void
    {
        $this->expectException(DueDateCalculatorException::class);

        $this->create()->calculate(date('Y-m-d H:i:s'), 0);
    }

    public function testCalculateOnNegativeTurninghour(): void
    {
        $this->expectException(DueDateCalculatorException::class);

        $this->create()->calculate(date('Y-m-d H:i:s'), -1);
    }

    public function testCalculate()
    {
        // 2018-05-11: its a friday
        $cases = [

            // One hour
            ['2018-05-11 12:00:00', 1, '2018-05-11 13:00:00'],
            ['2018-05-11 12:01:01', 2, '2018-05-11 14:01:01'],

            // Weekend (should be null)
            ['2018-05-12 12:00:00', 1, null],

            // Skip weekend
            ['2018-05-11 12:00:00', 6, '2018-05-14 10:00:00'],
            ['2018-05-11 12:12:12', 12, '2018-05-14 16:12:12'],
        ];

        foreach ($cases as $case) {
            $this->assertEquals($this->calculateTest($case[0], $case[1]), $case[2]);
        }
    }

    /**
     * Shortcut method
     * @param  string $datetime Y-m-d H:i:s
     * @param  int    $hours
     * @return string|null
     */
    public function calculateTest(string $datetime, int $hours)
    {
        $date = $this->create()->calculate($datetime, $hours);

        if (!$date) {
            return $date;
        }

        return $this->format($date);
    }

    /**
     * Creator method
     * @return DueDateCalculator
     */
    public function create()
    {
        return (new DueDateCalculator)
            ->setDayStart(9)
            ->setDayEnd(17);
    }

    /**
     * @param  \DateTimeInterface $time
     * @return string
     */
    public function format(\DateTimeInterface $time)
    {
        return $time->format('Y-m-d H:i:s');
    }
}
