<?php

$this->startSetup();

$this->removeAttribute('customer_address', 'email');

$this->addAttribute('customer_address', 'cus_email', array(
    'label'			=> 'Email',
    'type' 			=> 'varchar',
    'visible'		=> 0,
    'required'		=> 1,
    'position'		=> 15,
));