<?php

  if (php_sapi_name() !== 'cli') {
    echo "<strong style='color: red;'>COMMAND LINE ONLY!</strong>";
    die();
  } else {
    echo "\e[1m" . "\nCompiling HTML...\n\n";
  }

  $options = getopt(null, array('url:', 'template-url:', 'output-dir:', 'page-in-subfolder:'));

  $site_url = '';
  if (isset($options['url']) && !empty($options['url'])) {
    $site_url = rtrim($options['url'], '/');
  }

  $template_url = $site_url;
  if (isset($options['template-url']) && !empty($options['template-url'])) {
    $template_url = $options['template-url'];
  }

  $output_folder = '.';
  if (isset($options['output-dir']) && !empty($options['output-dir'])) {
    $output_folder = $options['output-dir'];
  }

  $pageInSubfolder = false;
  if (isset($options['page-in-subfolder']) && !empty($options['page-in-subfolder'])) {
    $pageInSubfolder = $options['page-in-subfolder'] === 'true' ? true : false;
  }

  $BUILD_VERSION = time();


	// Twig environment
  require 'vendor/autoload.php';

	$loader = new Twig_Loader_Filesystem(array('twig', 'twig/pages'));
  $twig = new Twig_Environment($loader);


  // Compile pages
  $site['base_url'] = rtrim($site_url, '/') . '/';
  $site['template_url'] = $template_url;

  $pages = glob('twig/pages/*.twig');

  if (!file_exists($output_folder)) {
    mkdir($output_folder, 0777);
  }

  foreach($pages as $page) {
    $route = basename($page, '.twig');
    $current_path = "/$route/";

    if ($pageInSubfolder && $route !== 'home') {
      $file_name = 'index';
      $page_output_folder = "$output_folder/$route";

      if (!file_exists($page_output_folder)) {
        mkdir($page_output_folder, 0777);
      }
    } elseif ($route === 'home') {
      $current_path = '/';
      $file_name = 'index';
      $page_output_folder = $output_folder;
    } else {
      $file_name = $route;
      $page_output_folder = $output_folder;
    }

    $site['url'] = $site_url . $current_path;

    $template = 'pages/' . $route . '.twig';
    $context = require 'context/context.php';
    $context['BUILD_VERSION'] = $BUILD_VERSION;
    $data = 'context/data/pages/' . $route . '.php';

    if (file_exists($data)) {
      $context['page'] = require $data;
    }

    $context['page']['class'] = 'page-' . $route;
    $context['page']['path'] = $current_path;

    $html = $twig->render($template, $context);

    $html_file = fopen("$page_output_folder/$file_name.html", 'w') or die("\e[31m\e[1m" . "\nERROR\n");
    fwrite($html_file, $html);
    fclose($html_file);

    echo "\e[32m\xE2\x9C\x94\e[0m" . " $page_output_folder/$file_name.html\n";
  }

  echo "\n";

  return true;
