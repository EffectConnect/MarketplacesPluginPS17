<?php
    namespace EffectConnect\PHPSdk\Core\Model\Response;

    use EffectConnect\PHPSdk\Core\Helper\Payload;
    use EffectConnect\PHPSdk\Core\Interfaces\ResponseContainerInterface;

    /**
     * Class ReportReadResponseContainer
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class ReportReadResponseContainer implements ResponseContainerInterface
    {
        const RESULT_COMPLETED   = 'completed';
        const RESULT_FAILED      = 'failed';
        /**
         * @var ReportInfo $_reportInfo
         */
        private $_reportInfo;
        /**
         * @var string $_reportResult
         */
        private $_reportResult;
        /**
         * @var ReportEvent[] $_reportEvents
         */
        private $_reportEvents = [];

        /**
         * ReportReadResponseContainer constructor.
         *
         * @param $payload
         */
        public function __construct($payload)
        {
            if ($payload === null)
            {
                return;
            }
            $this->_reportInfo = new ReportInfo(Payload::extract($payload, 'reportInfo'));
            $this->_reportResult = Payload::extract($payload, 'reportResult');
            foreach (Payload::extract($payload, 'reportEvents', true) as $event)
            {
                $this->_reportEvents[] = new ReportEvent($event);
            }
        }

        /**
         * @return ReportInfo
         */
        public function getReportInfo()
        {
            return $this->_reportInfo;
        }

        /**
         * @return string
         */
        public function getReportResult()
        {
            return $this->_reportResult;
        }

        /**
         * @return ReportEvent[]
         */
        public function getReportEvents()
        {
            return $this->_reportEvents;
        }
    }