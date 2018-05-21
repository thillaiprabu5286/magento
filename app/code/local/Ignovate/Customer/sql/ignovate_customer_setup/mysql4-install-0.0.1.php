<?php

$this->startSetup();

$this->addAttribute('customer_address', 'city_id', array(
    'label'			=> 'City ID (Ref Store id)',
    'type' 			=> 'static',
    'visible'		=> 0,
    'required'		=> 1,
    'position'		=> 1,
));