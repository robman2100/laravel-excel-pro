<?php

namespace robman2100\Excel\Factories;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use robman2100\Excel\Files\TemporaryFile;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use robman2100\Excel\Concerns\MapsCsvSettings;
use robman2100\Excel\Concerns\WithCustomCsvSettings;
use robman2100\Excel\Exceptions\NoTypeDetectedException;

class ReaderFactory
{
    use MapsCsvSettings;

    /**
     * @param object        $import
     * @param TemporaryFile $file
     * @param string        $readerType
     *
     * @throws Exception
     * @return IReader
     */
    public static function make($import, TemporaryFile $file, string $readerType = null): IReader
    {
        $reader = IOFactory::createReader(
            $readerType ?: static::identify($file)
        );

        if (method_exists($reader, 'setReadDataOnly')) {
            $reader->setReadDataOnly(config('excel.imports.read_only', true));
        }

        if ($reader instanceof Csv) {
            static::applyCsvSettings(config('excel.imports.csv', []));

            if ($import instanceof WithCustomCsvSettings) {
                static::applyCsvSettings($import->getCsvSettings());
            }

            $reader->setDelimiter(static::$delimiter);
            $reader->setEnclosure(static::$enclosure);
            $reader->setEscapeCharacter(static::$escapeCharacter);
            $reader->setContiguous(static::$contiguous);
            $reader->setInputEncoding(static::$inputEncoding);
        }

        return $reader;
    }

    /**
     * @param TemporaryFile $temporaryFile
     *
     * @throws NoTypeDetectedException
     * @return string
     */
    private static function identify(TemporaryFile $temporaryFile): string
    {
        try {
            return IOFactory::identify($temporaryFile->getLocalPath());
        } catch (Exception $e) {
            throw new NoTypeDetectedException(null, null, $e);
        }
    }
}
