<?php
    namespace EffectConnect\PHPSdk\Core\CallType;

    use EffectConnect\PHPSdk\Core\Abstracts\CallType;
    use EffectConnect\PHPSdk\ApiCall;
    use EffectConnect\PHPSdk\Core\Exception\InvalidActionForCallTypeException;
    use EffectConnect\PHPSdk\Core\Helper\Payload;
    use EffectConnect\PHPSdk\Core\Interfaces\CallTypeInterface;
    use EffectConnect\PHPSdk\Core\Interfaces\ResponseContainerInterface;
    use EffectConnect\PHPSdk\Core\Model\Request\ReportReadRequest;
    use EffectConnect\PHPSdk\Core\Model\Response\ReportReadResponseContainer;
    use EffectConnect\PHPSdk\Core\Validation\ReportValidator;

    /**
     * Class ReportCall
     *
     * CallType class for retrieving the process report.
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     *
     * @method ApiCall read(ReportReadRequest $reportReadRequest)
     */
    final class ReportCall extends CallType implements CallTypeInterface
    {
        protected $validatorClass = ReportValidator::class;

        /**
         * @param ApiCall $apiCall
         *
         * @return ApiCall
         * @throws InvalidActionForCallTypeException
         */
        public function _prepareCall($apiCall)
        {
            switch ($this->action)
            {
                case CallTypeInterface::ACTION_READ:
                    $method = 'GET';
                    break;
                default:
                    throw new InvalidActionForCallTypeException();
            }
            $apiCall
                ->setUri('/report')
                ->setMethod($method)
            ;

            return $apiCall;
        }

        /**
         * @param $method
         * @param $responsePayload
         *
         * @return ResponseContainerInterface
         */
        public static function processResponse($method, $responsePayload)
        {
            return new ReportReadResponseContainer(Payload::extract($responsePayload, 'ReportReadResponseContainer'));
        }
    }