<?php
namespace splFileOBject\interfaces;

/**
 * Interface Reader
 * @package splFileOBject\src\interfaces
 * @author Liangshiqing
 */
interface Reader
{
    public function getFields();

    public function getTotalLine();

    public function getIterator();
}