<?php

$this->startSetup();


$this->getConnection()->addColumn(
    $this->getTable('ignovate_driver/driver'),
    'store_id',
    'INT(10) AFTER `id`'
);

$this->endSetup();