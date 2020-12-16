<?php
    namespace EffectConnect\PHPSdk\Core\Helper;

    /**
     * Class Payload
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class Payload
    {
        /**
         * @param      $payload
         * @param      $field
         * @param bool $iteration
         *
         * @return mixed|null|\SimpleXMLElement|string
         */
        final public static function extract($payload, $field, $iteration=false)
        {
            if (is_array($payload))
            {
                return self::_extractFromJson($payload, $field);
            } elseif ($payload instanceof \SimpleXMLElement)
            {
                return self::_extractFromXml($payload, $field, $iteration);
            }

            return null;
        }

        /**
         * @param $payload
         * @param $field
         *
         * @return bool
         */
        final public static function contains($payload, $field)
        {
            if (is_array($payload))
            {
                return array_key_exists($field, $payload);
            } elseif ($payload instanceof \SimpleXMLElement)
            {
                return isset($payload->{$field});
            }

            return false;
        }

        /**
         * @param array $payload
         * @param       $field
         * @param bool  $iteration
         *
         * @return mixed|null
         */
        private static function _extractFromJson(array $payload, $field, $iteration=false)
        {
            if (array_key_exists($field, $payload))
            {
                return $payload[$field];
            }
            if ($iteration)
            {
                return reset($payload);
            }

            return null;
        }

        /**
         * @param \SimpleXMLElement $payload
         * @param                   $field
         * @param bool              $iteration
         *
         * @return array|null|\SimpleXMLElement|string
         */
        private static function _extractFromXml(\SimpleXMLElement $payload, $field, $iteration=false)
        {
            if (isset($payload->{$field}))
            {
                $value = $payload->{$field};
                if (count($value->children()) === 0)
                {
                    return (string)$value;
                } else
                {
                    $newValue = $value;
                    if ($iteration)
                    {
                        $newValue = [];
                        foreach ($value->children() as $child)
                        {
                            $newValue[] = $child;
                        }
                    }
                    return $newValue;
                }
            } elseif ($iteration)
            {
                return $payload;
            }

            return null;
        }
    }