<?php
/**
 * @param $input
 * @return mixed
 * @todo implement function
 */
function merge($input)
{
    $arr = [];
    foreach ($input as $values) {
        if (count($values) !== 2) {
            continue;
        }

        $array = [
            strtotime($values[0]),
            strtotime($values[1])
        ];

        $arr = array_merge($arr, [$array]);
    }

    usort($arr, function ($a, $b) {
        return $a[0] > $b[0];
    });

    $n = 0;
    $len = count($arr);
    for ($i = 1; $i < $len; ++$i) {
        if ($arr[$i][0] > $arr[$n][1] + 1) {
            $n = $i;
        } else {
            if ($arr[$n][1] < $arr[$i][1]) {
                $arr[$n][1] = $arr[$i][1];
            }

            unset($arr[$i]);
        }
    }
    $arr = array_values($arr);

    usort($arr, function ($a, $b) {
        return $a[0] > $b[0];
    });

    $input = [];
    foreach ($arr as $values) {
        $values [0] = date('Y-m-d H:i:s', $values [0]);
        $values [1] = date('Y-m-d H:i:s', $values [1]);
        $array = [$values [0], $values [1]];
        array_push($input, $array);
    }

    return ($input);
}
