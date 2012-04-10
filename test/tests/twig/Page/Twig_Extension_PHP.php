<?php

class Twig_Extension_PHP extends Twig_Extension {
    public function __construct() {}

    public function getFilters() {
        return array(
            'is_array'  => new Twig_Filter_Function('_is_array'),
        );
    }

    // interface required
    public function getName() {
        return 'phpFunctions';
    }
}

function _is_array($array = array()) {
    if (is_array($array) && count($array) > 0) {
        return TRUE;
    } else {
        return FALSE;
    }
}