<?php

$this->startSetup();

$this->addAttribute('customer_address', 'street_name', array(
    'label'			=> 'Street Name',
    'type' 			=> 'varchar',
    'visible'		=> 0,
    'required'		=> 1,
    'position'		=> 15,
));