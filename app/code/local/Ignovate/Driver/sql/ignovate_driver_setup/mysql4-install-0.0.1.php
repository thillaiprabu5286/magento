<?php
$debug = true;
/** @var $this Mage_Catalog_Model_Resource_Setup */
$this->startSetup();

$tbl = $this->getTable('ignovate_driver/driver');
if ($this->getConnection()->isTableExists($tbl)) {
    $this->getConnection()->dropTable($tbl);
}

// Create supplier table
$tbl = $this->getConnection()
    ->newTable($tbl)
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        'unsigned'  => true,
    ), 'Supplier ID')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 128, array(
        'nullable'  => true,
    ), 'name')
    ->addColumn('pan_number', Varien_Db_Ddl_Table::TYPE_VARCHAR, 128, array(
        'nullable'  => true,
    ), 'pan_number')
    ->addColumn('aadhaar_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 128, array(
        'nullable'  => true,
    ), 'aadhaar_id')
    ->addColumn('phone', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'nullable'  => true,
    ), 'Phone')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable'  => false,
    ), 'Creation date')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable'  => false,
    ), 'Updated date')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'nullable'  => true,
        'default'   => 0
    ), 'status')
    ->setComment('Driver list')
;
$this->getConnection()->createTable($tbl);

$this->endSetup();
