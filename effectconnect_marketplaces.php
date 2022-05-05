<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

use EffectConnect\Marketplaces\Enums\ConfigurationKeys;
use EffectConnect\Marketplaces\Manager\CarrierManager;
use EffectConnect\Marketplaces\Manager\TabManager;
use EffectConnect\Marketplaces\Manager\TranslationManager;
use EffectConnect\Marketplaces\Model\Connection;
use EffectConnect\Marketplaces\Model\OfferExportQueue;
use EffectConnect\Marketplaces\Model\TrackingExportQueue;

/**
 * Class EffectConnect_Marketplaces
 */
class EffectConnect_Marketplaces extends Module
{
    /**
     * @var string[]
     */
    protected $_hooks = array(
        'ecOfferExportQueueSchedule' ,         // Product price or stock update (stock and price update to EffectConnect)
        'actionValidateOrder',                 // New order was placed (stock update to EffectConnect)
        'actionOrderStatusUpdate',             // Order state update (order state update to EffectConnect)
        'actionObjectOrderCarrierUpdateAfter', // Order carrier update (tracking number update to EffectConnect)
        'actionCarrierUpdate',                 // Carrier ID update
        'displayAdminOrderSideBottom'          // Display EffectConnect order info on admin order detail page (1.7.7)
    );

    /**
     * EffectConnect_Marketplaces constructor.
     */
    public function __construct()
    {
        $this->name                     = 'effectconnect_marketplaces';
        $this->tab                      = 'market_place';
        $this->version                  = '3.1.24';
        $this->author                   = 'EffectConnect';
        $this->need_instance            = 1;
        $this->bootstrap                = true;
        $this->ps_versions_compliancy   = [
            'min'   => '1.7.6.5',
            'max'   => '1.7.8'
        ];

        parent::__construct();

        $this->displayName              = $this->trans('EffectConnect Marketplaces', [], 'Modules.Effectconnectmarketplaces.Admin');
        $this->description              = $this->trans('EffectConnect Marketplaces Prestashop plugin', [], 'Modules.Effectconnectmarketplaces.Admin');
        $this->confirmUninstall         = $this->trans('Are you sure you want to uninstall the EffectConnect Marketplaces module?', [], 'Modules.Effectconnectmarketplaces.Admin');
    }

