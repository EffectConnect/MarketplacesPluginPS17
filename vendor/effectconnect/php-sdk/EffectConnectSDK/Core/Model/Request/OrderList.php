<?php
    namespace EffectConnect\PHPSdk\Core\Model\Request;

    use EffectConnect\PHPSdk\Core\Abstracts\ApiModel;
    use EffectConnect\PHPSdk\Core\Exception\MissingFilterValueException;
    use EffectConnect\PHPSdk\Core\Interfaces\ApiModelInterface;
    use EffectConnect\PHPSdk\Core\Interfaces\OrderListFilterInterface;

    /**
     * Class OrderList
     *
     * @author  Mark Thiesen
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     *
     */
    final class OrderList extends ApiModel implements ApiModelInterface
    {
        /**
         * OPTIONAL
         * @var OrderListFilterInterface[] $_filters
         *
         * List of filters
         */
        protected $_filters = [];

        /**
         * @return string
         */
        public function getName()
        {
            return 'list';
        }

        /**
         * @return OrderListFilterInterface[]
         */
        public function getFilters()
        {
            return $this->_filters;
        }

        /**
         * @param OrderListFilterInterface $filter
         *
         * @return OrderList
         *
         * @throws MissingFilterValueException
         */
        public function addFilter(OrderListFilterInterface $filter)
        {
            try
            {
                if ($filterValue = $filter->getFilterValue())
                {
                    $this->_filters[] = $filter;
                } else
                {
                    throw new MissingFilterValueException(get_class($filter));
                }
            } catch (MissingFilterValueException $filterValueException)
            {
                throw $filterValueException;
            } catch (\Exception $exception)
            {
                throw new MissingFilterValueException(get_class($filter));
            }

            return $this;
        }
    }