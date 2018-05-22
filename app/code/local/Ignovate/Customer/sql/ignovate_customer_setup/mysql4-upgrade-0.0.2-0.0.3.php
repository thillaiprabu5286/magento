<?php

$this->startSetup();

$this->addAttribute('customer_address', 'cus_email', array(
    'label'			=> 'Email',
    'type' 			=> 'varchar',
    'visible'		=> 0,
    'required'		=> 1,
    'position'		=> 15,
));