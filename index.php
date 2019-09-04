<?php

  $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http');
  $site_url =  $protocol . '://' . $_SERVER['HTTP_HOST'];
  $current_path = $_SERVER['REQUEST_URI'];

  $site['base_url'] = $site_url . '/';
  $site['url'] = $site_url . $current_path;
  $site['path'] = $current_path;

  require 'vendor/autoload.php';

	// Twig environment
	$loader = new Twig_Loader_Filesystem(array('twig'));
  $twig = new Twig_Environment($loader);

  $context = require 'context/context.php';

	// Simple router library
	$klein = new \Klein\Klein();

	// Respond to these routes.
	$klein->respond('GET', '/', 'home');
  $klein->respond('GET', '/[:name]/', 'route');
  $klein->dispatch();

  function route($request) {
    global $twig, $context;

    $template = 'pages/' . $request->name . '.twig';

    if (!file_exists('twig/' . $template)) {
      header('HTTP/1.0 404 Not Found');

      $data = 'context/data/pages/404.php';

      if (file_exists($data)) {
        $context['page'] = require $data;
        $context['page']['class'] = 'page-404';
      }

      return $twig->render('404.twig', $context);
    }

    $data = 'context/data/pages/' . $request->name . '.php';

    if (file_exists($data)) {
      $context['page'] = require $data;
      $context['page']['class'] = 'page-' . $request->name;
    }

    return $twig->render($template, $context);
  }

  // Special routes
  function home() {
    global $twig, $context;

    $data = 'context/data/pages/home.php';

    if (file_exists($data)) {
      $context['page'] = require $data;
      $context['page']['class'] = 'page-home';
    }

    return $twig->render('pages/home.twig', $context);
  }
