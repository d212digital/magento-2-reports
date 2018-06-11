<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Reports
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Reports\Block\Dashboard;

use Magento\Backend\Block\Template;
use Magento\Directory\Model\Currency;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Mageplaza\Reports\Helper\Data;

/**
 * Class TotalSales
 * @package Mageplaza\Reports\Block\Dashboard
 */
class AverageOrderValue extends AbstractClass
{
    const NAME = 'averageOrderValue';

    /**
     * @var string
     */
    protected $_template = 'dashboard/chart.phtml';

    /**
     * @var PriceHelper
     */
    protected $_priceHelper;

    /**
     * @var Currency
     */
    protected $_currency;

    /**
     * TotalSales constructor.
     * @param Template\Context $context
     * @param PriceHelper $priceHelper
     * @param Currency $currency
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        PriceHelper $priceHelper,
        Currency $currency,
        Data $helperData,
        array $data = []
    )
    {
        parent::__construct($context, $helperData, $data);

        $this->_currency    = $currency;
        $this->_priceHelper = $priceHelper;
    }

    /**
     * @return float|int|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getTotal()
    {
        $date   = $this->_helperData->getDateRange();
        $totals = $this->_helperData->getSalesByDateRange($date[0], $date[1]);

        return $this->_priceHelper->currency(($totals->getAverage() ? $totals->getAverage() : 0));
    }

    /**
     * @return float|int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getRate()
    {
        $dates         = $this->_helperData->getDateRange();
        $totals        = $this->_helperData->getSalesByDateRange($dates[0], $dates[1]);
        $compareTotals = $this->_helperData->getSalesByDateRange($dates[2], $dates[3]);
        if ($totals->getAverage() == 0 && $compareTotals->getAverage() == 0) {
            return 0;
        } else if ($compareTotals->getAverage() == 0) {
            return 100;
        } else if ($totals->getAverage() == 0) {
            return -100;
        }

        return round(((($totals->getAverage() - $compareTotals->getAverage()) / $compareTotals->getAverage()) * 100), 2);
    }

    /**
     * @param $date
     * @param null $endDate
     * @return float|int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getDataByDate($date, $endDate = null)
    {
        $totals = $this->_helperData->getSalesByDateRange($date, $endDate);

        return round($totals->getAverage() ? $totals->getAverage() : 0, 2);
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getTitle()
    {
        return __('Average Order Value');
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getYLabel()
    {
        return __('orders');
    }
}