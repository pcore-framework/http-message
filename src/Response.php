<?php

namespace PCore\HttpMessage;

use PCore\HttpMessage\Stream\StringStream;
use Psr\Http\Message\{ResponseInterface, StreamInterface};

/**
 * Class Response
 * @package PCore\HttpMessage
 * @github https://github.com/pcore-framework/http-message
 */
class Response extends BaseResponse
{

    public function json($data, int $status = 200): ResponseInterface
    {
        return $this->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->end(json_encode($data), $status);
    }

    public function end(null|StreamInterface|string $data = null, int $status = 200): ResponseInterface
    {
        return $this->withStatus($status)
            ->withBody($data instanceof StreamInterface
                ? $data
                : new StringStream((string)$data));
    }

}