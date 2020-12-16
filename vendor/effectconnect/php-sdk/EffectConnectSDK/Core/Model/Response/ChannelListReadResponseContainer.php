<?php
    namespace EffectConnect\PHPSdk\Core\Model\Response;

    use EffectConnect\PHPSdk\Core\Helper\Payload;
    use EffectConnect\PHPSdk\Core\Interfaces\ResponseContainerInterface;

    /**
     * Class ChannelListReadResponseContainer
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class ChannelListReadResponseContainer implements ResponseContainerInterface
    {
        /**
         * @var Channel[] $_orders
         */
        private $_channels = [];

        /**
         * OrderListReadResponseContainer constructor.
         *
         * @param $payload
         */
        public function __construct($payload)
        {
            if ($payload === null || $payload === '')
            {
                return;
            }
            foreach (Payload::extract($payload, 'Channels', true) as $channel)
            {
                $this->_channels[] = new Channel($channel);
            }
        }

        /**
         * @return Channel[]
         */
        public function getChannels()
        {
            return $this->_channels;
        }
    }
