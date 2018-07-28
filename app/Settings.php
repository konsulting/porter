<?php
namespace App;

use ArrayAccess;
use Illuminate\Support\Arr;

class Settings implements ArrayAccess
{
    protected $path;
    protected $data;

    public function __construct($path)
    {
        $this->path = $path;
        $this->data = json_decode(file_get_contents($path), true);
    }

    public function get($key)
    {
        return Arr::get($this->data, $key);
    }

    public function set($key, $value)
    {
        Arr::set($this->data, $key, $value);

        $this->save();
    }

    protected function save()
    {
        file_put_contents(
            $this->path,
            json_encode($this->data, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT)
        );
    }

    public function toArray()
    {
        return $this->data;
    }

    public function offsetExists($offset)
    {
        return Arr::exists($this->data, $offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        Arr::pull($this->data, $offset);

        $this->save();
    }
}
