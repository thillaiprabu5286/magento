<?php

$this->startSetup();

$this->removeAttribute('customer_address', 'email');

$this->addAttribute('customer_address', 'street_name', array(
    'label'			=> 'Street Name',
    'type' 			=> 'varchar',
    'visible'		=> 0,
    'required'		=> 1,
    'position'		=> 15,
));