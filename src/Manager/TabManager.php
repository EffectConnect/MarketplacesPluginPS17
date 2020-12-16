<?php

namespace EffectConnect\Marketplaces\Manager;

use Context;
use Tab;
use Language;
use PrestaShopDatabaseException;
use PrestaShopException;

/**
 * Class TabManager
 * @package EffectConnect\Marketplaces\Manager
 */
class TabManager
{
    const PARENT_CLASS = 'Effectconnect_Marketplaces';

    /**
     * @param string $className
     * @param string $tabName
     * @param string $moduleName
     * @param string $parentClassName
     * @param string $icon
     * @return bool
     */
    private static function addTab(string $className, string $tabName, string $moduleName, string $parentClassName, string $icon = ''): bool
    {
        $translator      = Context::getContext()->getTranslator();
        $tab             = new Tab();
        $tab->active     = 1;
        $tab->class_name = $className;
        $tab->id_parent  = intval(Tab::getIdFromClassName($parentClassName));
        $tab->module     = $moduleName;
        $tab->icon       = $icon;
        $tab->name       = [];
        foreach (Language::getLanguages(true) as $lang) {
            // TODO: for the actual translations to work the translations have to be present first!
            $tab->name[$lang['id_lang']] = $translator->trans($tabName, [], 'Modules.Effectconnectmarketplaces.Admin', $lang['locale']);
        }
        return $tab->add();
    }

    /**
     * @param string $tabName
     * @param string $translation
     * @return bool
     */
    public static function addParentTab(string $tabName, string $translation): bool
    {
        return self::addTab(self::PARENT_CLASS, $tabName, 'effectconnect_marketplaces', 'CONFIGURE', 'settings');
    }

    /**
     * @return bool
     */
    public static function removeParentTab(): bool
    {
        return self::removeTab(self::PARENT_CLASS);
    }

    /**
     * @param string $className
     * @param string $tabName
     * @param string $translation
     * @return bool
     */
    public static function addChildTab(string $className, string $tabName, string $translation): bool
    {
        return self::addTab($className, $tabName, 'effectconnect_marketplaces', self::PARENT_CLASS);
    }

    /**
     * @param string $className
     * @return bool
     */
    public static function removeTab(string $className): bool
    {
        try {
            $tabId = intval(Tab::getIdFromClassName($className));
            $tab = new Tab($tabId);
            if ($tab->name !== '') {
                return $tab->delete();
            }
        } catch (PrestaShopDatabaseException $e) {
            return false;
        } catch (PrestaShopException $e) {
            return false;
        }
        return true;
    }
}
