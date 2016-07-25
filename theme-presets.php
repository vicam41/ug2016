<?php
// make sure to not include translations
$args['presets']['default'] = array(
    'title' => 'Default',
    'demo' => 'http://demo.mythemeshop.com/schema/',
    'thumbnail' => get_template_directory_uri().'/options/demo-importer/demo-files/default/thumb.jpg', // could use external url, to minimize theme zip size
    'menus' => array( 'primary-menu' => 'Primary Menu', 'secondary-menu' => 'Secondary Menu' ), // menu location slug => Demo menu name
);

$args['presets']['minimal'] = array(
    'title' => 'Minimal',
    'demo' => 'http://demo.mythemeshop.com/schema-light/',
    'thumbnail' => get_template_directory_uri().'/options/demo-importer/demo-files/minimal/thumb.jpg', // could use external url, to minimize theme zip size
    'menus' => array( 'primary-menu' => 'Primary Menu', 'secondary-menu' => 'Secondary Menu' ), // menu location slug => Demo menu name
);

global $mts_presets;
$mts_presets = $args['presets'];
