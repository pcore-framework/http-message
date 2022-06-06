<?php

declare(strict_types=1);

namespace PCore\HttpMessage;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class Request
 * @package PCore\HttpMessage
 * @github https://github.com/pcore-framework/http-message
 */
class Request extends Message implements RequestInterface
{

    protected UriInterface $uri;
    protected string $requestTarget = '/';

    public function __construct(
        protected string $method,
        string|UriInterface $uri,
        array $headers = [],
        string|null|StreamInterface $body = null,
        protected string $protocolVersion = '1.1'
    )
    {
        $this->uri = $uri instanceof UriInterface ? $uri : new Uri($uri);
        $this->formatBody($body);
        $this->formatHeaders($headers);
    }

    /**
     * @inheritDoc
     */
    public function getRequestTarget()
    {
        if ('/' === $this->requestTarget) {
            return $this->uri->getPath() . $this->uri->getQuery();
        }
        return '/';
    }

    /**
     * @inheritDoc
     */
    public function withRequestTarget($requestTarget)
    {
        $this->requestTarget = $requestTarget;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @inheritDoc
     */
    public function withMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @inheritDoc
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        if (true === $preserveHost) {
            $uri = $uri->withHost($this->getHeaderLine('Host'));
        }
        $this->uri = $uri;
        return $this;
    }

}