<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Cookies;

use Carbon\Carbon;
use JsonException;
use DateTimeInterface;
use TimurFlush\Auth\Exception;

class Native implements CookiesInterface
{
    /**
     * @var array
     */
    protected array $options = [
        'name'      => 'TF_UserSession',
        'domain'    => '',
        'path'      => '/',
        'secure'    => false,
    ];

    /**
     * @var string
     */
    protected string $moduleName;

    /**
     * Native constructor.
     *
     * @param array|null $options
     */
    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->options = array_merge(
                $this->options,
                $options
            );
        }
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception Please see the method `static::getKey()`
     */
    public function set($value, DateTimeInterface $dateTime, bool $secure = null, string $path = null, string $domain = null): void
    {
        $diff = Carbon::now()->diffInSeconds($dateTime, true);

        setcookie(
            $this->getKey(),
            json_encode($value),
            time() + $diff,
            $path ?? $this->options['path'],
            $domain ?? $this->options['domain'],
            $secure ?? $this->options['secure'],
            true
        );
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception Please see the method `static::getKey()`
     */
    public function get(): ?string
    {
        if (isset($_COOKIE[$this->getKey()])) {
            $value = $_COOKIE[$this->getKey()];

            try {
                return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $exception) {
                $this->remove();
            }
        }

        return null;
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception Please see the method `static::set()`
     */
    public function remove(): void
    {
        $this->set(null, Carbon::createFromTimestamp(1));
    }

    /**
     * {@inheritDoc}
     */
    public function setModuleName(string $module)
    {
        $this->moduleName = $module;
    }

    /**
     * Returns the cookie key.
     *
     * @throws Exception If you haven't set the module name
     *
     * @return string
     */
    protected function getKey()
    {
        if (!isset($this->moduleName)) {
            throw new Exception('Before using this adapter you need to set a module name.');
        }

        return join('-', [
            $this->options['name'],
            $this->moduleName
        ]);
    }
}
