<?php
    namespace EffectConnect\PHPSdk\Core\Model\Response;

    use EffectConnect\PHPSdk\Core\Helper\Payload;

    /**
     * Class ShippingAddress
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class ShippingAddress
    {
        /**
         * @var string $_salutation
         */
        private $_salutation;
        /**
         * @var string $_firstName
         */
        private $_firstName;
        /**
         * @var string $_lastName
         */
        private $_lastName;
        /**
         * @var string $_company
         */
        private $_company;
        /**
         * @var string $_street
         */
        private $_street;
        /**
         * @var int $_houseNumber
         */
        private $_houseNumber;
        /**
         * @var string $_houseNumberExtension
         */
        private $_houseNumberExtension;
        /**
         * @var string $_addressNote
         */
        private $_addressNote;
        /**
         * @var string $_zipCode
         */
        private $_zipCode;
        /**
         * @var string $_city
         */
        private $_city;
        /**
         * @var string $_state
         */
        private $_state;
        /**
         * @var string $_country
         */
        private $_country;
        /**
         * @var string $_phone
         */
        private $_phone;
        /**
         * @var string $_taxNumber
         */
        private $_taxNumber;
        /**
         * @var string $_email
         */
        private $_email;

        /**
         * BillingAddress constructor.
         *
         * @param $payload
         */
        public function __construct($payload)
        {
            if ($payload === null)
            {
                return;
            }
            $this->_salutation           = Payload::extract($payload, 'salutation');
            $this->_firstName            = Payload::extract($payload, 'firstName');
            $this->_lastName             = Payload::extract($payload, 'lastName');
            $this->_company              = Payload::extract($payload, 'company');
            $this->_street               = Payload::extract($payload, 'street');
            $this->_houseNumber          = Payload::extract($payload, 'houseNumber');
            $this->_houseNumberExtension = Payload::extract($payload, 'houseNumberExtension');
            $this->_addressNote          = Payload::extract($payload, 'addressNote');
            $this->_zipCode              = Payload::extract($payload, 'zipCode');
            $this->_city                 = Payload::extract($payload, 'city');
            $this->_state                = Payload::extract($payload, 'state');
            $this->_country              = Payload::extract($payload, 'country');
            $this->_phone                = Payload::extract($payload, 'phone');
            $this->_taxNumber            = Payload::extract($payload, 'taxNumber');
            $this->_email                = Payload::extract($payload, 'email');
        }

        /**
         * @return string
         */
        public function getSalutation()
        {
            return $this->_salutation;
        }

        /**
         * @return string
         */
        public function getFirstName()
        {
            return $this->_firstName;
        }

        /**
         * @return string
         */
        public function getLastName()
        {
            return $this->_lastName;
        }

        /**
         * @return string
         */
        public function getCompany()
        {
            return $this->_company;
        }

        /**
         * @return string
         */
        public function getStreet()
        {
            return $this->_street;
        }

        /**
         * @return int
         */
        public function getHouseNumber()
        {
            return $this->_houseNumber;
        }

        /**
         * @return string
         */
        public function getHouseNumberExtension()
        {
            return $this->_houseNumberExtension;
        }

        /**
         * @return string
         */
        public function getAddressNote()
        {
            return $this->_addressNote;
        }

        /**
         * @return string
         */
        public function getZipCode()
        {
            return $this->_zipCode;
        }

        /**
         * @return string
         */
        public function getCity()
        {
            return $this->_city;
        }

        /**
         * @return string
         */
        public function getState()
        {
            return $this->_state;
        }

        /**
         * @return string
         */
        public function getCountry()
        {
            return $this->_country;
        }

        /**
         * @return string
         */
        public function getPhone()
        {
            return $this->_phone;
        }

        /**
         * @return string
         */
        public function getTaxNumber()
        {
            return $this->_taxNumber;
        }

        /**
         * @return string
         */
        public function getEmail()
        {
            return $this->_email;
        }
    }
