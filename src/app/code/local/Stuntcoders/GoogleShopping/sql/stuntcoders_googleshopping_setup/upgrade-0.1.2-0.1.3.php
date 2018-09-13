<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$entityTypeId = $installer->getEntityTypeId('catalog_category');
$attributeSetId = $installer->getDefaultAttributeSetId($entityTypeId);

$installer->addAttribute('catalog_category', 'google_category', array(
    'type' => 'varchar',
    'label' => 'Google Merchant Category',
    'input' => 'text',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible' => true,
    'required' => false,
    'user_defined' => false,
    'default' => '',
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'unique' => false
));

$installer->addAttributeToGroup(
    $entityTypeId,
    $attributeSetId,
    'General Information',
    'google_category',
    4
);

$installer->endSetup();
