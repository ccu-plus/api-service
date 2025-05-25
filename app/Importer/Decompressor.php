<?php

namespace App\Importer;

use PharData;
use RuntimeException;
use UnexpectedValueException;

class Decompressor
{
    /**
     * 解壓縮壓縮檔.
     *
     * @param string $path
     *
     * @return string
     *
     * @throws RuntimeException
     * @throws UnexpectedValueException
     */
    public function decompress(string $path): string
    {
        $dir = $this->tempdir();

        $extracted = (new PharData($path))->extractTo($dir, null, true);

        unlink($path);

        if (!$extracted) {
            throw new RuntimeException('There is something wrong when extract file');
        }

        return $dir;
    }

    /**
     * 創建暫存資料夾並取得資料夾位址.
     *
     * @return string
     */
    protected function tempdir(): string
    {
        $exception = new RuntimeException('Could not create temp directory.');

        $path = tempnam(sys_get_temp_dir(), 'ccu-plus-course-import-');

        if (false === $path) {
            throw $exception;
        } else if (!unlink($path)) {
            throw $exception;
        } else if (!mkdir($path)) {
            throw $exception;
        };

        return $path;
    }
}