    /**
     * @return bool
     */
    public function install()
    {
        if (parent::install()) {

            // Register hooks.
            foreach ($this->_hooks as $hook) {
                if (!$this->registerHook($hook)) {
                    $this->_errors[] = 'Error while registering hooks';
                    return false;
                }
            }

            // Create database tables.
            if (
                !Connection::createDbTable()
                || !TrackingExportQueue::createDbTable()
                || !OfferExportQueue::createDbTable()
            ) {
                $this->_errors[] = 'Error while creating database tables';
                return false;
            }

            // Add tabs to admin - the translations parameters are only used for the PS wordings extraction system - the actual translations are done within the TabManager.
            if (
                !TabManager::addParentTab('EffectConnect', $this->trans('EffectConnect', [], 'Modules.Effectconnectmarketplaces.Admin'))
                || !TabManager::addChildTab('AdminConnectionControllerLegacyClass', 'Connections', $this->trans('Connections', [], 'Modules.Effectconnectmarketplaces.Admin'))
                || !TabManager::addChildTab('AdminLogControllerLegacyClass', 'Logs', $this->trans('Logs', [], 'Modules.Effectconnectmarketplaces.Admin'))
            ) {
                $this->_errors[] = 'Error while adding admin tabs';
                return false;
            }

            // Add carrier.
            if (!CarrierManager::addCarrier()) {
                $this->_errors[] = 'Error while adding carrier';
                return false;
            }

            // TODO: add payment method?

            // Add translations.
            if (!$this->addTranslations()) {
                $this->_errors[] = 'Error while adding translations';
                return false;
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
                    $this->_errors[] = 'Error while unregistering hooks';
                    return false;
                }
            }

            // Remove database tables.
            if (
                !Connection::removeDbTable()
                || !TrackingExportQueue::removeDbTable()
                || !OfferExportQueue::removeDbTable()
            ) {
                $this->_errors[] = 'Error while removing database tables';
                return false;
            }

            // Remove tabs from admin.
            if (
                !TabManager::removeTab('AdminLogControllerLegacyClass')
                || !TabManager::removeTab('AdminConnectionControllerLegacyClass')
                || !TabManager::removeParentTab()
            ) {
                $this->_errors[] = 'Error while removing admin tabs';
                return false;
            }

            // Remove carrier.
            if (!CarrierManager::removeCarrier()) {
                $this->_errors[] = 'Error while removing carrier';
                return false;
            }

            // Remove translations.
            if (!$this->removeTranslations()) {
                $this->_errors[] = 'Error while removing translations';
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * @param false $force_all
     * @return bool
     */
    public function enable($force_all = false)
    {
        if (parent::enable($force_all)) {

            // Register hooks.
            foreach ($this->_hooks as $hook) {
                if (!$this->registerHook($hook)) {
                    $this->_errors[] = 'Error while registering hooks';
                    return false;
                }
            }

            // Disable admin tabs.
            if (!Tab::enablingForModule($this->name)) {
                $this->_errors[] = 'Error while enabling admin tabs';
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * @param false $force_all
     * @return bool
     */
    public function disable($force_all = false)
    {
        if (parent::disable($force_all)) {

            // Unregister hooks.
            foreach ($this->_hooks as $hook) {
                if (!$this->unregisterHook($hook)) {
                    $this->_errors[] = 'Error while unregistering hooks';
                    return false;
                }
            }

            // Disable admin tabs.
            if (!Tab::disablingForModule($this->name)) {
                $this->_errors[] = 'Error while disabling admin tabs';
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * Run the upgrade for a given module name and version - add new translations to database with each upgrade.
     * This method is executed on upgrade only if a corresponding update file exists in upgrade\upgrade-x.x.x.php.
     *
     * @return array
     */
    public function runUpgradeModule()
    {
        $this->addTranslations();
        return parent::runUpgradeModule();
    }

    /**
     * For extracting wordings to translatable strings in admin.
     * Available since 1.7.6.
     *
     * @return bool
     */
    public function isUsingNewTranslationSystem()
    {
        return true;
    }

    /**
     * Install script: import CSV translations and save them into database.
     * Will work from 1.7.6.
     *
     * @return bool
     */
    protected function addTranslations()
    {
        try {
            $translationService  = $this->get('prestashop.service.translation');
            $cacheRefreshService = $this->get('prestashop.cache.refresh');
            $translationManager  = new TranslationManager($translationService, $cacheRefreshService);
            return $translationManager->addTranslations();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Uninstall script: remove translations from database.
     *
     * @return bool
     */
    protected function removeTranslations()
    {
        try {
            $translationService  = $this->get('prestashop.service.translation');
            $cacheRefreshService = $this->get('prestashop.cache.refresh');
            $translationManager  = new TranslationManager($translationService, $cacheRefreshService);
            return $translationManager->removeTranslations();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * We created our own hook for product updates, because the existing PS hooks don't contain info about the changes.
     * In case product prices (main price or one of the combination prices) have changed, we add the product ID to the
     * stock export queue, so the cronjob can sync the new price to EffectConnect with an API call.
     *
     * Hook is called in:
     * Product->updateAttribute    - when price of a product combination has changed
     * Product->update             - when price of a product has changed
     * SpecificPrice->add          - when a discount rule was added for a specific product
     * SpecificPrice->update       - when a discount rule was updated for a specific product
     * SpecificPrice->delete       - when a discount rule was deleted for a specific product
     * StockAvailable::setQuantity - when the stock amount of a product or combination has changed
     *
     * TODO: do we need to know in what shop context data has changed?
     *
     * @param array $params
     */
    public function hookEcOfferExportQueueSchedule(array $params)
    {
        if (isset($params['id_product']) && intval($params['id_product']) > 0) {
            OfferExportQueue::schedule(intval($params['id_product']));
        }
    }

    /**
     * For (frontend) orders we can use a existing PS hook to handle stock changes.
     *
     * TODO: do we need to know in what shop context data has changed?
     *
     * @param array $params
     */
    public function hookActionValidateOrder(array $params)
    {
        if (isset($params['order']) && $params['order'] instanceof Order) {
            foreach ($params['order']->getProducts() as $productArray) {
                OfferExportQueue::schedule(intval($productArray['id_product']));
            }
        }
    }

    /**
     * When updating a carrier in PS backoffice, the ID will change, so we have to update the ID in all our connections accordingly and in the default PS configuration.
     *
     * @param array $params hook parameters containing the new carrier.
     */
    public function hookActionCarrierUpdate(array $params)
    {
        if (isset($params['id_carrier']) && isset($params['carrier']) && $params['carrier'] instanceof Carrier) {
            Connection::updateCarrier(intval($params['id_carrier']), intval($params['carrier']->id));
            Configuration::updateGlobalValue(ConfigurationKeys::EC_DEFAULT_CARRIER_ID, intval($params['carrier']->id));
        }
    }

    /**
     * Order carrier update hook for sending tracking number to EC.
     *
     * @param array $params
     */
    public function hookActionObjectOrderCarrierUpdateAfter(array $params)
    {
        if (isset($params['object']) && $params['object'] instanceof OrderCarrier)
        {
            $orderCarrier   = $params['object'];
            $trackingNumber = strval($orderCarrier->tracking_number);
            $idOrder        = intval($orderCarrier->id_order);
            $carrierName    = strval((new Carrier(intval($orderCarrier->id_carrier)))->name);
            TrackingExportQueue::updateCarrierNameAndTrackingNumber($idOrder, $carrierName, $trackingNumber);
        }
    }

    /**
     * Order state update hook for updating order state to EC.
     *
     * @param array $params
     */
    public function hookActionOrderStatusUpdate(array $params)
    {
        if (isset($params['id_order']) && isset($params['newOrderStatus']) && $params['newOrderStatus'] instanceof OrderState)
        {
            if (boolval($params['newOrderStatus']->shipped) === true) {
                TrackingExportQueue::updateIsShipped(intval($params['id_order']), 1);
            }
        }
    }

    /**
     * Display details from EffectConnect on admin order detail page (if any).
     * Hook available since 1.7.7.
     *
     * @param array $params
     * @return mixed
     */
    public function hookDisplayAdminOrderSideBottom(array $params)
    {
        if (isset($params['id_order']))
        {
            $orderDetails = TrackingExportQueue::getListByIdOrder(intval($params['id_order']));
            if (count($orderDetails) === 1)
            {
                return $this->get('twig')->render('@Modules/effectconnect_marketplaces/views/templates/admin/order/effectconnect_order_details.html.twig', [
                    'EffectConnectOrder' => reset($orderDetails)
                ]);
            }
        }
        return '';
    }
}