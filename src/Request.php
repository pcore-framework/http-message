<?php

declare(strict_types=1);

namespace PCore\HttpMessage;

use PCore\HttpMessage\Bags\HeaderBag;
use Psr\Http\Message\{RequestInterface, StreamInterface, UriInterface};

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
        $this->headers = new HeaderBag($headers);
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
        if ($requestTarget === $this->requestTarget) {
            return $this;
        }
        $new = clone $this;
        $new->requestTarget = $requestTarget;
        return $new;
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
        if ($method === $this->method) {
            return $this;
        }
        $new = clone $this;
        $new->method = $method;
        return $new;
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
        if ($uri === $this->uri) {
            return $this;
        }
        $new = clone $this;
        if (true === $preserveHost) {
            $uri = $uri->withHost($this->getHeaderLine('Host'));
        }
        $new->uri = $uri;
        return $new;
    }

}