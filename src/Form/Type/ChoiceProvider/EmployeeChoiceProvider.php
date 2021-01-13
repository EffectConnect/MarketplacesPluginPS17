<?php

namespace EffectConnect\Marketplaces\Form\Type\ChoiceProvider;

use Employee;
use Exception;
use PrestaShopDatabaseException;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Class EmployeeChoiceProvider
 * @package EffectConnect\Marketplaces\Form\Type\ChoiceProvider
 */
final class EmployeeChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @return array
     */
    public function getChoices()
    {
        $employeesChoices = ['' => ''];

        try {
            $employees = Employee::getEmployees();
        } catch (Exception $e) {
            $employees = [];
        }

        foreach ($employees as $employeeArray) {
            $employeesChoices[$this->getName($employeeArray)] = $employeeArray['id_employee'];
        }

        return $employeesChoices;
    }

    /**
     * @return array
     */
    public function getChoicesAttributes()
    {
        $employeesAttributes = [];

        try {
            $employees = Employee::getEmployees();
        } catch (Exception $e) {
            $employees = [];
        }

        foreach ($employees as $employeeArray)
        {
            $model = new Employee();
            $model->id = $employeeArray['id_employee'];
            try {
                $shops = $model->getAssociatedShops();
            } catch (PrestaShopDatabaseException $e) {
                $shops = [];
            }

            $employeesAttributes[$this->getName($employeeArray)] = ['data-shop-id' => json_encode($shops)];
        }

        return $employeesAttributes;
    }

    /**
     * @param array $employeeArray
     * @return string
     */
    protected function getName(array $employeeArray)
    {
        return sprintf('%s %s', $employeeArray['firstname'], $employeeArray['lastname']);
    }
}
