<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../vendor/autoload.php';
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFields\NumericCustomFieldModel;
use AmoCRM\Models\LeadModel;
use AmoCRM\OAuth\OAuthConfig;

use src\controllers\AmoCRMApiClientWrapper;
use src\controllers\CustomFieldsController;
use src\Database;
require_once __DIR__ . '/db.php';


$config = include(__DIR__ . '/../config/amocrm.php');
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_INT);
$spentTimeOver30Seconds = filter_input(INPUT_POST, 'spentTimeOver30Seconds', FILTER_SANITIZE_NUMBER_INT);

// Массивы с данными на отправку
$contactData = [
    "name"  => $name,
    "email" => $email,
    "phone" => $phone,
    "timeCheck" => $spentTimeOver30Seconds,
];
$leadData = [
    "name" => "Заявка от {$name}",
    "price" => $price,
];

$db = Database::getInstance();

// Создание объектов конфигурации и сервиса
$oAuthConfig = new OAuthConfig($config);
$apiClient = new \AmoCRM\Client\AmoCRMApiClient(
    $oAuthConfig->getIntegrationId(),
    $oAuthConfig->getSecretKey(),
    $oAuthConfig->getRedirectDomain()
);

// Обертка класса из API
$apiClientWrapper = new AmoCRMApiClientWrapper($apiClient, $oAuthConfig);
$customFieldsController = new CustomFieldsController($apiClientWrapper);


/*** Заполнение общего класса кастомных полей для сделки ***/
$leadCustomFieldsValues = new CustomFieldsValuesCollection();
$leadPrice = $customFieldsController->getFieldIdByCode("leads", "price");
if (!$leadPrice) {
    $model = new NumericCustomFieldModel();
    $leadPrice = $customFieldsController->createCustomField("leads", $model, "Цена", "PRICE");
}
$leadCustomFieldsValues->add($customFieldsController->getCustomNumericField($leadPrice->id, $leadData['price']));
$lead = new LeadModel();
$lead->setName($leadData['name']);
$lead->setCustomFieldsValues($leadCustomFieldsValues);

/*** Заполнение общего класса кастомных полей для контактов ***/
$contact = new ContactModel();
$contact->setName($contactData['name']);
$contactCustomFieldsValues = new CustomFieldsValuesCollection();

// Создание и заполнение timeCheck
$contactTimeCheck = $customFieldsController->getFieldIdByCode("contacts", "timecheck");
if (!$contactTimeCheck) {
    $model = new NumericCustomFieldModel();
    $contactTimeCheck = $customFieldsController->createCustomField("contacts", $model, "Больше 30 секунд на сайте", "TIMECHECK");
}
$contactCustomFieldsValues->add($customFieldsController->getCustomNumericField($contactTimeCheck->id, $contactData['timeCheck']));
// Создание и заполнение поля email
$contactEmailId = $customFieldsController->getFieldIdByCode("contacts", "email")->id;
$contactCustomFieldsValues->add($customFieldsController->getCustomTextField($contactEmailId, $contactData['email']));
// Создание и заполнение поля phone
$contactPhoneId = $customFieldsController->getFieldIdByCode("contacts","phone")->id;
$contactCustomFieldsValues->add($customFieldsController->getCustomTextField($contactPhoneId, $contactData['phone']));
$contact->setCustomFieldsValues($contactCustomFieldsValues);
// Создание контакта
$contactId = $apiClientWrapper->contacts()->addOne($contact)->getId();
// Связка контакта и сделки
$contactsCollection = new ContactsCollection();
$contact = new ContactModel();
$contact->setId($contactId);
$contactsCollection->add($contact);
$lead->setContacts($contactsCollection);  // Привязываем контакт к лиду

// Создаем Сделку
$apiClientWrapper->leads()->addOne($lead);

?>
