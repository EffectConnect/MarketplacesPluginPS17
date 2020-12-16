<?php
    namespace EffectConnect\PHPSdk\Core\Helper;

    use SimpleXMLElement;

    /**
     * Class EffectConnectXMLElement
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     *
     */
    final class EffectConnectXMLElement extends SimpleXMLElement
    {
        /**
         * @param SimpleXMLElement $parent
         *
         * @param string $name
         * @param mixed $value
         *
         * @return SimpleXMLElement
         */
        public static function addCDataChild($parent, $name, $value)
        {
            $child = $parent->addChild($name);
            if ($value)
            {
                if ($domElement = dom_import_simplexml($child))
                {
                    $domOwner = $domElement->ownerDocument;
                    $domElement->appendChild($domOwner->createCDATASection($value));
                }
            }
            return $child;
        }

        /**
         * @param SimpleXMLElement $to
         * @param SimpleXMLElement $from
         */
        public static function insert($to, $from)
        {
            $toDom   = dom_import_simplexml($to);
            $fromDom = dom_import_simplexml($from);
            $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
        }
    }