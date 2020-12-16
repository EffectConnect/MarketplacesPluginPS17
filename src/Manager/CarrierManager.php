<?php

namespace EffectConnect\Marketplaces\Manager;

use Configuration;
use Carrier;
use EffectConnect\Marketplaces\Enums\ConfigurationKeys;
use Group;
use PrestaShopDatabaseException;
use PrestaShopException;
use Zone;

/**
 * Class CarrierManager
 * @package EffectConnect\Marketplaces\Manager
 */
class CarrierManager
{
    const EC_CUSTOMER_GROUP_NAME = 'Effectconnect Marketplaces Group';
    const EC_CARRIER_NAME        = 'Effectconnect Marketplaces Carrier';

    /**
     * TODO:
     *  - how to get rid of the default carriers options admin range settings, since they are not used anyway;
     *  - carrier default VAT settings using $carrier->setTaxRulesGroup();? (when not defining these the tax on shipping will be 0%);
     *  - do we want to make sure carrier costs are not recalculated after adding tracking info to order (in admin or some process);
     *
     * TODO:
     *  - check if carrier already exist (and is active) before creating it;
     *  - what if PS group feature is not active?
     *  - save the group ID somewhere to be able to use it as default group to assign customers to when importing orders.
     *
     * @return bool
     */
    public static function addCarrier()
    {
        //
        // Add customer group.
        // When creating a new carrier, we'll have to make sure it is only available for EC orders (and not appears on frontend).
        // We do this by creating a new customer group for EC and assign the carrier to it.
        //

        // Check it group already exists.
        $ecGroupArray = Group::searchByName(self::EC_CUSTOMER_GROUP_NAME);
        if ($ecGroupArray)
        {
            // Use existing group.
            $ecGroupId = intval($ecGroupArray['id_group']);
        }
        else
        {
            // Create new group.
            $ecGroup                       = new Group();
            $ecGroup->name                 = [Configuration::get('PS_LANG_DEFAULT') => self::EC_CUSTOMER_GROUP_NAME];
            $ecGroup->price_display_method = 0;

            try {
                $success = $ecGroup->add();
            } catch (PrestaShopDatabaseException $e) {
                $success = false;
            } catch (PrestaShopException $e) {
                $success = false;
            }

            if (!$success) {
                return false;
            }

            $ecGroupId = intval($ecGroup->id);
        }

        Configuration::updateGlobalValue(ConfigurationKeys::EC_DEFAULT_CUSTOMER_GROUP_ID, $ecGroupId);

        //
        // Add carrier.
        //

        $carrier = new Carrier();
        $carrier->name                 = self::EC_CARRIER_NAME;
        $carrier->is_free              = true; // Needs weight/prices ranges in case it's not free.
        $carrier->need_range           = true;
        $carrier->active               = true;
        $carrier->delay                = [Configuration::get('PS_LANG_DEFAULT') => self::EC_CARRIER_NAME];
        $carrier->position             = Carrier::getHigherPosition() + 1;

        try {
            $success = $carrier->add();
            if ($success)
            {
                // Assign EC customer group to carrier.
                $carrier->setGroups([$ecGroupId]);

                // Assign all zones to carrier.
                $zones = Zone::getZones();
                foreach ($zones as $zone) {
                    $carrier->addZone($zone['id_zone']);
                }

                Configuration::updateGlobalValue(ConfigurationKeys::EC_DEFAULT_CARRIER_ID, $carrier->id);
            }
        } catch (PrestaShopDatabaseException $e) {
            $success = false;
        } catch (PrestaShopException $e) {
            $success = false;
        }

        return $success;
    }

    /**
     * @return bool
     */
    public static function removeCarrier()
    {
        return true; // TODO: implement this if desirable
    }
}