<?php

namespace App\Authentication\Validators;

class StudentId extends Validator
{
    /**
     * 學號格式.
     *
     * @var string
     */
    protected $pattern = '/^[468][0-9]{8}$/';

    /**
     * 科系與學號中間三碼對應表.
     *
     * @var array<string>
     */
    protected $departments = [
        '110',
        '115',
        '120',
        '125',
        '130',
        '131',
        '132',
        '137',
        '140',
        '141',
        '210',
        '211',
        '212',
        '216',
        '220',
        '232',
        '235',
        '237',
        '238',
        '240',
        '245',
        '246',
        '250',
        '256',
        '257',
        '258',
        '260',
        '310',
        '315',
        '320',
        '321',
        '330',
        '335',
        '336',
        '341',
        '366',
        '370',
        '410',
        '415',
        '420',
        '421',
        '425',
        '430',
        '441',
        '445',
        '450',
        '510',
        '511',
        '515',
        '520',
        '526',
        '530',
        '535',
        '546',
        '556',
        '605',
        '610',
        '620',
        '630',
        '710',
        '715',
        '716',
        '717',
        '725',
        '736',
        '740',
        '745',
        '751',
    ];

    /**
     * 檢查學號是否有效.
     *
     * @param string $sid
     *
     * @return bool
     */
    public function valid(string $sid): bool
    {
        if (1 !== preg_match($this->pattern, $sid)) {
            return false;
        } elseif (!$this->isEnrollment($sid)) {
            return false;
        } elseif (!in_array(substr($sid, 3, 3), $this->departments, true)) {
            return false;
        }

        return intval(substr($sid, -3)) < 200;
    }

    /**
     * 是否在學.
     *
     * @param string $sid
     *
     * @return bool
     */
    protected function isEnrollment(string $sid): bool
    {
        $years = $this->currentYear() - $this->startYear($sid);

        switch (substr($sid, 0, 1)) {
            case '4': // 學士
                return $years <= 7;
            case '6': // 碩士
                return $years <= 5;
            case '8': // 博士
                return $years <= 8;
            default:
                return false; // @codeCoverageIgnore
        }
    }

    /**
     * 入學學年.
     *
     * @param string $sid
     *
     * @return int
     */
    protected function startYear(string $sid): int
    {
        $year = intval(substr($sid, 1, 2));

        if ($year < 84) {
            $year += 100;
        }

        return $year;
    }

    /**
     * 目前學年.
     *
     * @return int
     */
    protected function currentYear(): int
    {
        $year = intval(date('Y')) - 1911;

        if (intval(date('n')) < 9) {
            $year -= 1;
        }

        return $year;
    }
}
