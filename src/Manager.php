<?php

declare(strict_types=1);

namespace TimurFlush\Auth;

use TimurFlush\Auth\Exception\InvalidArgumentException;
use TimurFlush\Auth\Hashing\BCrypt;
use TimurFlush\Auth\Hashing\HashingInterface;

class Manager
{
    /**
     * @var bool
     */
    protected static bool $initialized = false;

    /**
     * @var array|null
     */
    protected static ?array $options;

    /**
     * Sets or returns the library options.
     *
     * @param null $options
     * @return mixed
     *
     * @throws Exception                If the options are not initialized
     * @throws InvalidArgumentException If a specified option does not exists.
     * @throws InvalidArgumentException If a specified option is not a necessary type.
     * @throws InvalidArgumentException If the `$options` argument is not null, an array or a string.
     */
    public static function options($options = null)
    {
        if (!self::$initialized) {
            throw new Exception(
                'Before call the static::options() method, you need to call the static::initialize()'
            );
        }

        if ($options === null) {
            return array_map(
                fn($value) => $value['value'],
                static::$options
            );
        } elseif (is_string($options)) {
            if (!isset(static::$options[$options])) {
                throw new InvalidArgumentException("The `" . $options . "` does not exist.");
            }

            return static::$options[$options]['value'];
        } elseif (is_array($options)) {
            foreach ($options as $key => $value) {
                if (!isset(static::$options[$key])) {
                    throw new InvalidArgumentException("The `" . $key . "` does not exist.");
                }

                $type = static::$options[$key]['type'];

                if (
                    !(
                        function_exists($functionName = 'is_' . $type) &&
                        $functionName($value)
                    ) &&
                    !(
                        $value instanceof $type
                    )
                ) {
                    throw new InvalidArgumentException(
                        "The `" . $key . "` option must be of the type " . $type
                    );
                }

                static::$options[$key]['value'] = $value;
            }
        } else {
            throw new InvalidArgumentException('The `options` argument must be null, string or array.');
        }
    }

    /**
     * Initialize options.
     *
     * @return void
     */
    public static function initialize(): void
    {
        if (static::$initialized) {
            return;
        }

        /**
         * After adding a new item, please also add it to the
         * the tests/unit/Manager/StateCest::describeDefaultOptions().
         */
        $options = [
            'userModel.allowZeroId' => [
                'type'  => 'bool',
                'value' => false
            ],
            'userModel.allowNegativeId' => [
                'type'  => 'bool',
                'value' => false
            ],
            'hashing.default' => [
                'type'  => HashingInterface::class,
                'value' => new BCrypt()
            ],
            'date.format' => [
                'type'  => 'string',
                'value' => 'Y-m-d H:i:s.uO'
            ],
        ];

        static::$options = $options;
        static::$initialized = true;
    }

    /**
     * Resets the state.
     *
     * @return void
     */
    public static function reset(): void
    {
        static::$options = null;
        static::$initialized = false;
    }
}
