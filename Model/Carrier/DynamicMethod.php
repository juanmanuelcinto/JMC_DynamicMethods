<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace JMC\DynamicShipping\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;

/**
 * Dynamic Methods model class
 * @author Juan Manuel Cinto <juanmanuelcinto@gmail.com>
 */
class DynamicMethod extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{

    /**
     * @var string
     */
    protected $_code = 'dynamicmethod';

    /**
     * @var boolean
     */
    protected $_isFixed = true;

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $_serializer;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_serializer = $serializer;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * @param RateRequest $request
     * @return
     */
    public function collectRates(RateRequest $request)
    {
        $data = $this->getConfigData('serialized');
        if (!$data) {
            return false;
        }

        $result = $this->_rateResultFactory->create();
        $customMethods = $this->_serializer->unserialize($data);
        $count = 1;
        foreach ($customMethods as $customMethod) {
            if (isset($customMethod['price'], $customMethod['name'])) {
                $method = $this->_rateMethodFactory->create();
                $method->setCarrier($this->_code);
                $method->setCarrierTitle($this->getConfigData('title'));
                $shippingPrice = $customMethod['price'];
                $method->setMethod($this->_code . '_' . $count);
                $method->setMethodTitle($customMethod['name']);
                if ($request->getFreeShipping() === true || $request->getPackageQty() == $this->getFreeBoxes()) {
                    $shippingPrice = '0.00';
                }
                $method->setPrice($shippingPrice);
                $method->setCost($shippingPrice);
                $result->append($method);
                $count++;
            }
        }

        return $result;
    }

    /**
     * getAllowedMethods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }
}
