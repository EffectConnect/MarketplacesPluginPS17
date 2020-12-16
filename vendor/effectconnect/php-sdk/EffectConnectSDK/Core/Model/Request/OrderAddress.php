<?php
    namespace EffectConnect\PHPSdk\Core\Model\Request;

    use EffectConnect\PHPSdk\Core\Abstracts\ApiModel;
    use EffectConnect\PHPSdk\Core\Exception\InvalidSalutationException;
    use EffectConnect\PHPSdk\Core\Helper\Reflector;
    use EffectConnect\PHPSdk\Core\Interfaces\ApiModelInterface;

    /**
     * Class OrderAddress
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     *
     */
    final class OrderAddress extends ApiModel implements ApiModelInterface
    {
        const SALUTATION_MALE   = 'm';
        const SALUTATION_FEMALE = 'f';
        const TYPE_SHIPPING     = 'shipping';
        const TYPE_BILLING      = 'billing';

        /**
         * Used to map the correct XML Node.
         *
         * @var string $_type
         */
        private $_type;

        /**
         * REQUIRED
         * @var string $_salutation
         *
         * Customer gender, according to the OrderAddress class constants.
         */
        protected $_salutation;

        /**
         * REQUIRED
         * @var string $_firstName
         *
         * Customer first name
         */
        protected $_firstName;

        /**
         * REQUIRED
         * @var string $_lastName
         *
         * Customer last name
         */
        protected $_lastName;

        /**
         * OPTIONAL
         * @var string $_company
         *
         * Company name
         */
        protected $_company;

        /**
         * REQUIRED
         * @var string $_street
         *
         * Street name
         */
        protected $_street;

        /**
         * REQUIRED
         * @var int $_houseNumber
         *
         * House number
         */
        protected $_houseNumber;

        /**
         * OPTIONAL
         * @var string $_houseNumberExtension
         *
         * House number extension
         */
        protected $_houseNumberExtension;

        /**
         * OPTIONAL
         * @var string $_addressNote
         *
         * Additional address information
         */
        protected $_addressNote;

        /**
         * REQUIRED
         * @var string $_zipCode
         *
         * Zip code
         */
        protected $_zipCode;

        /**
         * REQUIRED
         * @var string $_city
         *
         * City
         */
        protected $_city;

        /**
         * REQUIRED
         * @var string $_state
         *
         * State
         */
        protected $_state;

        /**
         * REQUIRED
         * @var string $_country
         *
         * Country code in ISO 3166-1 alpha-2 format
         * http://www.iso.org/iso/home/standards/country_codes/country_names_and_code_elements_xml-temp.htm
         */
        protected $_country;

        /**
         * OPTIONAL
         * @var string $_phone
         *
         * Phone number
         */
        protected $_phone;

        /**
         * OPTIONAL
         * @var string $_taxNumber
         *
         * Tax number
         */
        protected $_taxNumber;

        /**
         * REQUIRED
         * @var string $_email
         *
         * Customer email
         */
        protected $_email;

        /**
         * @param string $type
         *
         * @return OrderAddress
         */
        public function setType($type)
        {
            $this->_type = $type;

            return $this;
        }

        public function getName()
        {
            return $this->_type.'Address';
        }

        /**
         * @return string
         */
        public function getSalutation()
        {
            return $this->_salutation;
        }

        /**
         * @param $salutation
         *
         * @return $this
         * @throws InvalidSalutationException
         * @throws \Exception
         */
        public function setSalutation($salutation)
        {
            if (!Reflector::isValid(OrderAddress::class, $salutation, 'SALUTATION_%'))
            {
                throw new InvalidSalutationException();
            }
            $this->_salutation = $salutation;

            return $this;
        }

        /**
         * @return string
         */
        public function getFirstName()
        {
            return $this->_firstName;
        }

        /**
         * @param string $firstName
         *
         * @return OrderAddress
         */
        public function setFirstName($firstName)
        {
            $this->_firstName = $firstName;

            return $this;
        }

        /**
         * @return string
         */
        public function getLastName()
        {
            return $this->_lastName;
        }

        /**
         * @param string $lastName
         *
         * @return OrderAddress
         */
        public function setLastName($lastName)
        {
            $this->_lastName = $lastName;

            return $this;
        }

        /**
         * @return string
         */
        public function getCompany()
        {
            return $this->_company;
        }

        /**
         * @param string $company
         *
         * @return OrderAddress
         */
        public function setCompany($company)
        {
            $this->_company = $company;

            return $this;
        }

        /**
         * @return string
         */
        public function getStreet()
        {
            return $this->_street;
        }

        /**
         * @param string $street
         *
         * @return OrderAddress
         */
        public function setStreet($street)
        {
            $this->_street = $street;

            return $this;
        }

        /**
         * @return int
         */
        public function getHouseNumber()
        {
            return $this->_houseNumber;
        }

        /**
         * @param int $houseNumber
         *
         * @return OrderAddress
         */
        public function setHouseNumber($houseNumber)
        {
            $this->_houseNumber = $houseNumber;

            return $this;
        }

        /**
         * @return string
         */
        public function getHouseNumberExtension()
        {
            return $this->_houseNumberExtension;
        }

        /**
         * @param string $houseNumberExtension
         *
         * @return OrderAddress
         */
        public function setHouseNumberExtension($houseNumberExtension)
        {
            $this->_houseNumberExtension = $houseNumberExtension;

            return $this;
        }

        /**
         * @return string
         */
        public function getAddressNote()
        {
            return $this->_addressNote;
        }

        /**
         * @param string $addressNote
         *
         * @return OrderAddress
         */
        public function setAddressNote($addressNote)
        {
            $this->_addressNote = $addressNote;

            return $this;
        }

        /**
         * @return string
         */
        public function getZipCode()
        {
            return $this->_zipCode;
        }

        /**
         * @param string $zipCode
         *
         * @return OrderAddress
         */
        public function setZipCode($zipCode)
        {
            $this->_zipCode = $zipCode;

            return $this;
        }

        /**
         * @return string
         */
        public function getCity()
        {
            return $this->_city;
        }

        /**
         * @param string $city
         *
         * @return OrderAddress
         */
        public function setCity($city)
        {
            $this->_city = $city;

            return $this;
        }

        /**
         * @return string
         */
        public function getState()
        {
            return $this->_state;
        }

        /**
         * @param string $state
         *
         * @return OrderAddress
         */
        public function setState($state)
        {
            $this->_state = $state;

            return $this;
        }

        /**
         * @return string
         */
        public function getCountry()
        {
            return $this->_country;
        }

        /**
         * @param string $country
         *
         * @return OrderAddress
         */
        public function setCountry($country)
        {
            $this->_country = $country;

            return $this;
        }

        /**
         * @return string
         */
        public function getPhone()
        {
            return $this->_phone;
        }

        /**
         * @param string $phone
         *
         * @return OrderAddress
         */
        public function setPhone($phone)
        {
            $this->_phone = $phone;

            return $this;
        }

        /**
         * @return string
         */
        public function getTaxNumber()
        {
            return $this->_taxNumber;
        }

        /**
         * @param string $taxNumber
         *
         * @return OrderAddress
         */
        public function setTaxNumber($taxNumber)
        {
            $this->_taxNumber = $taxNumber;

            return $this;
        }

        /**
         * @param string $email
         *
         * @return OrderAddress
         */
        public function setEmail($email)
        {
            $this->_email = $email;

            return $this;
        }

        /**
         * @return string
         */
        public function getEmail()
        {
            return $this->_email;
        }
    }
