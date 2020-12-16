<?php
    namespace EffectConnect\PHPSdk\Core\Helper;

    use EffectConnect\PHPSdk\Core\Exception\InvalidKeyException;

    /**
     * Class Keychain
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     *
     */
    final class Keychain
    {
        /**
         * @var string $_publicKey
         */
        private $_publicKey;

        /**
         * @var string $_secretKey
         */
        private $_secretKey;

        final public function _isValid()
        {
            return ($this->_publicKey && $this->_secretKey);
        }

        /**
         * @param string $publicKey
         *
         * @return Keychain
         * @throws InvalidKeyException
         */
        final public function setPublicKey($publicKey)
        {
            if (strlen($publicKey) !== 24)
            {
                throw new InvalidKeyException('Public');
            }
            $this->_publicKey = $publicKey;

            return $this;
        }

        /**
         * @param string $secretKey
         *
         * @return Keychain
         * @throws InvalidKeyException
         */
        final public function setSecretKey($secretKey)
        {
            if (strlen($secretKey) !== 32)
            {
                throw new InvalidKeyException('Secret');
            }
            $this->_secretKey = $secretKey;

            return $this;
        }

        /**
         * @return string
         */
        final public function getPublicKey()
        {
            return $this->_publicKey;
        }

        /**
         * @return string
         */
        final public function getSecretKey()
        {
            return $this->_secretKey;
        }
    }