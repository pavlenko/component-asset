<?php

namespace PE\Component\Asset\Exception;

class UnexpectedValueException extends \UnexpectedValueException implements ExceptionInterface
{
    /**
     * Constructor
     *
     * @param string $value   Invalid value
     * @param string $type    Expected type
     * @param string $message Optional custom message
     */
    public function __construct($value, $type, $message = null)
    {
        parent::__construct(sprintf(
            $message ?: 'Expected value type of %s, %s given',
            $type,
            is_object($value) ? get_class($value) : gettype($value)
        ));
    }

    /**
     * @param mixed  $value       Checked value
     * @param string $type        Expected type
     * @param bool   $isArrayItem Internal usage only
     *
     * @throws static
     */
    public static function validate($value, $type, $isArrayItem = false)
    {
        $prefix = $isArrayItem ? 'Expected array item' : 'Expected value';

        if ('[]' === substr($type, -2)) {
            if (!is_array($value)) {
                // Validate array itself
                throw new static($value, $type, $prefix . ' type of %s, %s given');
            }

            foreach ($value as $item) {
                // Validate array items
                static::validate($item, substr($type, 0, -2), true);
            }
        } else {
            if (function_exists('is_' . $type)) {
                // Validate simple type
                if (!call_user_func('is_' . $type, $value)) {
                    throw new static($value, $type, $prefix . ' type of %s, %s given');
                }
            } else if (!($value instanceof $type)) {
                // Validate object type
                throw new static($value, $type, $prefix . ' instance of %s, %s given');
            }
        }
    }
}