<?php

  $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http');
  $site_url =  $protocol . '://' . $_SERVER['HTTP_HOST'];
  $current_path = $_SERVER['REQUEST_URI'];

  $site['url'] = $site_url . $current_path;
  $site['base_url'] = rtrim($site_url, '/') . '/';
  $site['template_url'] = $site_url;

  require 'vendor/autoload.php';

	// Twig environment
	$loader = new Twig_Loader_Filesystem(array('twig'));
  $twig = new Twig_Environment($loader);

  $context = require 'context/context.php';

  // Build Version
  $build_version = time();
  $build_version_file = '/build-version.txt';

  if (file_exists($build_version_file)) {
    $file_contents = trim(file_get_contents($build_version_file));

    if (isset($file_contents) && !empty($file_contents)) {
      $build_version = $file_contents;
    }
  }

  $context['BUILD_VERSION'] = $build_version;

	// Simple router library
	$klein = new \Klein\Klein();

	// Respond to these routes.
  $klein->respond('GET', '/', 'home');
  $klein->respond('GET', '/[:name]', 'force_trailing_slash');
  $klein->respond('GET', '/[:name]/', 'route');
  $klein->dispatch();

  function force_trailing_slash($request) {
    header('Location: /' . $request->name . '/');
    exit;
  }

  function route($request) {
    global $twig, $context, $current_path;

    $template = 'pages/' . $request->name . '.twig';

    if (!file_exists('twig/' . $template)) {
      header('HTTP/1.0 404 Not Found');

      $data = 'context/data/pages/404.php';

      if (file_exists($data)) {
        $context['page'] = require $data;
      }

      $context['page']['class'] = 'page-404';
      $context['page']['path'] = $current_path;

      return $twig->render('pages/404.twig', $context);
    }

    $data = 'context/data/pages/' . $request->name . '.php';

    if (file_exists($data)) {
      $context['page'] = require $data;
    }

    $context['page']['class'] = 'page-' . $request->name;
    $context['page']['path'] = $current_path;

    return $twig->render($template, $context);
  }

  // Special routes
  function home() {
    global $twig, $context, $current_path;

    $data = 'context/data/pages/home.php';

    if (file_exists($data)) {
      $context['page'] = require $data;
    }

    $context['page']['class'] = 'page-home';
    $context['page']['path'] = $current_path;

    return $twig->render('pages/home.twig', $context);
  }
