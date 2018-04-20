<?php

$this->startSetup();

$this->getConnection()->addColumn(
    $this->getTable('ignovate_driver/driver'),
    'driving_license',
    'VARCHAR(255) AFTER `aadhaar_id`'
);

$this->getConnection()->addColumn(
    $this->getTable('ignovate_driver/driver'),
    'filename',
    'VARCHAR(255) AFTER `driving_license`'
);

$this->endSetup();