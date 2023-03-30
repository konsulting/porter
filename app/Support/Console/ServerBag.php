<?php

namespace App\Support\Console;

class ServerBag
{
    /**
     * Hold the values.
     *
     * @var array
     */
    protected $server;

    /**
     * ServerBag constructor. Allow us to override some values for testing.
     *
     * @param array|null $overrides
     */
    public function __construct(array $overrides = null)
    {
        $overrides = $overrides ? array_change_key_case($overrides, CASE_LOWER) : [];

        foreach ($_SERVER as $key => $value) {
            $key = strtolower($key);

            $this->server[$key] = $overrides[$key] ?? $value;
        }
    }

    /**
     * Get a server variable.
     *
     * @param $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return $this->server[strtolower((string) $key)];
    }
}
