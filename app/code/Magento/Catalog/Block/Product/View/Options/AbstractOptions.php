<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Product options abstract type block
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Product\View\Options;

abstract class AbstractOptions extends \Magento\View\Element\Template
{
    /**
     * Product object
     *
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * Product option object
     *
     * @var \Magento\Catalog\Model\Product\Option
     */
    protected $_option;

    /**
     * Tax data
     *
     * @var \Magento\Tax\Helper\Data
     */
    protected $_taxData = null;

    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreHelper;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Core\Helper\Data $coreHelper
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Core\Helper\Data $coreHelper,
        array $data = array()
    ) {
        $this->_coreHelper = $coreHelper;
        $this->_taxData = $taxData;
        parent::__construct($context, $data);
    }

    /**
     * Set Product object
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Block\Product\View\Options\AbstractOptions
     */
    public function setProduct(\Magento\Catalog\Model\Product $product = null)
    {
        $this->_product = $product;
        return $this;
    }

    /**
     * Retrieve Product object
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_product;
    }

    /**
     * Set option
     *
     * @param \Magento\Catalog\Model\Product\Option $option
     * @return \Magento\Catalog\Block\Product\View\Options\AbstractOptions
     */
    public function setOption(\Magento\Catalog\Model\Product\Option $option)
    {
        $this->_option = $option;
        return $this;
    }

    /**
     * Get option
     *
     * @return \Magento\Catalog\Model\Product\Option
     */
    public function getOption()
    {
        return $this->_option;
    }

    /**
     * @return string
     */
    public function getFormatedPrice()
    {
        if ($option = $this->getOption()) {
            return $this->_formatPrice(
                array(
                    'is_percent' => $option->getPriceType() == 'percent',
                    'pricing_value' => $option->getPrice($option->getPriceType() == 'percent')
                )
            );
        }
        return '';
    }

    /**
     * Return formated price
     *
     * @param array $value
     * @param bool $flag
     * @return string
     */
    protected function _formatPrice($value, $flag = true)
    {
        if ($value['pricing_value'] == 0) {
            return '';
        }

        $sign = '+';
        if ($value['pricing_value'] < 0) {
            $sign = '-';
            $value['pricing_value'] = 0 - $value['pricing_value'];
        }

        $priceStr = $sign;

        $customOptionPrice = $this->getProduct()->getPriceInfo()->getPrice('custom_option_price');
        $optionAmount = $customOptionPrice->getCustomAmount($value['pricing_value']);
        $priceStr .= $this->getLayout()->getBlock('product.price.render.default')->renderAmount(
            $optionAmount,
            $customOptionPrice,
            $this->getProduct()
        );

        if ($flag) {
            $priceStr = '<span class="price-notice">' . $priceStr . '</span>';
        }

        return $priceStr;
    }

    /**
     * Get price with including/excluding tax
     *
     * @param float $price
     * @param bool $includingTax
     * @return float
     */
    public function getPrice($price, $includingTax = null)
    {
        if (!is_null($includingTax)) {
            $price = $this->_taxData->getPrice($this->getProduct(), $price, true);
        } else {
            $price = $this->_taxData->getPrice($this->getProduct(), $price);
        }
        return $price;
    }

    /**
     * Returns price converted to current currency rate
     *
     * @param float $price
     * @return float
     */
    public function getCurrencyPrice($price)
    {
        $store = $this->getProduct()->getStore();
        return $this->_coreHelper->currencyByStore($price, $store, false);
    }
}
