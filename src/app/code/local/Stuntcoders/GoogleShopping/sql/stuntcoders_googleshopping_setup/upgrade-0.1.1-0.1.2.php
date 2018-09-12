<?php

$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()
    ->addColumn($installer->getTable('stuntcoders_googleshopping/feed'), 'attributes', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => false,
        'default' => '',
        'comment' => 'Attributes',
    ));

$installer->endSetup();