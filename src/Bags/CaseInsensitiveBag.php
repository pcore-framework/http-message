<?php

namespace PCore\HttpMessage\Bags;

/**
 * Class CaseInsensitiveBag
 * @package PCore\HttpMessage\Bags
 * @github https://github.com/pcore-framework/http-message
 */
class CaseInsensitiveBag extends ParameterBag
{

    protected array $map = [];

    /**
     * @param array $parameters
     * @return void
     */
    public function replace(array $parameters = [])
    {
        foreach ($parameters as $key => $parameter) {
            $upperCaseKey = strtoupper($key);
            $this->parameters[$upperCaseKey] = $parameter;
            $this->map[$upperCaseKey] = $key;
        }
    }

    /**
     * @param string $key
     * @param $default
     * @return mixed
     */
    public function get(string $key, $default = null): mixed
    {
        return parent::get(strtoupper($key), $default);
    }

    /**
     * @param string $key
     * @param $value
     * @return void
     */
    public function set(string $key, $value)
    {
        parent::set(strtoupper($key), $value);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return parent::has(strtoupper($key));
    }

    /**
     * @param string $key
     * @return void
     */
    public function remove(string $key)
    {
        parent::remove(strtoupper($key));
    }

}