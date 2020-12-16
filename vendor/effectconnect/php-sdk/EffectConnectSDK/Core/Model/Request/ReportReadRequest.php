<?php
    namespace EffectConnect\PHPSdk\Core\Model\Request;

    use EffectConnect\PHPSdk\Core\Abstracts\ApiModel;
    use EffectConnect\PHPSdk\Core\Interfaces\ApiModelInterface;

    /**
     * Class ReportReadRequest
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class ReportReadRequest extends ApiModel implements ApiModelInterface
    {
        /**
         * @var string $_ID
         */
        protected $_ID;

        public function getName()
        {
            return 'report';
        }

        /**
         * @return string
         */
        public function getID()
        {
            return $this->_ID;
        }

        /**
         * @param $id
         *
         * @return ReportReadRequest
         */
        public function setID($id)
        {
            $this->_ID = $id;

            return $this;
        }
    }