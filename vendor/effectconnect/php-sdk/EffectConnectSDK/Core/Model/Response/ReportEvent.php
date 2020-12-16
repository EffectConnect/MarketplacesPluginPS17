<?php
    namespace EffectConnect\PHPSdk\Core\Model\Response;

    use EffectConnect\PHPSdk\Core\Helper\Payload;

    /**
     * Class ReportEvent
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class ReportEvent
    {
        const TYPE_VALIDATION   = 'Validation';
        const TYPE_NODE         = 'Node';

        const RESULT_ERROR   = 'Error';
        const RESULT_SUCCESS = 'Success';
        const RESULT_WARNING = 'Warning';

        /**
         * @var int $_iteration
         */
        private $_iteration;
        /**
         * @var string $_type
         */
        private $_type;
        /**
         * @var string $_result
         */
        private $_result;
        /**
         * @var string $_message
         */
        private $_message;
        /**
         * @var Note $_notes
         */
        private $_notes = [];

        /**
         * ReportEvent constructor.
         *
         * @param $payload
         *
         */
        public function __construct($payload)
        {
            if ($payload === null)
            {
                return;
            }
            $this->_iteration = (int)Payload::extract($payload, 'iteration');
            $this->_type      = Payload::extract($payload, 'type');
            $this->_result    = Payload::extract($payload, 'result');
            $this->_message   = Payload::extract($payload, 'message');
            foreach (Payload::extract($payload, 'notes', true) as $note)
            {
                $this->_notes[] = new Note((string)$note);
            }
        }

        /**
         * @return int
         */
        public function getIteration()
        {
            return $this->_iteration;
        }

        /**
         * @return string
         */
        public function getType()
        {
            return $this->_type;
        }

        /**
         * @return string
         */
        public function getResult()
        {
            return $this->_result;
        }

        /**
         * @return string
         */
        public function getMessage()
        {
            return $this->_message;
        }

        /**
         * @return Note
         */
        public function getNotes()
        {
            return $this->_notes;
        }
    }