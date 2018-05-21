<?php

$this->startSetup();

$this->removeAttribute('customer_address', 'city_id');

$this->addAttribute('customer_address', 'city_id', array(
    'label'			=> 'City ID (Ref Store id)',
    'type' 			=> 'varchar',
    'visible'		=> 0,
    'required'		=> 1,
    'position'		=> 10,
));

$this->addAttribute('customer_address', 'door_no', array(
    'label'			=> 'Door No',
    'type' 			=> 'varchar',
    'visible'		=> 0,
    'required'		=> 1,
    'position'		=> 11,
));

$this->addAttribute('customer_address', 'apt_name', array(
    'label'			=> 'Appartment Name',
    'type' 			=> 'varchar',
    'visible'		=> 0,
    'required'		=> 1,
    'position'		=> 12,
));

$this->addAttribute('customer_address', 'landmark', array(
    'label'			=> 'Landmark',
    'type' 			=> 'varchar',
    'visible'		=> 0,
    'required'		=> 1,
    'position'		=> 13,
));