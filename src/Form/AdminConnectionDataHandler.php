<?php

namespace EffectConnect\Marketplaces\Form;

use EffectConnect\Marketplaces\Model\Connection;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;
use Validate;

/**
 * Class AdminConnectionDataHandler
 * @package EffectConnect\Marketplaces\Form
 */
class AdminConnectionDataHandler implements FormDataHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        $record = new Connection();
        $record = $this->assignData($record, $data);
        return $record->save();
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        $record = new Connection(intval($id));
        if (Validate::isLoadedObject($record)) {
            $record = $this->assignData($record, $data);
        }
        return $record->save();
    }

    /**
     * @param Connection $record
     * @param array $data
     * @return Connection
     */
    protected function assignData(Connection $record, array $data)
    {
        $record->is_active                        = intval($data['is_active']);
        $record->name                             = $data['name'];
        $record->id_shop                          = intval($data['id_shop']);
        $record->public_key                       = $data['public_key'];
        $record->secret_key                       = $data['secret_key'];
        $record->catalog_export_only_active       = intval($data['catalog_export_only_active']);
        $record->catalog_export_special_price     = intval($data['catalog_export_special_price']);
        $record->catalog_export_add_option_title  = intval($data['catalog_export_add_option_title']);
        $record->catalog_export_ean_leading_zero  = intval($data['catalog_export_ean_leading_zero']);
        $record->catalog_export_skip_invalid_ean  = intval($data['catalog_export_skip_invalid_ean']);
        $record->order_import_id_group            = intval($data['order_import_id_group']);
        $record->order_import_id_carrier          = intval($data['order_import_id_carrier']);
        $record->order_import_id_payment_module   = intval($data['order_import_id_payment_module']);
        $record->order_import_external_fulfilment = $data['order_import_external_fulfilment'];
        $record->order_import_send_emails         = intval($data['order_import_send_emails']);

        return $record;
    }
}
