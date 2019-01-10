<?php

/**
 * Функция, которая возвращает сумму положительных целых чисел, превышающих 64-битный integer.
  *
 * @param string|int $addend_1
 * @param string|int $addend_2
 *
 * @return string
 * @throws \Exception
 */
function bigIntSum($addend_1, $addend_2)
{
    if (!is_numeric($addend_1)) {
        throw new \Exception('Первое слагаемое должно быть целым числом');
    }
    if (!is_numeric($addend_2)) {
        throw new \Exception('Второе слагаемое должно быть целым числом');
    }
    if (strpos($addend_1, '.') !== false) {
        throw new \Exception('Первое слагаемое должно быть целым числом, а не дробным');
    }
    if (strpos($addend_2, '.') !== false) {
        throw new \Exception('Второе слагаемое должно быть целым числом, а не дробным');
    }
    if (strpos($addend_1, '-') === 0) {
        throw new \Exception('Первое слагаемое должно быть положительным числом');
    }
    if (strpos($addend_2, '-') === 0) {
        throw new \Exception('Второе слагаемое должно быть положительным числом');
    }

    $addend_1 = (string)$addend_1;
    $addend_2 = (string)$addend_2;

    $addend_1_length = strlen($addend_1);
    $addend_2_length = strlen($addend_2);

    $max_length_of_addend = ($addend_1_length > $addend_2_length) ? $addend_1_length : $addend_2_length;

    // Добавляем ведущие нули для слагаемого минимальной длины
    if ($addend_1_length < $max_length_of_addend) {
        $addend_1 = sprintf('%0' . $max_length_of_addend . 'd', $addend_1);
    }
    if ($addend_2_length < $max_length_of_addend) {
        $addend_2 = sprintf('%0' . $max_length_of_addend . 'd', $addend_2);
    }

    $sum_of_numbers = 0;

    // складываем число каждого порядка (как в сложении "столбиком")
    for ($i = $max_length_of_addend - 1; $i >= 0; $i--) {
        $sum_of_numbers += (int)$addend_1[$i] + (int)$addend_2[$i];
        $addend_1[$i] = (string)($sum_of_numbers % 10);
        $sum_of_numbers = (int)($sum_of_numbers / 10);
    }
    if ($sum_of_numbers > 0) {
        $addend_1 = (string)$sum_of_numbers . $addend_1;
    }

    return $addend_1;
}