<?php

namespace splFileOBject\readers;

use splFileOBject\exception\ReaderException;
use SplFileObject;
use splFileOBject\interfaces\Reader;

/**
 * Class CsvReader
 * @package splFileOBject\readers
 * @author Liangshiqing
 */
class CsvReader implements Reader
{
    /**
     * @var SplFileObject|string|null
     */
    public $file;

    /**
     * @var int
     */
    public $skip = 0;

    /**
     * @var int|null
     */
    private $_totalLines;

    /**
     * CsvReader constructor.
     * @param $file
     * @param $skip
     * @throws ReaderException
     */
    public function __construct($file, $skip = 0)
    {
        $this->file = $file;
        $this->skip = $skip;
        if (!$this->file instanceof SplFileObject) {
            if (!is_file($this->file) || !is_readable($this->file)) {
                throw new ReaderException("No exists file or not readable {$this->file}");
            }
            $this->file = new SplFileObject($this->file,'r');
        }

        if (!$this->file instanceof SplFileObject) {
            throw new ReaderException("Reader file error");
        }

//        if (strpos($this->file->fgets(), "\r\n") !== false) {
//            throw new ReaderException("The csv file must be LF line tail");
//        }
    }


    /**
     * @return array|false
     */
    public function getFields()
    {
        $this->file->setFlags(SplFileObject::DROP_NEW_LINE | SplFileObject::SKIP_EMPTY);
        $this->file->rewind();
        $fields = $this->file->fgetcsv();
        $fields = array_map('trim', $fields);

        return array_combine($fields, $fields);
    }

    /**
     * @return int|null
     */
    public function getTotalLine()
    {
        if ($this->_totalLines !== null) {
            return $this->_totalLines;
        }

        $this->file->setFlags(SplFileObject::READ_AHEAD | SplFileObject::DROP_NEW_LINE | SplFileObject::SKIP_EMPTY);
        $this->file->seek(PHP_INT_MAX);
        $this->_totalLines = $this->file->key();

        return $this->_totalLines;

    }

    /**
     * 倒带 跳过行首
     *
     * @return \Generator
     */
    public function getIterator()
    {
        $fields = $this->getFields();
        $totalFields = count($fields);

        $this->file->rewind();
        $this->file->fgets();

        if ($this->skip) {
            for ($i = 0; $i < $this->skip; ++$i) {
                $this->file->fgets();
            }
        }

        while (!$this->file->eof() && $row = $this->file->fgetcsv()) {
            if ($totalFields - count($row) > 0) {
                $row = array_pad($row, $totalFields, '');
            }
            $row = array_combine($fields, $row);
            yield  $row;
        }
    }
}