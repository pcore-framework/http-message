<?php

namespace PCore\HttpMessage;

use Psr\Http\Message\ResponseInterface;

/**
 * Class Response
 * @package PCore\HttpMessage
 * @github https://github.com/pcore-framework/http-message
 */
class Response extends BaseResponse
{

    public static function json($data, int $status = 200): ResponseInterface
    {
        return new static(
            $status,
            ['Content-Type' => 'application/json; charset=utf-8'],
            json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }

}