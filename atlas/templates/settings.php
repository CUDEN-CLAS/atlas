<?php
/**
 * @file
 * Drupal site-specific configuration file.
 *
 * We include a pre and post local file so that we can set variables for the
 * main file and override config as needed.
 */

/**
 * Include a pre local settings file if it exists.
 */
$local_pre_settings = dirname(__FILE__) . '/settings.local_pre.php';
if (file_exists($local_pre_settings)) {
  include $local_pre_settings;
}

if (isset($launched) && $launched && isset($conf["cu_path"])) {
  if (isset($_SERVER['WWWNG_ENV'])) {
    if ($_SERVER['HTTP_HOST'] == 'clas.ucdenver.edu' &&
      strpos($_SERVER['REQUEST_URI'], $conf['cu_sid']) !== false) {
      header('HTTP/1.0 301 Moved Permanently');
      header('Location: https://clas.ucdenver.edu'. str_replace($conf['cu_sid'], $conf["cu_path"], $_SERVER['REQUEST_URI']));
      exit();
    }
    elseif ($_SERVER['HTTP_HOST'] == 'clas-test.ucdenver.pvt' &&
      strpos($_SERVER['REQUEST_URI'], $conf['cu_sid']) !== false) {
      header('HTTP/1.0 301 Moved Permanently');
      header('Location: https://clas-test.ucdenver.pvt'. str_replace($conf['cu_sid'], $conf["cu_path"], $_SERVER['REQUEST_URI']));
      exit();
    }
    elseif ($_SERVER['HTTP_HOST'] == 'www-dev.ucdenver.edu' &&
      strpos($_SERVER['REQUEST_URI'], $conf['cu_sid']) !== false) {
      header('HTTP/1.0 301 Moved Permanently');
      header('Location: https://www-dev.ucdenver.edu'. str_replace($conf['cu_sid'], $conf["cu_path"], $_SERVER['REQUEST_URI']));
      exit();
    }
    elseif ($_SERVER['HTTP_HOST'] == 'express.local' &&
      strpos($_SERVER['REQUEST_URI'], $conf['cu_sid']) !== false) {
      header('HTTP/1.0 301 Moved Permanently');
      header('Location: https://express.local'. str_replace($conf['cu_sid'], $conf["cu_path"], $_SERVER['REQUEST_URI']));
      exit();
    }
  }
}

$host = $_SERVER['HTTP_HOST'];

// Compress cached pages always off; we use mod_deflate
$conf['page_compression'] = 0;

// Set up environment specific variables for wwwng.
// If wwwng env isset or php executed through cli (drush).
if (isset($_SERVER["WWWNG_ENV"]) || PHP_SAPI === "cli") {

  // Never allow updating modules through UI.
  $conf['allow_authorize_operations'] = FALSE;

  // Caching across all of wwwng.
  $conf['cache'] = 1;
  // @todo Solve js inclusion issues to re-enable block cache.
  // @see #attached.
  $conf['block_cache'] = 0;

  // Aggregate css and js files.
  $conf['preprocess_css'] = TRUE;
  $conf['preprocess_js'] = TRUE;

  // Drupal doesn't cache if we invoke hooks during bootstrap.
  $conf['page_cache_invoke_hooks'] = FALSE;

  // Memcache and Varnish Backends.
  $conf['cache_backends'] = array(

{% if environment != 'local' %}
    'profiles/{{profile}}/modules/contrib/varnish/varnish.cache.inc',
{% endif %}
    'profiles/{{profile}}/modules/contrib/memcache/memcache.inc',
  );

  // Memcache lock file location.
  $conf['lock_inc'] = 'profiles/{{profile}}/modules/contrib/memcache/memcache-lock.inc';

  // Setup cache_form bin.
  $conf['cache_class_cache_form'] = 'DrupalDatabaseCache';

{% if environment != 'local' %}
  // Set varnish as the page cache.
  $conf['cache_class_cache_page'] = 'VarnishCache';
{% endif %}

  // Set memcache as default.
  $conf['cache_default_class'] = 'MemCacheDrupal';

  // Memcache bins and stampede protection.
  $conf['memcache_bins'] = array('cache' => 'default');

  // Set to FALSE on Jan 5, 2012 - drastically improved performance.
  $conf['memcache_stampede_protection'] = FALSE;
  $conf['memcache_stampede_semaphore'] = 15;
  $conf['memcache_stampede_wait_time'] = 5;
  $conf['memcache_stampede_wait_limit'] = 3;

  // Disable poorman cron.
  $conf['cron_safe_threshold'] = 0;

  // No IP blocking from the UI, we'll take care of that at a higher level.
  $conf['blocked_ips'] = array();

  // Change colors and text for environment indicator based on ENV var.
  if (isset($_SERVER['WWWNG_ENV'])) {
    global $base_url;

    switch($_SERVER['WWWNG_ENV']) {
      case 'cust_dev':
        $base_url .= 'https://www-dev.ucdenver.edu';
        break;

      case 'cust_test':
        $base_url .= 'https://clas-test.ucdenver.pvt';
        break;

      case 'cust_prod':
        $base_url .= 'https://clas.ucdenver.edu';
        break;

      case 'express_local':
        $base_url .= 'https://express.local';
        break;

    }
    if ($pool != "poolb-homepage") {
      $base_url .= '/' . $path;
    }
  }
}

// Memcache
$conf['memcache_key_prefix'] = $conf['cu_sid'];
$conf['memcache_servers'] = array(
  {% for ip in memcache_servers -%}
  '{{ip}}' => 'default',
  {% endfor %}
);

{% if environment != 'local' %}
// Varnish
$conf['reverse_proxy'] = TRUE;
$conf['reverse_proxy_addresses'] = array({% for ip in reverse_proxies -%}'{{ip}}',{% endfor %});
// Drupal will look for IP in $_SERVER['X-Forwarded-For']
$conf['reverse_proxy_header'] = 'X-Forwarded-For';
// Define Varnish Server Pool and version.
$conf['varnish_control_terminal'] = '{{varnish_control}}';
$conf['varnish_version'] = 4;
{% if environment == 'test' %}
  $conf['varnish_control_key'] = substr(file_get_contents('/etc/varnish/secret'),0,-1);
{% endif %}
{% endif %}

{% if environment in ['local','dev'] %}
$conf['drupal_http_request_fails'] = FALSE;
{% endif %}
// Google Analytics
$conf['googleanalytics_account'] = 'UA-733655-8';

{% if environment == 'local' %}
$conf['error_level'] = 2;
{% endif %}

/**
 * Include a post local settings file if it exists.
 */
$local_post_settings = dirname(__FILE__) . '/settings.local_post.php';
if (file_exists($local_post_settings)) {
  include $local_post_settings;
}
