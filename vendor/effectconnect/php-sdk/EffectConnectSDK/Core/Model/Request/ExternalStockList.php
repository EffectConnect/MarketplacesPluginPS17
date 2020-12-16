<?php
    namespace EffectConnect\PHPSdk\Core\Model\Request;

    use EffectConnect\PHPSdk\Core\Abstracts\ApiModel;
    use EffectConnect\PHPSdk\Core\Interfaces\ApiModelInterface;

    /**
     * Class ExternalStockList
     *
     * @author  Mark Thiesen
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     *
     */
    final class ExternalStockList extends ApiModel implements ApiModelInterface
    {
        /**
         * MANDATORY
         * @var int $_channelId
         *
         * the channelId to get the external stock from
         */
        protected $_channelId;

        /**
         * @return string
         */
        public function getName()
        {
            return 'channel';
        }

        /**
         * @return int
         */
        public function getChannelId()
        {
            return $this->_channelId;
        }

        /**
         * @param int $channelId
         *
         * @return ExternalStockList
         */
        public function setChannelId(int $channelId)
        {
            $this->_channelId = $channelId;

            return $this;
        }
    }