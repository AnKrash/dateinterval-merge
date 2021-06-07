<?php

use PHPUnit\Framework\TestCase;

require "index.php";


class MergeTest extends TestCase
{
    const CASES = [
        // case 0 - intersection
        [
            // input
            [
                ['2021-03-23 12:00:00', '2021-03-23 15:00:00'],
                ['2021-03-23 10:00:00', '2021-03-23 14:00:00'],
                ['2021-03-24 12:00:00', '2021-03-24 15:00:00'],
            ],
            // output
            [
                ['2021-03-23 10:00:00', '2021-03-23 15:00:00'],
                ['2021-03-24 12:00:00', '2021-03-24 15:00:00'],
            ],
        ],
        // case 1 - inclusion
        [
            // input
            [
                ['2021-03-24 12:00:00', '2021-03-24 15:00:00'],
                ['2021-03-23 12:00:00', '2021-03-23 15:00:00'],
                ['2021-03-23 13:00:00', '2021-03-23 14:00:00'],
                ['2021-03-23 13:00:00', '2021-03-23 15:00:00'],
            ],
            // output
            [
                ['2021-03-24 12:00:00', '2021-03-24 15:00:00'],
                ['2021-03-23 12:00:00', '2021-03-23 15:00:00'],
            ]
        ],
        // case 2 - no intersections
        [
            // input
            [
                ['2021-03-23 12:00:00', '2021-03-23 15:00:00'],
                ['2021-03-24 12:00:00', '2021-03-24 15:00:00'],
                ['2021-03-25 12:00:00', '2021-03-25 15:00:00'],
                ['2021-03-26 12:00:00', '2021-03-26 15:00:00'],
            ],
            // output
            [
                ['2021-03-23 12:00:00', '2021-03-23 15:00:00'],
                ['2021-03-24 12:00:00', '2021-03-24 15:00:00'],
                ['2021-03-25 12:00:00', '2021-03-25 15:00:00'],
                ['2021-03-26 12:00:00', '2021-03-26 15:00:00'],
            ]
        ],
        // case 3 - combined
        [
            // input
            [
                ['2021-03-24 12:00:00', '2021-03-24 15:00:00'],
                ['2021-03-23 12:00:00', '2021-03-23 15:00:00'],
                ['2021-03-23 13:00:00', '2021-03-23 14:00:00'],
                ['2021-03-23 14:00:00', '2021-03-23 16:00:00'],
            ],
            // output
            [
                ['2021-03-24 12:00:00', '2021-03-24 15:00:00'],
                ['2021-03-23 12:00:00', '2021-03-23 16:00:00'],
            ]
        ],
        // case 4 - empty input
        [
            // input
            [
            ],
            // output
            [
            ]
        ],
    ];

    /** @dataProvider dataProvider */
    public function testMerge($input, $output) {
        $actualOutput = merge($input);
        $this->assertSame($actualOutput, $output);
    }

    public function testBigVolume() {
        $input = $output = [];
        $startDate = new \DateTime('2021-05-05 6:00');
        $endDate = new \DateTime('2021-05-05 6:45');

        for ($i = 0; $i < 100; $i++) {
            $startDate->modify('+1 day');
            $endDate->modify('+1 day');

            for ($j = 0; $j < 12; $j += 2) {
                $shouldIntersect = ($i + $j) % 7 === 0;
                $pair = [];

                for ($k = 0; $k < 2; $k++) {
                    $start = (clone $startDate)->modify(($j + $k) . ' hour');
                    $end = (clone $endDate)->modify(($j + $k) . ' hour');

                    if ($k === 0 && $shouldIntersect) {
                        $end->modify('+30 mins');
                    }

                    $period = [$start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')];
                    $pair[] = $period;
                    $input[] = $period;
                }

                if ($shouldIntersect) {
                    $pair = [[$pair[0][0], $pair[1][1]]];
                }

                array_push($output, ...$pair);
            }
        }

        $time = new \DateTime();
        $actualOutput = merge($input);
        $evalTime = (new \DateTime())->getTimestamp() - $time->getTimestamp();
        print($evalTime);
        $this->assertSame($actualOutput, $output);
        $this->assertLessThan(2, $evalTime);
    }

    public function dataProvider() {
        return static::CASES;
    }
}