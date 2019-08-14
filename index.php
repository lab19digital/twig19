<?php

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

    $template = 'twig/pages/' . $request->name . '.twig';

    if (!file_exists($template)) {
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
      $context['page']['path'] = '/' . $request->name . '/';
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
      $context['page']['path'] = '/';
    }

    return $twig->render('pages/home.twig', $context);
  }
