<?php

$context = array();

$context['site'] = require 'data/site.php';

// Pages data
$pages_data_dir = dirname(__FILE__) . '/data/pages';

foreach (scandir($pages_data_dir) as $page) {
  $page_path = $pages_data_dir . '/' . $page;

  if (is_file($page_path) && strpos($page, '.php') !== false) {
    $page_name = str_replace('.php', '', $page);

    $context['page'][$page_name] = require $page_path;
  }
}

// Components data
$components_data_dir = dirname(__FILE__) . '/data/components';

foreach (scandir($components_data_dir) as $component) {
  $component_path = $components_data_dir . '/' . $component;

  if (is_file($component_path) && strpos($component, '.php') !== false) {
    $component_name = str_replace('.php', '', $component);

    $context['component'][$component_name] = require $component_path;
  }
}

return $context;
