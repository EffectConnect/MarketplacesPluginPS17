# EffectConnect Marketplaces - Prestashop 1.7 plugin

Use this plugin to connect your Prestashop 1.7 webshop with EffectConnect Marketplaces. For more information about EffectConnect, go to the [EffectConnect website](https://www.effectconnect.com "EffectConnect Website").

## Install module

1. Download the [ZIP file](https://github.com/EffectConnect/MarketplacesPluginPrestashop/releases/ "Plugin ZIP") of the plugin.

2. Upload the ZIP file in your Prestashop Environment (Modules -> Module Manager > Upload a module), select the downloaded file and the installation will start automatically.

## Configure module

1. Make sure you have a EffectConnect account.

2. Create API key in EffectConnect (API > Manager API keys > Create API key).
   - Type: 'custom'
   - Permissions: 'all' (you can deselect channels you don't want to import orders for in your webshop).
   - Save the key.
   
3. Add a new connection in your webshop (Configure > EffectConnect > Connections) and use the API keys you just created.

## Setup cron tasks

To activate all synchronisation processes between EffectConnect and your webshop you need to setup the following cron tasks:

- Export catalog (command ```ec:export-catalog```)
  

- Export all offers (command ```ec:export-offers```)


- Export queued offers (command ```ec:export-queued-offers```)

    
- Import orders (command ```ec:import-orders```)


- Track & trace export (command ```ec:export-tracking-numbers```)

    
- Clean log files and temporarily catalog XML files (command ```ec:clean-files```)

### Example crontab:
 
```
0 4 * * * <php_path>php <prestashop_path>bin/console ec:export-catalog
*/20 * * * * <php_path>php <prestashop_path>bin/console ec:export-offers
* * * * * <php_path>php <prestashop_path>bin/console ec:export-queued-offers
*/15 * * * * <php_path>php <prestashop_path>bin/console ec:import-orders
*/15 * * * * <php_path>php <prestashop_path>bin/console ec:export-tracking-numbers
0 5 * * * <php_path>php <prestashop_path>bin/console ec:clean-files
```
