<?php

$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http');
$site_url =  $protocol . '://' . $_SERVER['HTTP_HOST'];
$current_path = $_SERVER['REQUEST_URI'];

$site['url'] = $site_url . $current_path;
$site['base_url'] = rtrim($site_url, '/') . '/';
$site['template_url'] = $site_url;

require 'vendor/autoload.php';

// Twig environment
$loader = new \Twig\Loader\FilesystemLoader(['twig', 'svgs']);
$twig = new \Twig\Environment($loader, [
  'debug' => true
]);
$twig->addExtension(new \Twig\Extension\DebugExtension());

$context = require 'context/context.php';

// Build Version
$build_version = time();

$context['BUILD_VERSION'] = $build_version;

// Simple router library
$klein = new \Klein\Klein();

// Respond to these routes.
$klein->respond('GET', '/', 'home_route');
$klein->respond('GET', '/[:name]', 'force_trailing_slash');
$klein->respond('GET', '/[:name]/', 'route');
$klein->respond('GET', '/component/[:name]', 'force_trailing_slash');
$klein->respond('GET', '/component/[:name]/', 'component_route');
$klein->dispatch();

function force_trailing_slash($request) {
  $params = $request->paramsNamed();
  header('Location: ' . $params[0] . '/');
  exit;
}

function route($request) {
  global $twig, $context;

  $template = 'pages/' . $request->name . '.twig';

  if (!file_exists('twig/' . $template) || $request->name === 'home') {
    header('HTTP/1.0 404 Not Found');

    return $twig->render('pages/404.twig', $context);
  }

  return $twig->render($template, $context);
}

function component_route($request) {
  global $twig, $context;

  $template = 'components/' . $request->name . '.twig';

  if (!file_exists('twig/' . $template)) {
    header('HTTP/1.0 404 Not Found');

    return $twig->render('pages/404.twig', $context);
  }

  $context['component_name'] = $request->name;

  $data = 'context/data/components/' . $request->name . '.php';

  if (file_exists($data)) {
    $context['component_data'] = require $data;
  }

  return $twig->render('component.twig', $context);
}

function home_route() {
  global $twig, $context;

  $template = 'pages/home.twig';

  if (!file_exists('twig/' . $template)) {
    header('HTTP/1.0 404 Not Found');

    return $twig->render('pages/404.twig', $context);
  }

  return $twig->render($template, $context);
}
