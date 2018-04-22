<?php

$this->startSetup();

$this->getConnection()->dropColumn(
    $this->getTable('ignovate_driver/driver'),
    'filename'
);

$this->getConnection()->addColumn(
    $this->getTable('ignovate_driver/driver'),
    'file_1',
    'VARCHAR(255) AFTER `aadhaar_id`'
);

$this->getConnection()->addColumn(
    $this->getTable('ignovate_driver/driver'),
    'file_2',
    'VARCHAR(255) AFTER `pan_number`'
);

$this->getConnection()->addColumn(
    $this->getTable('ignovate_driver/driver'),
    'file_3',
    'VARCHAR(255) AFTER `driving_license`'
);

$this->getConnection()->addColumn(
    $this->getTable('ignovate_driver/driver'),
    'filename',
    'VARCHAR(255) AFTER `driving_license`'
);

$this->endSetup();