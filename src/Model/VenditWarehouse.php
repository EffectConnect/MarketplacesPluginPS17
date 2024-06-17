<?php

namespace EffectConnect\Marketplaces\Model;

/**
 * Model for the table ps_vms_warehouse.
 */
class VenditWarehouse
{
    /**
     * id_vms_warehouse
     * @var int
     */
    private $_id;

    /**
     * title_vms_warehouse
     * @var string
     */
    private $_name;

    /**
     * vms_ismainoffice
     * @var bool
     */
    private $_isMainOffice;

    /**
     * vms_iswarehouse
     * @var bool
     */
    private $_isWarehouse;

    /**
     * @param int $id
     * @param string $name
     * @param bool $isMainOffice
     * @param bool $isWarehouse
     */
    public function __construct(int $id, string $name, bool $isMainOffice, bool $isWarehouse)
    {
        $this->_id           = $id;
        $this->_name         = $name;
        $this->_isMainOffice = $isMainOffice;
        $this->_isWarehouse  = $isWarehouse;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->_id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->_id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->_name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->_name = $name;
    }

    /**
     * @return bool
     */
    public function isMainOffice(): bool
    {
        return $this->_isMainOffice;
    }

    /**
     * @param bool $isMainOffice
     */
    public function setIsMainOffice(bool $isMainOffice)
    {
        $this->_isMainOffice = $isMainOffice;
    }

    /**
     * @return bool
     */
    public function isWarehouse(): bool
    {
        return $this->_isWarehouse;
    }

    /**
     * @param bool $isWarehouse
     */
    public function setIsWarehouse(bool $isWarehouse)
    {
        $this->_isWarehouse = $isWarehouse;
    }
}