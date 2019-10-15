<?php

namespace App\Http\Validators\Rules;

use Illuminate\Contracts\Validation\Rule;

final class StudentId implements Rule
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
     * @var array
     */
    protected $departments = [
        '110', // 中國文學系、中國文學研究所
        '115', // 外國語文學系、外國語文研究所
        '120', // 歷史學系、歷史研究所
        '125', // 哲學系、哲學研究所
        '130', // 語言學研究所
        '131', // 語言學研究所碩士班語言學組
        '132', // 語言學研究所碩士班華語教學研究組
        '137', // 外國語文學系英語教學碩士班
        '140', // 台灣文學與創意應用研究所
        '210', // 數學系
        '211', // 數學系應用數學碩士班
        '216', // 地球與環境科學系地震學碩/博士班
        '220', // 物理學系、物理研究所
        '232', // 數學系統計科學碩士班
        '235', // 地球與環境科學系
        '238', // 地球與環境科學系碩士班
        '240', // 數學研究所、數學系數學科學博士班
        '245', // 生物醫學科學系分子生物博士班
        '246', // 生命科學系分子生物碩/博士班、生物醫學科學系分子生物碩士班
        '250', // 生命科學系、生物醫學科學系
        '256', // 生命科學系生物醫學碩士班、生物醫學科學系生物醫學碩士班
        '260', // 化學暨生物化學系、化學暨生物化學研究所
        '310', // 社會福利學系、社會福利研究所
        '315', // 心理學系、心理學研究所
        '320', // 勞工關係學系
        '321', // 勞工關係學系碩士班
        '330', // 政治學系、政治學研究所
        '335', // 傳播學系
        '336', // 傳播學系電訊傳播碩士班
        '341', // 戰略暨國際事務研究所
        '366', // 心理學系臨床心理學碩士班
        '370', // 認知科學博士學位學程
        '410', // 資訊工程學系、資訊工程研究所
        '415', // 電機工程學系、電機工程研究所
        '420', // 機械工程學系、機械工程研究所
        '421', // 機械工程學系光機電整合工程碩士班
        '425', // 化學工程學系、化學工程研究所
        '430', // 通訊工程學系、通訊工程研究所
        '441', // 光機電整合工程研究所
        '445', // 前瞻製造系統碩/博士學位學程
        '450', // 環境智能及智慧系統博士學位學程
        '510', // 經濟學系
        '511', // 經濟學系國際經濟學碩/博士班
        '515', // 財務金融學系、財務金融研究所
        '520', // 企業管理學系、企業管理研究所
        '526', // 會計與資訊科技學系、會計與資訊科技研究所
        '530', // 資訊管理學系、資訊管理研究所
        '535', // 國際財務金融管理碩士學位學程
        '546', // 企業管理學系行銷管理碩士班
        '556', // 資訊管理學系醫療資訊管理碩士班
        '605', // 法律學系、法律學研究所
        '610', // 法律學系法學組
        '620', // 法律學系法制組
        '630', // 財經法律學系、財經法律學研究所
        '710', // 成人及繼續教育學系、成人及繼續教育研究所
        '716', // 教育學研究所課程與教學碩士班、教育學研究所課程博士班
        '717', // 教育學研究所教育學碩/博士班
        '725', // 犯罪防治學系、犯罪防治研究所
        '736', // 運動競技學系、運動競技學系運動與休閒教育碩士班
        '745', // 教育領導與管理發展國際碩/博士學位學程、
        '751', // 成人及繼續教育學系高齡者教育碩士班
    ];

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $sid
     * @return bool
     */
    public function passes($attribute, $sid)
    {
        if (!is_string($sid) || 1 !== preg_match($this->pattern, $sid)) {
            return false;
        } else if (!$this->isEnrollment($sid)) {
            return false;
        } else if (!in_array(substr($sid, 3, 3), $this->departments, true)) {
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
                return false;
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

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        return '學號格式錯誤';
    }
}
