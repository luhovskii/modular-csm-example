<?php
// Simple diagnostic helper for shared-hosting troubleshooting.
// Upload this file to your site root or the app folder and open it in a browser.
declare(strict_types=1);

function ok($v) { return $v ? '<span style="color:green">OK</span>' : '<span style="color:red">FAIL</span>'; }

?><!doctype html>
<html><head><meta charset="utf-8"><title>App diagnose</title></head><body>
<h1>Diagnostic</h1>
<ul>
  <li><strong>PHP version:</strong> <?php echo phpversion(); ?></li>
  <li><strong>Loaded extensions (json, mbstring, dom, openssl):</strong>
    <ul>
      <li>json: <?php echo ok(extension_loaded('json')); ?></li>
      <li>mbstring: <?php echo ok(extension_loaded('mbstring')); ?></li>
      <li>dom: <?php echo ok(extension_loaded('dom')); ?></li>
      <li>openssl: <?php echo ok(extension_loaded('openssl')); ?></li>
    </ul>
  </li>
  <li><strong>random_bytes available:</strong> <?php echo ok(function_exists('random_bytes')); ?></li>
  <li><strong>vendor/autoload.php exists:</strong> <?php echo ok(file_exists(__DIR__ . '/vendor/autoload.php')); ?></li>
  <li><strong>core/bootstrap.php exists:</strong> <?php echo ok(file_exists(__DIR__ . '/core/bootstrap.php')); ?></li>
  <li><strong>storage writable:</strong> <?php echo ok(is_writable(__DIR__ . '/storage') || is_writable(__DIR__ . '/storage/users.json')); ?></li>
  <li><strong>modules/blog/data writable:</strong> <?php echo ok(is_writable(__DIR__ . '/modules/blog/data') || is_writable(__DIR__ . '/modules/blog/data/posts.json')); ?></li>
  <li><strong>webserver user (uid/gid):</strong> <?php echo getmyuid() . '/' . getmygid(); ?></li>
  <li><strong>open_basedir:</strong> <?php echo ini_get('open_basedir') ?: '<em>none</em>'; ?></li>
  <li><strong>display_errors:</strong> <?php echo ini_get('display_errors') ?: 'Off'; ?></li>
  <li><strong>error_log:</strong> <?php echo ini_get('error_log') ?: '<em>php default</em>'; ?></li>
  <li><strong>Recommended temporary debug snippet:</strong>
    <pre style="background:#f4f4f4;padding:8px">// add at top of public/index.php
ini_set('display_errors', '1');
error_reporting(E_ALL);
// then refresh the failing page to see the error
    </pre>
  </li>
  <li><strong>Quick checks to run on the host (SSH or cPanel file manager):</strong>
    <pre style="background:#f4f4f4;padding:8px"># check php version
php -v
# check file ownership and perms
ls -la
# view latest apache/php error log (path varies, check cPanel 'Errors')
tail -n 200 /home/youruser/logs/error_log
    </pre>
  </li>
</ul>
<p>After you open this page on the server, paste the output here and I will interpret it and give exact fixes.</p>
</body></html>
