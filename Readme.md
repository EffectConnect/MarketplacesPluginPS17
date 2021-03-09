# EffectConnect Marketplaces - Prestashop 1.7 plugin

Use this plugin to connect your Prestashop 1.7 webshop with EffectConnect Marketplaces. For more information about EffectConnect, go to the [EffectConnect website](https://www.effectconnect.com "EffectConnect Website").

## Install module

1. Download the latest release [effectconnect_marketplaces_x_x_x.zip](https://github.com/EffectConnect/MarketplacesPluginPrestashop/releases/ "Plugin ZIP") of the plugin.

2. Upload the ZIP file in your Prestashop Environment (Modules -> Module Manager > Upload a module), select the downloaded file and the installation will start automatically.

## Configure module

1. Make sure you have a *new* EffectConnect account.

2. Create API key in EffectConnect (API > Manager API keys > Create API key).
   - Type: 'custom'
   - Permissions: 'all' (you can deselect channels you don't want to import orders for in your webshop).
   - Save the key.
   
3. Add a new connection in your webshop (Configure > EffectConnect > Connections) and use the API keys you just created.

> For importing orders you need to select a payment module to assign the order to. 
> The EffectConnect Marketplaces plugin includes its own payment module ('EffectConnect Marketplaces Payment').
> It's recommended to use this payment module, because we can't expect other payment methods to be compatible with our plugin.

## Setup cron tasks

To activate all synchronisation processes between EffectConnect and your webshop you need to setup the following cron tasks:

```
0 4 * * * <php_path>php <prestashop_path>bin/console ec:export-catalog > <prestashop_path>modules/effectconnect_marketplaces/data/log/cron_export_catalog.log
*/20 * * * * <php_path>php <prestashop_path>bin/console ec:export-offers > <prestashop_path>modules/effectconnect_marketplaces/data/log/cron_export_offers.log
* * * * * <php_path>php <prestashop_path>bin/console ec:export-queued-offers > <prestashop_path>modules/effectconnect_marketplaces/data/log/cron_export_queued_offers.log
*/15 * * * * <php_path>php <prestashop_path>bin/console ec:import-orders > <prestashop_path>modules/effectconnect_marketplaces/data/log/cron_import_orders.log
*/15 * * * * <php_path>php <prestashop_path>bin/console ec:export-tracking-numbers > <prestashop_path>modules/effectconnect_marketplaces/data/log/export_tracking_numbers.log
0 5 * * * <php_path>php <prestashop_path>bin/console ec:clean-files > <prestashop_path>modules/effectconnect_marketplaces/data/log/cron_clean_files.log
```
### Example crontab:

```
0 4 * * * /usr/bin/php73 /home/kpdevelopment/domains/prestashop-1-7-7-1.nl/public_html/bin/console ec:export-catalog > /home/kpdevelopment/domains/prestashop-1-7-7-1.nl/public_html/modules/effectconnect_marketplaces/data/log/cron_export_catalog.log
*/20 * * * * /usr/bin/php73 /home/kpdevelopment/domains/prestashop-1-7-7-1.nl/public_html/bin/console ec:export-offers > /home/kpdevelopment/domains/prestashop-1-7-7-1.nl/public_html/modules/effectconnect_marketplaces/data/log/cron_export_offers.log
* * * * * /usr/bin/php73 /home/kpdevelopment/domains/prestashop-1-7-7-1.nl/public_html/bin/console ec:export-queued-offers > /home/kpdevelopment/domains/prestashop-1-7-7-1.nl/public_html/modules/effectconnect_marketplaces/data/log/cron_export_queued_offers.log
*/15 * * * * /usr/bin/php73 /home/kpdevelopment/domains/prestashop-1-7-7-1.nl/public_html/bin/console ec:import-orders > /home/kpdevelopment/domains/prestashop-1-7-7-1.nl/public_html/modules/effectconnect_marketplaces/data/log/cron_import_orders.log
*/15 * * * * /usr/bin/php73 /home/kpdevelopment/domains/prestashop-1-7-7-1.nl/public_html/bin/console ec:export-tracking-numbers > /home/kpdevelopment/domains/prestashop-1-7-7-1.nl/public_html/modules/effectconnect_marketplaces/data/log/export_tracking_numbers.log
0 5 * * * /usr/bin/php73 /home/kpdevelopment/domains/prestashop-1-7-7-1.nl/public_html/bin/console ec:clean-files > /home/kpdevelopment/domains/prestashop-1-7-7-1.nl/public_html/modules/effectconnect_marketplaces/data/log/cron_clean_files.log
```
### PHP versions

When setting up cron tasks make sure to use the correct PHP version that matches your Prestashop installation:

| Prestashop Version | PHP version |
| ------------- | ------------- |
| Prestashop 1.7.6.5 | PHP 7.1 - 7.2 |
| Prestashop 1.7.7.0 | PHP 7.1 - 7.3 |
| Prestashop 1.7.7.1 | PHP 7.1 - 7.3 |

You can check your default PHP version by running `php -v` in your SSH console.
If the default version does not match the table above, ask your server administrator how to run commands in another PHP version. 
For example on some servers you can just replace `php` by `php72` (or any desired version).    

## Explanation of commands

- Export catalog
  - Export products to EffectConnect 
  - Command: ```ec:export-catalog```
  - Recommended to run: once a day

- Export all offers
  - Export all stock and prices to EffectConnect 
  - Command: ```ec:export-offers```
  - Recommended to run: every 20 minutes

- Export queued offers
  - Export stock and prices for products that have been changed and therefore added to the export queue:
    - when price of a product combination has changed
    - when price of a product has changed
    - when a discount rule was added for a specific product
    - when a discount rule was updated for a specific product
    - when a discount rule was deleted for a specific product
    - when the stock amount of a product or combination has changed by admin
    - when the stock amount of a product or combination has changed by a order 
  - Command: ```ec:export-queued-offers```
  - Recommended to run: run every minute
    
- Import orders
  - Import orders from EffectConnect channels into webshop:
    - orders in EffectConnect will be updated with the webshop order reference and ID
    - orders in EffectConnect will be updated with the imported status (successful or error) as a order tag - orders with this tag will not be imported again (remove the tag in EffectConnect to try to import the order again)
  - Command: ```ec:import-orders```
  - Recommended to run: every 15 minutes

- Track & Trace export 
  - Export shipping information to EffectConnect when:
    - order state was updated to 'shipped' (order will get 'shipped' status in EffectConnect)
    - a tracking number was added to an order (carrier and T&T will be exported, order also will get 'shipped' status in EffectConnect - updates of T&T's won't be exported)
  - Command: ```ec:export-tracking-numbers```
  - Recommended to run: every 15 minutes
    
- Clean log files and temporarily catalog XML files 
  - Clean all xml and log files that are created 7 days ago in the <prestashop_path>modules/effectconnect_marketplaces/data folder:
    - xml files are exported files that have been generated by the ```ec:export-catalog```, ```ec:export-offers``` and ```ec:export-queued-offers``` commands
    - log files keep track of several processes within the Prestashop Plugin and can be used by the EffectConnect support team for troubleshooting in case functionality does not work as expected     
  - Command: ```ec:clean-files```
  - Recommended to run: once a day
