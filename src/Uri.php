<?php
namespace Selline\Http;
use Psr\Http\Message\UriInterface;
use Selline\Http\Exceptions\InvalidArgumentException;

final class Uri implements UriInterface
{
    private const SCHEMES = ['http' => 80, 'https' => 443];
    private const CHAR_UNRESERVED = 'a-zA-Z0-9_\-\.~';
    private const CHAR_SUB_DELIMS = '!\$&\'\(\)\*\+,;=';

    /**
     * Конструктор.
     * @param string $sourceUri
     * @throws InvalidArgumentException
     */
    public function __construct(
        string $sourceUri = ''
    ){
        if ('' !== $sourceUri) {
            if (false === $parts = parse_url($sourceUri)) {
                throw new InvalidArgumentException(sprintf("Unable to parse URI: %s", $sourceUri));
            }
            $this->applyUriParts($parts);
        }
    }

    private string $scheme = '';
    private string $userInfo = '';
    private string $host = '';
    private string $path = '';
    private string $query = '';
    private string $fragment = '';

    private ?int $port = null;


    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getAuthority(): string
    {
        if ('' === $this->host) {
            return '';
        }

        $authority = $this->host;
        if ('' !== $this->userInfo) {
            $authority = $this->userInfo . '@' . $authority;
        }

        if (null !== $this->port) {
            $authority .= ':' . $this->port;
        }

        return $authority;
    }

    public function getUserInfo(): string
    {
        return $this->userInfo;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getFragment(): string
    {
        return $this->fragment;
    }

    /**
     * @inheritDoc
     * @throws InvalidArgumentException
     */
    public function withScheme($scheme): self
    {
        if (!is_string($scheme)) {
            throw new InvalidArgumentException('Scheme must be a string');
        }

        if ($this->scheme === $scheme = strtr($scheme, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz')) {
            return $this;
        }

        $new = clone $this;
        $new->scheme = $scheme;
        $new->port = $new->filterPort($new->port);

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withUserInfo($user, $password = null): self
    {
        $info = $user;
        if (null !== $password && '' !== $password) {
            $info .= ':' . $password;
        }

        if ($this->userInfo === $info) {
            return $this;
        }

        $new = clone $this;
        $new->userInfo = $info;

        return $new;
    }

    /**
     * @inheritDoc
     * @throws InvalidArgumentException
     */
    public function withHost($host): self
    {
        if (!is_string($host)) {
            throw new InvalidArgumentException('Host must be a string');
        }

        if ($this->host === $host = strtr($host, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz')) {
            return $this;
        }

        $new = clone $this;
        $new->host = $host;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withPort($port): self
    {
        if ($this->port === $port = $this->filterPort($port)) {
            return $this;
        }

        $new = clone $this;
        $new->port = $port;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withPath($path): self
    {
        if ($this->path === $path = $this->filterPath($path)) {
            return $this;
        }

        $new = clone $this;
        $new->path = $path;

        return $new;
    }

    /**
     * @inheritDoc
     * @throws InvalidArgumentException
     */
    public function withQuery($query): self
    {
        if ($this->query === $query = $this->filterQueryAndFragment($query)) {
            return $this;
        }

        $new = clone $this;
        $new->query = $query;

        return $new;
    }

    /**
     * @inheritDoc
     * @throws InvalidArgumentException
     */
    public function withFragment($fragment): self
    {
        if ($this->fragment === $fragment = $this->filterQueryAndFragment($fragment)) {
            return $this;
        }

        $new = clone $this;
        $new->fragment = $fragment;

        return $new;
    }

    public function __toString()
    {
        return self::createUriString($this->scheme, $this->getAuthority(), $this->path, $this->query, $this->fragment);
    }

    private function applyUriParts(array $parts)
    {
        $this->scheme = isset($parts['scheme']) ? strtr($parts['scheme'], 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz') : '';
        $this->userInfo = $parts['user'] ?? '';
        $this->host = isset($parts['host']) ? strtr($parts['host'], 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz') : '';
        $this->port = isset($parts['port']) ? $this->filterPort($parts['port']) : null;
        $this->path = isset($parts['path']) ? $this->filterPath($parts['path']) : '';
        $this->query = isset($parts['query']) ? $this->filterQueryAndFragment($parts['query']) : '';
        $this->fragment = isset($parts['fragment']) ? $this->filterQueryAndFragment($parts['fragment']) : '';
        if (isset($parts['pass'])) {
            $this->userInfo .= ':' . $parts['pass'];
        }
    }

    /**
     * Is a given port non-standard for the current scheme?
     * @param string $scheme
     * @param int $port
     * @return bool
     */
    private static function isNonStandardPort(string $scheme, int $port): bool
    {
        return !isset(self::SCHEMES[$scheme]) || $port !== self::SCHEMES[$scheme];
    }

    /**
     * @param int|null $port
     * @return int|null
     */
    private function filterPort(?int $port): ?int
    {
        if (null === $port) {
            return null;
        }

        $port = (int) $port;
        if (0 > $port || 0xffff < $port) {
            throw new InvalidArgumentException(\sprintf('Invalid port: %d. Must be between 0 and 65535', $port));
        }

        return self::isNonStandardPort($this->scheme, $port) ? $port : null;
    }

    /**
     * @param array|string $path
     * @return string
     * @throws InvalidArgumentException
     *
     * @todo метод должен принимать только строки
     */
    private function filterPath(array|string $path): string
    {
        if (!is_string($path)) {
            throw new InvalidArgumentException('Path must be a string');
        }

        return preg_replace_callback('/(?:[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '%:@\/]++|%(?![A-Fa-f0-9]{2}))/', [__CLASS__, 'rawurlencodeMatchZero'], $path);
    }

    /**
     * @param string|array $str
     * @return string
     * @throws InvalidArgumentException
     *
     * @todo убрать дублтрование с filterPath()
     */
    private function filterQueryAndFragment(string|array $str): string
    {
        if (!is_string($str)) {
            throw new InvalidArgumentException('Query and fragment must be a string');
        }

        return preg_replace_callback('/(?:[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '%:@\/\?]++|%(?![A-Fa-f0-9]{2}))/', [__CLASS__, 'rawurlencodeMatchZero'], $str);
    }

    /**
     * @param array $match
     * @return string
     */
    private static function rawurlencodeMatchZero(array $match): string
    {
        return rawurlencode($match[0]);
    }

    private static function createUriString(string $scheme, string $authority, string $path, string $query, string $fragment)
    {
        $uri = '';
        if ('' !== $scheme) {
            $uri .= $scheme . ':';
        }

        if ('' !== $authority) {
            $uri .= '//' . $authority;
        }

        if ('' !== $path) {
            if ('/' !== $path[0]) {
                if ('' !== $authority) {
                    // If the path is rootless and an authority is present, the path MUST be prefixed by "/"
                    $path = '/' . $path;
                }
            } elseif (isset($path[1]) && '/' === $path[1]) {
                if ('' === $authority) {
                    // If the path is starting with more than one "/" and no authority is present, the
                    // starting slashes MUST be reduced to one.
                    $path = '/' . \ltrim($path, '/');
                }
            }

            $uri .= $path;
        }

        if ('' !== $query) {
            $uri .= '?' . $query;
        }

        if ('' !== $fragment) {
            $uri .= '#' . $fragment;
        }

        return $uri;
    }
}