<?php

namespace Astatroth\LaravelConfig;

use Illuminate\Config\Repository as RepositoryBase;

class Repository extends RepositoryBase
{
    protected $writer;

    public function __construct($items = [], $writer)
    {
        $this->writer = $writer;

        parent::__construct($items);
    }

    public function write($key, $value)
    {
        list($filename, $item) = $this->parseKey($key);

        $result = $this->writer->write($item, $value, $filename);

        if (!$result) {
            throw new \Exception('File could not be written to.');
        }

        $this->set($key, $value);
    }

    private function parseKey($key)
    {
        return preg_split('/\./', $key, 2);
    }
}