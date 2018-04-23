<?php

$this->startSetup();

$this->getConnection()->dropColumn(
    $this->getTable('ignovate_driver/driver'),
    'file_1'
);
$this->getConnection()->dropColumn(
    $this->getTable('ignovate_driver/driver'),
    'file_2'
);
$this->getConnection()->dropColumn(
    $this->getTable('ignovate_driver/driver'),
    'file_3'
);
$this->getConnection()->dropColumn(
    $this->getTable('ignovate_driver/driver'),
    'filename'
);

$this->getConnection()->addColumn(
    $this->getTable('ignovate_driver/driver'),
    'file_aadhaar',
    'VARCHAR(255) AFTER `aadhaar_id`'
);

$this->getConnection()->addColumn(
    $this->getTable('ignovate_driver/driver'),
    'file_pan',
    'VARCHAR(255) AFTER `pan_number`'
);

$this->getConnection()->addColumn(
    $this->getTable('ignovate_driver/driver'),
    'file_license',
    'VARCHAR(255) AFTER `driving_license`'
);

$this->endSetup();