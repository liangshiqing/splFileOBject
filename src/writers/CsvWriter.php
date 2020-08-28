<?php


namespace splFileOBject\writers;

use splFileOBject\exception\WriterException;
use splFileOBject\interfaces\Writer;
use splFileOBject;

/**
 * Class CsvWriter
 * @package splFileOBject\writers
 * @author Liangshiqing
 */
class CsvWriter implements Writer
{

    /**
     * @var splFileOBject
     */
    public $file;

    /**
     * @var array
     */
    public $header = [];

    /**
     * @var array
     */
    private $_buffer = [];

    /**
     * @var bool
     */
    private $_headerSent = false;

    /**
     * CsvWriter constructor.
     * @param $file
     * @param $header
     * @throws WriterException
     */
    public function __construct($file, array $header)
    {
        $this->file = $file;
        $this->header = $header;

        if (!$this->file instanceof SplFileObject) {

            $dirname = dirname($this->file);

            if (!file_exists($dirname)) {
                mkdir($dirname, 0755, true);
            }
            if (!is_writable($dirname)) {
                throw new WriterException("No exists file or not writable: {$this->file}");
            }
            $this->file = new SplFileObject($this->file, 'wb');
        }
    }

    /**
     * write
     *
     * @param $fields
     * @return $this
     */
    public function writeln($fields)
    {
        $this->_sendHeader();
        $fields = (array)$fields;
        if ($this->_buffer) {
            $fields = array_merge($this->_buffer, $fields);
            $this->_buffer = [];
        }

        foreach ($fields as &$field) {
            if (!is_object($field) && method_exists($field, '__toString')) {
                $field = (string)$field;
            }
        }

        $this->file->fputcsv($fields);

        return $this;
    }

    /**
     * Temporary save without writing
     *
     * @param array $column
     * @return $this
     */
    public function write(array $column)
    {
        $this->_buffer = array_merge($this->_buffer, $column);
        return $this;
    }

    /**
     * set header
     */
    private function _sendHeader()
    {
        if (!$this->_headerSent) {
            $header = $this->header;
            $this->file->fputcsv($header);
            $this->_headerSent = true;
        }
    }

}