<span class="dms-display-money">
    <?php $currency = new \Dms\Common\Structure\Money\Currency($value['currency']) ?>
    {{ number_format($value['amount'], $currency->getDefaultFractionDigits()) . ' ' . $currency->getCurrencyCode() }}
</span>