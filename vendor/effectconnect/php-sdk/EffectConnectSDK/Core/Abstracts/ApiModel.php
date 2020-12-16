<?php
    namespace EffectConnect\PHPSdk\Core\Abstracts;

    use EffectConnect\PHPSdk\Core\Exception\InvalidPropertyException;
    use EffectConnect\PHPSdk\Core\Helper\EffectConnectXMLElement;

    /**
     * Class ApiModel
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     *
     */
    abstract class ApiModel
    {
        /**
         * @return string
         *
         * @throws InvalidPropertyException
         */
        public function getXml()
        {
            $xmlPayload = new \SimpleXMLElement('<'.$this->getName().'/>');
            $properties  = get_object_vars($this);
            foreach (array_keys($properties) as $property)
            {
                $cleanProperty = ltrim($property, '_');
                $action = 'get'.ucfirst($cleanProperty);
                if (!method_exists($this, $action))
                {
                    throw new InvalidPropertyException();
                }
                $value = $this->{$action}();
                if ($value !== null)
                {
                    if ($value instanceof ApiModel)
                    {
                        $modelValue = simplexml_load_string($value->getXml());
                        EffectConnectXMLElement::insert($xmlPayload, $modelValue);
                    } elseif (is_array($value))
                    {
                        if (count($value) === 0)
                        {
                            continue;
                        }
                        if ($this->isIterator())
                        {
                            foreach ($value as $parent => $list) {
                                if ($list instanceof ApiModel)
                                {
                                    $xml = simplexml_load_string($list->getXml());
                                    EffectConnectXMLElement::insert($xmlPayload, $xml);
                                } elseif (is_string($list))
                                {
                                    EffectConnectXMLElement::addCDataChild($xmlPayload, $cleanProperty, $list);
                                } elseif (is_array($list))
                                {
                                    $container = $xmlPayload->addChild($cleanProperty);
                                    foreach ($list as $item)
                                    {
                                        EffectConnectXMLElement::addCDataChild($container, $parent, $item);
                                    }
                                } else
                                {
                                    if (is_bool($list))
                                    {
                                        $list = $list?'true':'false';
                                    }
                                    $xmlPayload->addChild($parent, $list);
                                }
                            }
                        } else
                        {
                            $iterableElement = $xmlPayload->addChild($cleanProperty);
                            foreach ($value as $parent => $list)
                            {
                                if ($list instanceof ApiModel)
                                {
                                    EffectConnectXMLElement::insert($iterableElement, simplexml_load_string($list->getXml()));
                                } elseif(is_array($list))
                                {
                                    EffectConnectXMLElement::addCDataChild($iterableElement, key($list), current($list));
                                }
                                else
                                {
                                    if (is_string($list))
                                    {
                                        EffectConnectXMLElement::addCDataChild($iterableElement, $parent, $list);
                                    } else
                                    {
                                        if (is_bool($list))
                                        {
                                            $list = $list?'true':'false';
                                        }
                                        $iterableElement->addChild($parent, $list);
                                    }
                                }
                            }
                        }
                    } else
                    {
                        if (is_string($value))
                        {
                            EffectConnectXMLElement::addCDataChild($xmlPayload, $cleanProperty, $value);
                        } else
                        {
                            if (is_bool($value))
                            {
                                $value = $value?'true':'false';
                            }
                            $xmlPayload->addChild($cleanProperty, $value);
                        }
                    }
                }
            }

            return $xmlPayload->asXML();
        }

        /**
         * @return bool
         */
        protected function isIterator()
        {
            return false;
        }

        /**
         * @return string
         */
        public abstract function getName();
    }
