<?php

$this->startSetup();


$this->getConnection()->addColumn(
    $this->getTable('sales/order'),
    'driver_status',
    'VARCHAR(255)'
);

$this->getConnection()->addColumn(
    $this->getTable('sales/order_grid'),
    'driver_status',
    'VARCHAR(255)'
);

$this->endSetup();