<?php

$this->startSetup();


$this->getConnection()->addColumn(
    $this->getTable('sales/order'),
    'driver',
    'INT(10)'
);

$this->getConnection()->addColumn(
    $this->getTable('sales/order_grid'),
    'driver',
    'INT(10)'
);

$this->endSetup();