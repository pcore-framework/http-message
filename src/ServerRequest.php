<?php

namespace PCore\HttpMessage;

use PCore\Session\Session;
use PCore\Utils\Arr;
use RuntimeException;

/**
 * Class ServerRequest
 * @package PCore\HttpMessage
 * @github https://github.com/pcore-framework/http-message
 */
class ServerRequest extends BaseServerRequest
{

    /**
     * @param string $name
     * @return string
     */
    public function header(string $name): string
    {
        return $this->getHeaderLine($name);
    }

    /**
     * @param string $name
     * @return ?string
     */
    public function server(string $name): ?string
    {
        return $this->getServerParams()[strtoupper($name)] ?? null;
    }

    /**
     * @param string $method
     * @return bool
     */
    public function isMethod(string $method): bool
    {
        return strcasecmp($this->getMethod(), $method) === 0;
    }

    /**
     * @return string
     */
    public function url(): string
    {
        return $this->getUri()->__toString();
    }

    /**
     * @param string $name
     * @return string|null
     */
    public function cookie(string $name): ?string
    {
        return $this->getCookieParams()[strtoupper($name)] ?? null;
    }

    /**
     * @return bool
     */
    public function isAjax(): bool
    {
        return strcasecmp('XMLHttpRequest', $this->getHeaderLine('X-REQUESTED-WITH')) === 0;
    }

    /**
     * @param string $path
     * @return bool
     */
    public function isPath(string $path): bool
    {
        $requestPath = $this->getUri()->getPath();
        return strcasecmp($requestPath, $path) === 0 || preg_match("#^{$path}$#iU", $requestPath);
    }

    /**
     * @return string
     */
    public function raw(): string
    {
        return $this->getBody()->getContents();
    }

    /**
     * @param null|array|string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function get(null|array|string $key = null, mixed $default = null): mixed
    {
        return $this->input($key, $default, $this->getQueryParams());
    }

    /**
     * @param null|array|string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function post(null|array|string $key = null, mixed $default = null): mixed
    {
        return $this->input($key, $default, $this->getParsedBody());
    }

    /**
     * @param $request
     * @param array $keys
     * @param array|null $default
     * @return array
     */
    public function inputs($request, array $keys, array $default = null): array
    {
        $data = json_decode($request->raw(), true);
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = data_get($data, $key, $default[$key] ?? null);
        }
        return $result;
    }

    /**
     * @param null|array|string $key
     * @param mixed|null $default
     * @param array|null $from
     * @return mixed
     */
    public function input(null|array|string $key = null, mixed $default = null, ?array $from = null): mixed
    {
        $from ??= $this->all();
        if (is_null($key)) {
            return $from ?? [];
        }
        if (is_array($key)) {
            $return = [];
            foreach ($key as $value) {
                $return[$value] = $this->isEmpty($from, $value) ? ($default[$value] ?? null) : $from[$value];
            }
            return $return;
        }
        return $this->isEmpty($from, $key) ? $default : $from[$key];
    }

    /**
     * @param array $haystack
     * @param $needle
     * @return bool
     */
    protected function isEmpty(array $haystack, $needle): bool
    {
        return !isset($haystack[$needle]) || $haystack[$needle] === '';
    }

    /**
     * @param string $field
     * @return UploadedFile|null
     */
    public function file(string $field): ?UploadedFile
    {
        return Arr::get($this->files(), $field);
    }

    /**
     * @return UploadedFile[]
     */
    public function files(): array
    {
        return $this->getUploadedFiles();
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->getQueryParams() + $this->getParsedBody();
    }

    /**
     * @return ?Session
     */
    public function session(): ?Session
    {
        if ($session = $this->getAttribute('PCore\Session\Session')) {
            return $session;
        }
        throw new RuntimeException('Сессия недействительна.');
    }

    /**
     * @return string
     */
    public function getRealIp(): string
    {
        $headers = $this->getHeaders();
        if (isset($headers['x-forwarded-for'][0]) && !empty($headers['x-forwarded-for'][0])) {
            return $headers['x-forwarded-for'][0];
        }
        if (isset($headers['x-real-ip'][0]) && !empty($headers['x-real-ip'][0])) {
            return $headers['x-real-ip'][0];
        }
        $serverParams = $this->getServerParams();
        return $serverParams['remote_addr'] ?? '';
    }

}