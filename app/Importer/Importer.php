<?php

declare(strict_types=1);

namespace App\Importer;

use Generator;
use Illuminate\Support\Str;
use InvalidArgumentException;

class Importer
{
    /**
     * 取得指定學期各系所開課資料.
     *
     *
     */
    public function get(string $semester): array
    {
        $parser = new Parser;

        $dir = $this->extract($semester);

        foreach ($this->parse($dir) as $path) {
            $departments[] = $parser->parse($path);

            unlink($path);
        }

        rmdir($dir);

        return $departments ?? [];
    }

    /**
     * 取得各系所課程網頁檔.
     *
     *
     */
    protected function parse(string $dir): Generator
    {
        $files = scandir($dir);

        if (false === $files) {
            throw new InvalidArgumentException(
                sprintf('%s is not a directory or there is something wrong when scanning directory.', $dir)
            );
        }

        foreach ($files as $file) {
            $path = sprintf('%s/%s', $dir, $file);

            if (!is_file($path)) {
                continue;
            }

            if (Str::contains($file, ['I000', '1014', '1406', '3708', '7006', 'index', 'all', 'e.html'])) {
                unlink($path);
            } else if ($this->convert($path)) {
                yield $path;
            }
        }
    }

    /**
     * 將 BIG-5 編碼檔案轉成 UTF-8.
     *
     *
     */
    protected function convert(string $path): bool
    {
        $content = file_get_contents($path);

        $encoding = mb_detect_encoding($content, ['ASCII', 'BIG-5', 'UTF-8']);

        if (false === $encoding) {
            return false;
        }

        if ('BIG-5' !== $encoding) {
            return true;
        }

        $converted = mb_convert_encoding($content, 'UTF-8', 'BIG-5');

        return false !== file_put_contents($path, $converted);
    }

    /**
     * 下載課程壓縮檔後，解壓縮並取得資料夾位址.
     *
     *
     */
    protected function extract(string $semester): string
    {
        return (new Decompressor)->decompress(
            (new Downloader)->download($semester)
        );
    }
}
