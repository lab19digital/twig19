<?php

  if (php_sapi_name() !== 'cli') {
    echo "<strong style='color: red;'>COMMAND LINE ONLY!</strong>";
    die();
  } else {
    echo "\e[1m" . "\nCompiling HTML...\n\n";
  }

  $options = getopt(null, array('url:', 'template-url:', 'output-dir:'));

  $site_url = '/';
  $template_url = $site_url;
  $output_folder = '.';

  if (isset($options['url']) && !empty($options['url'])) {
    $site_url = $options['url'];
  }

  if (isset($options['template-url']) && !empty($options['template-url'])) {
    $template_url = $options['template-url'];
  }

  if (isset($options['output-dir']) && !empty($options['output-dir'])) {
    $output_folder = $options['output-dir'];
  }

  $cache_var = time();


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
    $file_name = ($route === 'home') ? 'index' : $route;

    $current_path = "/$route/";

    $site['url'] = $site_url . $current_path;

    $template = 'pages/' . $route . '.twig';
    $context = require 'context/context.php';
    $context['cache_var'] = $cache_var;
    $data = 'context/data/pages/' . $route . '.php';

    if (file_exists($data)) {
      $context['page'] = require $data;
    }

    $context['page']['class'] = 'page-' . $route;
    $context['page']['path'] = $current_path;

    $html = $twig->render($template, $context);

    $html_file = fopen("$output_folder/$file_name.html", 'w') or die("\e[31m\e[1m" . "\nERROR\n");
    fwrite($html_file, $html);
    fclose($html_file);

    echo "\e[32m\xE2\x9C\x94\e[0m" . " $output_folder/\e[1m$file_name\e[0m.html\n";
  }
