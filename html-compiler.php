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

$output_folder = 'public';
if (isset($options['output-dir']) && !empty($options['output-dir'])) {
  $output_folder = $options['output-dir'];
}

$page_in_subfolder = false;
if (isset($options['page-in-subfolder']) && !empty($options['page-in-subfolder'])) {
  $page_in_subfolder = $options['page-in-subfolder'] === 'true' ? true : false;
}

$compiler_current_path = dirname(__FILE__);

// Compile pages
$site['base_url'] = rtrim($site_url, '/') . '/';
$site['template_url'] = $template_url;

require 'vendor/autoload.php';

// Twig environment
$loader = new \Twig\Loader\FilesystemLoader(['twig', 'svgs']);
$twig = new \Twig\Environment($loader);

// Build Version
$build_version = time();

$demo_links = [];

$pages = glob('twig/pages/*.twig');
$components = glob('twig/components/*.twig');

if (!file_exists($output_folder)) {
  mkdir($output_folder, 0777);
}

$components_output_folder = "$output_folder/component";

if (!file_exists($components_output_folder)) {
  mkdir($components_output_folder, 0777);
}

foreach($pages as $page) {
  try {
    $route = basename($page, '.twig');
    $current_path = "/$route/";

    if ($page_in_subfolder) {
      $file_name = 'index';
      $page_output_folder = "$output_folder/$route";

      if ($route === 'home') {
        $current_path = '/';
        $page_output_folder = $output_folder;
      }

      if (!file_exists($page_output_folder)) {
        mkdir($page_output_folder, 0777);
      }
    } else {
      $file_name = $route;
      $page_output_folder = $output_folder;
    }

    $site['url'] = $site_url . $current_path;

    $template = 'pages/' . $route . '.twig';

    $context = require 'context/context.php';
    $context['BUILD_VERSION'] = $build_version;

    $html = $twig->render($template, $context);

    $html_file = fopen("$page_output_folder/$file_name.html", 'w') or die("\e[31m\e[1m" . "\nERROR\n");
    fwrite($html_file, $html);
    fclose($html_file);

    $demo_links[] = "$page_output_folder/$file_name.html";

    echo "\e[32m\xE2\x9C\x94\e[0m" . " $page_output_folder/$file_name.html\n";
  } catch (Exeption $e) {
    echo "\e[31m\xE2\x9D\x8C\e[0m" . " $page_output_folder/$file_name.html\n";
  }
}

$demo_links[] = 'separator';

foreach($components as $component) {
  try {
    $route = basename($component, '.twig');
    $current_path = "/component/$route/";

    if ($page_in_subfolder) {
      $file_name = 'index';
      $component_output_folder = "$components_output_folder/$route";

      if (!file_exists($component_output_folder)) {
        mkdir($component_output_folder, 0777);
      }
    } else {
      $file_name = $route;
      $component_output_folder = "$components_output_folder";
    }

    $site['url'] = $site_url . $current_path;

    $context = require 'context/context.php';
    $context['BUILD_VERSION'] = $build_version;

    $context['component_name'] = $route;

    $data = 'context/data/components/' . $route . '.php';

    if (file_exists($data)) {
      $context['component_data'] = require $data;
    }

    $html = $twig->render('component.twig', $context);

    $html_file = fopen("$component_output_folder/$file_name.html", 'w') or die("\e[31m\e[1m" . "\nERROR\n");
    fwrite($html_file, $html);
    fclose($html_file);

    $demo_links[] = "$component_output_folder/$file_name.html";

    echo "\e[32m\xE2\x9C\x94\e[0m" . " $component_output_folder/$file_name.html\n";
  } catch (Exeption $e) {
    echo "\e[31m\xE2\x9D\x8C\e[0m" . " $component_output_folder/$file_name.html\n";
  }
}

// Generate links file
$demo_links_html = '';

foreach($demo_links as $key => $demo_link) {
  if ($demo_link === 'separator') {
    $demo_links_html .= '<br><hr><br>';
  } else {
    $link = str_replace('index.html', '', $demo_link);
    $link = str_replace("$compiler_current_path/public", $site_url, $link);

    $demo_links[$key] = $link;
    $demo_links_html .= '<a href="' . $link . '" target="_blank">' . $link . '</a><br>';
  }
}

$demo_links_file = fopen("$output_folder/demo-links.html", 'w') or die("\e[31m\e[1m" . "\nERROR\n");
fwrite($demo_links_file, $demo_links_html);
fclose($demo_links_file);

echo "\n";

return true;
