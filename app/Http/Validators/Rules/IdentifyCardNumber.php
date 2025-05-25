<?php

declare(strict_types=1);

namespace App\Http\Validators\Rules;

use Illuminate\Contracts\Validation\Rule;

final class IdentifyCardNumber implements Rule
{
    /**
     * 身分證字號格式.
     *
     * @var string
     */
    private $pattern = '/^[A-Z][12][0-9]{8}$/';

    /**
     * 身分證字號字母對應字元表.
     *
     * @var array
     */
    private $locations = [
        'A' => [1, 0], 'B' => [1, 1], 'C' => [1, 2], 'D' => [1, 3],
        'E' => [1, 4], 'F' => [1, 5], 'G' => [1, 6], 'H' => [1, 7],
        'I' => [3, 4], 'J' => [1, 8], 'K' => [1, 9], 'L' => [2, 0],
        'M' => [2, 1], 'N' => [2, 2], 'O' => [3, 5], 'P' => [2, 3],
        'Q' => [2, 4], 'R' => [2, 5], 'S' => [2, 6], 'T' => [2, 7],
        'U' => [2, 8], 'V' => [2, 9], 'W' => [3, 2], 'X' => [3, 0],
        'Y' => [3, 1], 'Z' => [3, 3],
    ];

    /**
     * 身分證字號驗證公式權重.
     *
     * @var array
     */
    private $weights = [1, 9, 8, 7, 6, 5, 4, 3, 2, 1, 1];

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $icn
     *
     * @return bool
     */
    public function passes($attribute, $icn)
    {
        if (!is_string($icn) || 1 !== preg_match($this->pattern, $icn)) {
            return false;
        }

        $transform = array_map(function ($m, $n) {
            return $m * $n;
        }, $this->explode($icn), $this->weights);

        return 0 === (array_sum($transform) % 10);
    }

    /**
     * 將身分證字號轉換為 11 位數字.
     *
     *
     */
    private function explode(string $icn): array
    {
        $icns = str_split($icn, 1);

        $letter = array_shift($icns);

        $icns = array_map('intval', $icns);

        array_unshift($icns, ...$this->locations[$letter]);

        return $icns;
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        return '身分證字號格式錯誤';
    }
}
