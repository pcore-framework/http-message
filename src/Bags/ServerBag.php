<?php

namespace PCore\HttpMessage\Bags;

/**
 * Class ServerBag
 * @package PCore\HttpMessage\Bags
 * @github https://github.com/pcore-framework/http-message
 */
class ServerBag extends CaseInsensitiveBag
{

    /**
     * Получает HTTP-заголовки
     * @return array
     */
    public function getHeaders(): array
    {
        $headers = [];
        foreach ($this->parameters as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $headers[str_replace('_', '-', substr($key, 5))] = $value;
            } elseif (in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH', 'CONTENT_MD5'], true)) {
                $headers[str_replace('_', '-', $key)] = $value;
            }
        }
        if (isset($this->parameters['PHP_AUTH_USER'])) {
            $headers['PHP_AUTH_USER'] = $this->parameters['PHP_AUTH_USER'];
            $headers['PHP_AUTH_PW'] = $this->parameters['PHP_AUTH_PW'] ?? '';
        } else {
            $authorizationHeader = null;
            if (isset($this->parameters['HTTP_AUTHORIZATION'])) {
                $authorizationHeader = $this->parameters['HTTP_AUTHORIZATION'];
            } elseif (isset($this->parameters['REDIRECT_HTTP_AUTHORIZATION'])) {
                $authorizationHeader = $this->parameters['REDIRECT_HTTP_AUTHORIZATION'];
            }
            if ($authorizationHeader !== null) {
                if (stripos($authorizationHeader, 'basic ') === 0) {
                    $exploded = explode(':', base64_decode(substr($authorizationHeader, 6)), 2);
                    if (count($exploded) == 2) {
                        [$headers['PHP_AUTH_USER'], $headers['PHP_AUTH_PW']] = $exploded;
                    }
                } elseif (empty($this->parameters['PHP_AUTH_DIGEST']) && (stripos($authorizationHeader, 'digest ') === 0)) {
                    $headers['PHP_AUTH_DIGEST'] = $authorizationHeader;
                    $this->parameters['PHP_AUTH_DIGEST'] = $authorizationHeader;
                } elseif (stripos($authorizationHeader, 'bearer ') === 0) {
                    $headers['AUTHORIZATION'] = $authorizationHeader;
                }
            }
        }
        if (isset($headers['AUTHORIZATION'])) {
            return $headers;
        }
        if (isset($headers['PHP_AUTH_USER'])) {
            $headers['AUTHORIZATION'] =
                'Basic ' . base64_encode($headers['PHP_AUTH_USER'] . ':' . $headers['PHP_AUTH_PW']);
        } elseif (isset($headers['PHP_AUTH_DIGEST'])) {
            $headers['AUTHORIZATION'] = $headers['PHP_AUTH_DIGEST'];
        }
        return $headers;
    }

}