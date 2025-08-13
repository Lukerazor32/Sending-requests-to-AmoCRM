<?php
namespace src\controllers;

use AmoCRM\Models\CustomFieldsValues\TextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\TextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\TextCustomFieldValueModel;

/*** Контроллер для взаимодействия с кастомными полями ***/
class CustomFieldsController
{
    private $apiClientWrapper;

    public function __construct($apiClientWrapper)
    {
        $this->apiClientWrapper = $apiClientWrapper;
    }

    public function getCustomTextField($id, $value) {
        $CustomFieldValuesModel = new TextCustomFieldValuesModel();
        return $this->getCustomField($CustomFieldValuesModel, $id, $value);
    }

    public function getCustomNumericField($id, $value) {
        $CustomFieldValuesModel = new TextCustomFieldValuesModel();
        return $this->getCustomField($CustomFieldValuesModel, $id, $value);
    }

    private function getCustomField($CustomFieldValuesModel, $id, $value) {
        $CustomFieldValuesModel->setFieldId($id);
        $CustomFieldValuesModel->setValues(
            (new TextCustomFieldValueCollection())
                ->add((new TextCustomFieldValueModel())->setValue($value))
        );
        return $CustomFieldValuesModel;
    }

    public function getFieldIdByCode($tag, $code)
    {
        $customFieldsCollection = $this->apiClientWrapper->customFields($tag)->get();

        // Преобразуем объект в массив для удобства
        foreach ($customFieldsCollection as $customField) {
            if (strtolower($customField->code) === strtolower($code)) {
                return $customField;
            }
        }
    }

    public function createCustomField($tag, $customField, $name, $code)
    {
        try {
            $customField
                ->setName($name)
                ->setCode($code)
                ->setIsRequired(false)
                ->setSort(500);
            return $this->apiClientWrapper->customFields($tag)->addOne($customField);
        } catch (\Exception $e) {
            return false;
        }
    }
}
