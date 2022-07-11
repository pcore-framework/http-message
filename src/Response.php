<?php

namespace PCore\HttpMessage;

use ArrayAccess;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Response
 * @package PCore\HttpMessage
 * @github https://github.com/pcore-framework/http-message
 */
class Response extends BaseResponse
{

    /**
     * @param array|ArrayAccess $data
     * @param int $status
     * @return ResponseInterface
     */
    public static function json($data, int $status = 200): ResponseInterface
    {
        return new static(
            $status,
            ['Content-Type' => 'application/json; charset=utf-8'],
            json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }

    /**
     * @param string $name
     * @param string $value
     * @param int $expires
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httponly
     * @param string $samesite
     * @return $this
     */
    public function withCookie(string $name, string $value, int $expires = 3600, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false, string $samesite = ''): static
    {
        $cookie = new Cookie(...func_get_args());
        return $this->withAddedHeader('Set-Cookie', $cookie->__toString());
    }

}