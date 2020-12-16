<?php
    namespace EffectConnect\PHPSdk\Core\Helper;

    use EffectConnect\PHPSdk\Core\Exception\InvalidReflectionException;

    /**
     * Class Reflector
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     *
     */
    final class Reflector
    {
        const TYPE_NO_ADDITIONS      = 0;
        const TYPE_PREFIXED_ADDITION = 1;
        const TYPE_SUFFIXED_ADDITION = 2;
        const TYPE_FULL_ADDITION     = 3;

        private static $_prefix;
        private static $_suffix;
        private static $_additionType;

        /**
         * @param $predefined
         *
         * @throws InvalidReflectionException
         */
        private static function _setup($predefined)
        {
            self::$_additionType = self::TYPE_NO_ADDITIONS;
            if ($predefined !== null)
            {
                $exploded = array_filter(explode('%', $predefined));
                if (count($exploded) == 1)
                {
                    if (strpos($predefined, '%') === 0)
                    {
                        self::$_additionType = self::TYPE_SUFFIXED_ADDITION;
                        self::$_suffix       = strrev(array_shift($exploded));
                    } else
                    {
                        self::$_additionType = self::TYPE_PREFIXED_ADDITION;
                        self::$_prefix       = array_shift($exploded);
                    }
                } else
                {
                    if (count($exploded) > 2)
                    {
                        throw new InvalidReflectionException();
                    }
                    self::$_additionType = self::TYPE_FULL_ADDITION;
                    self::$_prefix       = array_shift($exploded);
                    self::$_suffix       = array_shift($exploded);
                }
            }
        }

        /**
         * @param string $class
         * @param mixed $variable
         * @param string|null $predefined
         *
         * @return bool
         *
         * @throws InvalidReflectionException
         * @throws \ReflectionException
         */
        final public static function isValid($class, $variable, $predefined=null)
        {
            self::_setup($predefined);
            $reflection      = new \ReflectionClass($class);
            foreach ($reflection->getConstants() as $constant => $validConstant)
            {
                if ($validConstant !== $variable)
                {
                    continue;
                }
                switch (self::$_additionType)
                {
                    case self::TYPE_PREFIXED_ADDITION:
                        return (strpos($constant, self::$_prefix) === 0);
                        break;
                    case self::TYPE_SUFFIXED_ADDITION:
                        return (strpos(strrev($constant), self::$_suffix) === 0);
                        break;
                    case self::TYPE_FULL_ADDITION:
                        return ((strpos($constant, self::$_prefix) === 0 && strpos(strrev($constant), self::$_suffix) === 0));
                        break;
                    case self::TYPE_NO_ADDITIONS:
                    default:
                        return true;
                        break;
                }
            }

            return false;
        }
    }