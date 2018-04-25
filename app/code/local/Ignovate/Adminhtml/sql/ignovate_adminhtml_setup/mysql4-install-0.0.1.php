<?php
/**
 * Created by PhpStorm.
 * User: prabu
 * Date: 25/04/18
 * Time: 12:07 PM
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$tblName = $installer->getTable('ignovate_adminhtml/user_store');
//echo $tblName;exit;

/**
 * Create table 'cms/page_store'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('ignovate_adminhtml/user_store'))
    ->addColumn('user_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'primary'   => true,
    ), 'User ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Store ID')
    ->addIndex($installer->getIdxName('ignovate_adminhtml/user_store', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('ignovate_adminhtml/user_store', 'user_id', 'admin/user', 'user_id'),
        'user_id', $installer->getTable('admin/user'), 'user_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('ignovate_adminhtml/user_store', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Admin user to store linkage');

$installer->getConnection()->createTable($table);

$installer->endSetup();
