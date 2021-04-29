<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class EffectConnect_Marketplacespayment
 */
class EffectConnect_Marketplacespayment extends PaymentModule
{
    /**
     * @var string[]
     */
    protected $_hooks = array(
        'paymentOptions',
        'paymentReturn',
    );

    /**
     * EffectConnect_Marketplacespayment constructor.
     */
    public function __construct()
	{
		$this->name                   = 'effectconnect_marketplacespayment';
		$this->tab                    = 'payments_gateways';
		$this->version                = '2.0.1';
		$this->author                 = 'EffectConnect';
		$this->controllers            = [];
		$this->is_eu_compatible       = 1;
        $this->ps_versions_compliancy = [
            'min'   => '1.7.6.5',
            'max'   => '1.7.7'
        ];

        $this->currencies_mode        = 'checkbox';
		$this->currencies             = true;
		$this->bootstrap              = true;

		parent::__construct();

        $this->displayName            = $this->trans('EffectConnect Marketplaces Payment', [], 'Modules.Effectconnectmarketplaces.Admin');
        $this->description            = $this->trans('Used as a payment module for importing EffectConnect orders.', [], 'Modules.Effectconnectmarketplaces.Admin');
        $this->confirmUninstall       = $this->trans('Are you sure you want to uninstall the EffectConnect Marketplaces Payment module?', [], 'Modules.Effectconnectmarketplaces.Admin');

        if (!count(Currency::checkPaymentCurrencies($this->id))) {
            $this->warning = $this->trans('No currency has been set for this module.');
        }
	}

    /**
     * @return bool|string
     */
    public function install()
    {
        if (parent::install()) {

            // Register hooks.
            foreach ($this->_hooks as $hook) {
                if (!$this->registerHook($hook)) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        if (parent::uninstall()) {

            // Unregister hooks.
            foreach ($this->_hooks as $hook) {
                if (!$this->unregisterHook($hook)) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * @param $params
     */
    public function hookPaymentOptions($params)
    {
    }

    /**
     * @param $params
     */
    public function hookPaymentReturn($params)
	{
	}
}
