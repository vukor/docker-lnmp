<?php

putenv('MAGICK_THREAD_LIMIT=1');

if (array_key_exists('phpinfo', $_GET)) {
	phpinfo();
	exit;
}

define('PHP_RECOMMENDED_VERSION', '5.3.3');
define('APACHE_RECOMMENDED_VERSION', '2.2.17');
define('MYSQL_RECOMMENDED_VERSION', '5.1.51');

$previousConfig = array();
$previousConfig['error_reporting'] = ini_get('error_reporting');
$previousConfig['display_errors'] = ini_get('display_errors');
$previousConfig['html_errors'] = ini_get('html_errors');
$previousConfig['error_prepend_string'] = ini_get('error_prepend_string');
$previousConfig['error_append_string'] = ini_get('error_append_string');

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
ini_set('error_prepend_string', '<li class="error">');
ini_set('error_append_string', '</li>');
date_default_timezone_set('America/New_York');

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (array_key_exists('type', $_POST) && $_POST['type'] == 'mysql') {
		$_SESSION['htester']['mysql'] = array(
			'host' => $_POST['host'],
			'user' => $_POST['user'],
			'password' => $_POST['password'],
			'dbname' => $_POST['dbname'],
		);
		header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		exit;
	}

	if (array_key_exists('type', $_POST) && $_POST['type'] == 'mail') {
		if (empty($_SESSION['htester']['mail']['transport'])) {
			$_SESSION['htester']['mail'] = array('transport' => 'sendmail', 'params' => '', 'headers' => '', 'message' => '', 'error' => '', 'email' => '',);
		}
		if (function_exists('filter_var') && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			$_SESSION['htester']['mail']['error'] = 'Invalid email.';
			$_SESSION['htester']['mail']['email'] = $_POST['email'];
		}
		elseif ($_SESSION['htester']['mail']['transport'] == 'smtp' && empty($_SESSION['htester']['mail']['params'])) {
			$_SESSION['htester']['mail']['error'] = 'Invalid SMTP params.';
			$_SESSION['htester']['mail']['email'] = $_POST['email'];
		}
		else {
			$email = $_POST[ 'email' ];
			$subject = 'Test message from the site ' . $_SERVER[ 'SERVER_NAME' ];
			$body = 'Test message to check the sending of letters from the site ' . $_SERVER['SERVER_NAME'];
			if ($_SESSION['htester']['mail']['transport'] == 'sendmail') {
				$params = !empty($_SESSION['htester']['mail']['params']) ? $_SESSION['htester']['mail']['params'] : '';
				$headers = !empty($_SESSION['htester']['mail']['headers']) ? $_SESSION['htester']['mail']['headers'] : '';
				$r = mail($email, $subject, $body, $headers, $params);
				if ($r) {
					$_SESSION['htester']['mail']['message'] = 'Test message sent using mail().';
				}
				else {
					$_SESSION['htester']['mail']['error'] = 'mail() function returned false. The letter was not sent.';
				}
			}
			else {
				include_once 'zend-mail.php';
				$mail = new Zend_Mail();
				
				$headerIsSet = array(
					'from' => false,
					'subject' => false,
					'to' => false,
					'cc' => false,
					'bcc' => false,
					'reply-to' => false,
					'return-path' => false,
				);
				if (!empty($_SESSION['htester']['mail']['headers'])) {
					$headers = explode("\n", $_SESSION['htester']['mail']['headers']);
					foreach ($headers as $k => $v) {
						list($headerName, $headerValue) = explode(':', $v);
						$headerName = strtolower(trim($headerName));
						$headerValue = trim($headerValue);
						if ($headerName == 'from') {
							$headerIsSet['from'] = true;
							$mail->setFrom($headerValue);
						}
						elseif ($headerName == 'subject') {
							$headerIsSet['subject'] = true;
							$mail->setSubject($headerValue);
						}
						elseif ($headerName == 'to') {
							$headerIsSet['to'] = true;
							$mail->addTo($headerValue);
						}
						elseif ($headerName == 'cc') {
							$headerIsSet['cc'] = true;
							$mail->addCc($headerValue);
						}
						elseif ($headerName == 'bcc') {
							$headerIsSet['bcc'] = true;
							$mail->addBcc($headerValue);
						}
						elseif ($headerName == 'reply-to') {
							$headerIsSet['reply-to'] = true;
							$mail->setReplyTo($headerValue);
						}
						elseif ($headerName == 'return-path') {
							$headerIsSet['return-path'] = true;
							$mail->setReturnPath($headerValue);
						}
						else {
							$mail->addHeader($headerName, $headerValue);
						}
					}
				}
				if (!$headerIsSet['to']) {
					$mail->addTo($email);
				}
				if (!$headerIsSet['subject']) {
					$mail->setSubject($subject);
				}
				if (!$headerIsSet['from']) {
					$mail->setFrom('noreply@' . $_SERVER['SERVER_NAME']);
				}
				
				$mail->setBodyText($body);
						
				$params = parse_url($_SESSION['htester']['mail']['params']);
				$smtpParams = array('name' => $_SERVER['SERVER_NAME']);
				if (!empty($params['scheme']) && $params['scheme'] != 'noauth') {
					$smtpParams['auth'] = $params['scheme'];
				}
				if (!empty($params['user'])) {
					$smtpParams['username'] = $params['user'];
				}
				if (!empty($params['pass'])) {
					$smtpParams['password'] = $params['pass'];
				}
				$transport = new Zend_Mail_Transport_Smtp($params['host'], $smtpParams);
				$mail->send($transport);
				$_SESSION['htester']['mail']['message'] = 'Test message sent using Zend_Mail_Transport_Smtp(\'' . $params['host'] . '\', ' . var_export($smtpParams, true) . ').';
			}
		}
		header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		exit;
	}

	if (array_key_exists('type', $_POST) && $_POST['type'] == 'mail-params') {
		$_SESSION['htester']['mail'] = array(
			'transport' => $_POST['transport'],
			'params' => $_POST['params'],
			'headers' => setCorrectLineEndingInHeaders($_POST['headers']),
			'message' => '',
			'error' => '',
			'email' => '',
		);
		header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		exit;
	}
}

if (array_key_exists('mod_rewrite_test', $_GET)) {
	exit('mod_rewrite enabled');
}

if (array_key_exists('gd_test', $_GET)) {
	header('Content-type: image/png');
	$im = imagecreatetruecolor(16, 16);
	$bgcolor = imagecolorallocate($im, 0, 0, 0);
	imagefill($im, 0, 0, $bgcolor);
	imagepng($im);
	imagedestroy($im);
	exit;
}

headerHtml();

h1('Core');

$dd = '';
$type = 'ok';
if (version_compare(PHP_VERSION, '5.2.0', '<')) {
	$dd = '<span>System is incompatible with this and earlier versions.</span><br />';
	$type = 'error';
}
$dd .= 'Recommended: ' . PHP_RECOMMENDED_VERSION . '<br /><a href="?phpinfo">phpinfo()</a>';
item('PHP Version: <span>' . PHP_VERSION . '</span>', $dd, $type);

item('OS: <span>' . PHP_OS . '</span>', '', 'info');
itemConfigRequired('safe_mode', 'disabled', 'In the system may be errors.<br />This feature has been DEPRECATED as of PHP 5.3.0. Relying on this feature is highly discouraged.<br />');
itemConfigRequired('register_globals', 'disabled', 'Enabling this directive is not safe.<br />This feature has been DEPRECATED as of PHP 5.3.0. Relying on this feature is highly discouraged.');
itemConfigRequired('magic_quotes_gpc', 'disabled', 'Enabling this directive is not safe.<br />This feature has been DEPRECATED as of PHP 5.3.0. Relying on this feature is highly discouraged.');
itemConfigRequired('short_open_tag', 'enabled', '', 'The system may not work properly.');
itemConfigRequired('file_uploads', 'enabled', '', 'The system will not work properly.');
itemConfig('upload_max_filesize', '100M or higher to upload large files.');

itemConfig('post_max_size', '100M or higher to upload large files.');
$disabledFunctions = getConfigVar('disable_functions');
if (empty($disabledFunctions)) {
	item('disable_functions: <span><em>none</em></span>', '', 'ok');
}
else {
	item('disable_functions: <span>' . $disabledFunctions . '</span>', '', 'note');
}

h1('Errors');
itemConfig('error_reporting');
itemConfigRequired('display_errors', 'disabled');
itemConfigRequired('log_errors', 'enabled');
itemConfig('error_log');
$outputBuffering = (int)getConfigVar('output_buffering');
item('output_buffering' . ': <span>' . $outputBuffering . '</span>', ($outputBuffering != 0 ? '<span>Required: <strong>0</strong></span>' : ''), ($outputBuffering != 0 ? 'error' : 'ok'));

h1('Misc', true);
item('date_default_timezone: <span>' . date_default_timezone_get() . '</span>', '', 'info');
itemConfig('allow_url_fopen');
itemConfig('memory_limit');
itemConfig('max_execution_time');
itemConfig('zend.ze1_compatibility_mode');

$old = ini_get('precision');
ini_set('precision', $old + 1);
if (ini_get('precision') == $old + 1) {
	item('function ini_set: <span>works</span>', '', 'ok');
}
else {
	item('function ini_set: <span>does not work</span>', '', 'error');
}
ini_set('precision', $old);

/*
$locale = setlocale(LC_ALL, array('ru_RU.CP1251', 'ru_RU', 'russian_russia.1251',));
item('<span class="tooltip" title="setlocale(LC_ALL, array(\'ru_RU.CP1251\', \'ru_RU\', \'russian_russia.1251\',))">setlocale()</span> result: <span>' . $locale . '</span>', '', 'info');
*/
$locale = setlocale(LC_ALL, array('ru_RU.utf8', 'ru_RU', 'russian_russia',));
if ($locale === false) {
	$locale = 'false';
}
item('<span class="tooltip" title="setlocale(LC_ALL, array(\'ru_RU.utf8\', \'ru_RU\', \'russian_russia\',))">setlocale()</span> result: <span>' . $locale . '</span>', '', 'info');

h1('Extensions');
itemExtension('mysql', true);
itemExtension('mysqli', true);
itemExtension('gd', true, '<img src="?gd_test" /> &larr; there should be a black square.');
itemExtension('iconv');
itemExtension('mbstring');
$func_overload = (int)ini_get('mbstring.func_overload');
if ($func_overload === 2 || $func_overload === 3 || $func_overload === 7) {
	item('mbstring.func_overload: <span>2</span>', '', 'ok');
}
else {
	item('mbstring.func_overload: <span>' . $func_overload . '</span>', 'Required: 2 or 3 or 7<br />Recommended: 2', 'error');
}
itemConfig('mbstring.internal_encoding');
itemExtension('curl');
itemExtension('tidy');
//itemExtension('zip');
//itemExtension('pdo_mysql');
//itemExtension('mime_magic');

h1('ImageMagick');
if (function_exists('popen')) {
	if (stripos(PHP_OS, 'win') !== false) {
		item('this test is not implemented for Windows', '', 'info');
	}
	else
	{

		/*$result = nl2br(execute('whereis identify'));
if ($result == 'identify:') {
	item('not found', '', 'note');
}
else {
	item($result . '<br />' . nl2br(execute('whereis convert')), '', 'ok');
}*/

		$convert = execute('which convert');
		$identify = execute('which identify');

		if($convert)
		{
			item($convert.'<br>'.$identify, '', 'info');


			$version = execute('convert -version');
			$lines = preg_split('/\r\n|\r|\n/', $version, 2);

			item(nl2br($lines[0]), '', 'ok');

			if (array_key_exists('imagemagick_test', $_POST))
			{

				$imgSource = dirname(__FILE__) . '/test.jpg';
				$img = escapeshellarg($imgSource);

				$thumbSource = dirname(__FILE__) . '/test-resized.jpg';
				$thumb = escapeshellarg($thumbSource);

				@unlink(dirname(__FILE__).'/test-resized.jpg');

				$output = execute($convert.' '.$img.' -resize 16x16 '.$thumb);

				if (empty($output)) {
					if (file_exists($thumbSource)) {
						$size = getimagesize($thumbSource);
						if ($size && $size[0] == 16 && $size[1] == 16) {
							$output = '<img src="test-resized.jpg"> OK';
						}
						else {
							$output = 'Error: file test-resized.jpg was created but sizes are wrong';
						}
					}
					else {
						$output = 'Error: file test-resized.jpg was not created';
					}
				}
				else {
					$output = 'Error: ' . $output;
				}

				item('<form method="post" action="/htester/"><input type="hidden" name="imagemagick_test" value="1"><input type="submit" value="Test ImageMagick" style="margin-left: 0;"></form><br>Result: '.$output, '', 'info');

			}
			else
			{
				if(is_writable(dirname(__FILE__)))
				{
					item('<form method="post" action="/htester/"><input type="hidden" name="imagemagick_test" value="1"><input type="submit" value="Test ImageMagick" style="margin-left: 0;"></form>', '', 'info');
				}
				else
				{
					item('Directory htester is not writeable, test can not be performed', '', 'note');
				}
			}
		}
		else
		{
			item('not found', '', 'note');
		}
	}
}
else {
	item('<em>function popen is disabled, can not try to determine whether the ImageMagick installed or not</em>', '', 'note');
}

h1('MySQL');
if (class_exists('mysqli')) {
	if (!empty($_SESSION['htester']['mysql'])) {
		$mysql = $_SESSION['htester']['mysql'];
	}
	else {
		$mysql = array('host' => '', 'user' => '', 'password' => '', 'dbname' => '',);
	}
	itemForm('<form action="' . $_SERVER['REQUEST_URI'] . '" method="post"><input type="hidden" name="type" value="mysql" />
		<p><label for="host">Host *:</label> <input name="host" id="host" value="' . htmlspecialchars($mysql['host'], ENT_COMPAT, 'UTF-8') . '" /></p>
		<p><label for="user">User *:</label> <input name="user" id="user" value="' . htmlspecialchars($mysql['user'], ENT_COMPAT, 'UTF-8') . '" /></p>
		<p><label for="password">Password *:</label> <input type="password" name="password" id="password" /></p>
		<p><label for="dbname">DB name:</label> <input name="dbname" id="dbname" value="' . htmlspecialchars($mysql['dbname'], ENT_COMPAT, 'UTF-8') . '" /></p>
		<p><input type="submit" value="Save in Session" /></p>
	</form>', 'info');
	if (!empty($mysql['host'])) {
		$link = new mysqli($mysql['host'], $mysql['user'], $mysql['password']);
		if (empty($link->connect_error)) {
			item('Connect: <span>done</span>', '', 'ok');
			$mysqlVersion = $link->server_info;
			$dd = '';
			$type = 'ok';
			if (version_compare($mysqlVersion, '5.0.0', '<')) {
				$dd = '<span>System is incompatible with this and earlier versions.</span><br />';
				$type = 'error';
			}
			$dd .= 'Recommended: ' . MYSQL_RECOMMENDED_VERSION;
			item('Version: <span>' . $mysqlVersion . '</span>', $dd, $type);

			if (!empty($mysql['dbname'])) {
				$result = $link->select_db($mysql['dbname']);
				if ($result) {
					item('Select DB: <span>done</span>', '', 'ok');
				}
				else {
					item('Select DB: <span>failed</span>', '', 'error');
				}
			}
		}
		else {
			item('Connect: <span>failed</span>');
		}
	}
}
else {
	item('class mysqli does not exists', '', 'error');
}

$isNginx = isset($_SERVER["SERVER_SOFTWARE"]) && strpos($_SERVER["SERVER_SOFTWARE"], 'nginx') !== false;
if (!$isNginx) {
	function checkModRewrite() {
		$url = parse_url('http://example.com' . $_SERVER['REQUEST_URI']);
		$urlPath = str_replace('index.php', '', $url['path']);
	
		if (!getConfigVar('allow_url_fopen')) {
			item('mod_rewrite: <span>yes and <em>not</em> checked</span>', '<span>allow_url_fopen must be enabled</span><br />Required: yes and checked', 'error');
		}
		elseif (file_get_contents('http://' . $_SERVER['HTTP_HOST'] . $urlPath . 'mod-rewrite/') != 'mod_rewrite enabled') {
			if (file_exists('.htaccess')) {
				item('mod_rewrite: <span>yes and <em>not</em> checked</span>', '<span>.htaccess must contain the correct instructions for mod_rewrite. If it does, mod_rewrite is not working.</span><br />Required: yes and checked', 'error');
			}
			else {
				item('mod_rewrite: <span>yes and <em>not</em> checked</span>', '<span>.htaccess must be uploaded into the same directory as index.php</span><br />Required: yes and checked', 'error');
			}
		}
		else {
			item('mod_rewrite: <span>yes and checked</span>', '', 'ok');
		}
	}

	h1('Apache', true);
	if (function_exists('apache_get_version')) {
		$apacheVersion = apache_get_version();
		if (preg_match('/Apache\/([\d\.]+)/', $apacheVersion, $match)) {
			$apacheVersion = $match[1];
			item('Version: <span>' . $apacheVersion . '</span>', 'Recommended: ' . APACHE_RECOMMENDED_VERSION, 'info');
		}
		else {
			item('Version: <span><em>undefined</em></span>', 'Recommended: ' . APACHE_RECOMMENDED_VERSION, 'note');
		}
		$apacheModules = apache_get_modules();
		if (in_array('mod_rewrite', $apacheModules)) {
			checkModRewrite();
		}
		else {
			item('mod_rewrite: <span>no</span>', 'Required: yes and checked', 'error');
		}
		item('mod_expires: <span>' . (in_array('mod_expires', $apacheModules) ? 'yes' : 'no') . '</span>', '', 'info');
		item('mod_mime: <span>' . (in_array('mod_mime', $apacheModules) ? 'yes' : 'no') . '</span>', '', 'info');
		item('mod_include: <span>' . (in_array('mod_include', $apacheModules) ? 'yes' : 'no') . '</span>', '', 'info');
		item('mod_env: <span>' . (in_array('mod_env', $apacheModules) ? 'yes' : 'no') . '</span>', '', 'info');
	}
	else {
		item('PHP is <span>not</span> running as an Apache module.', '', 'info');
		checkModRewrite();
	}
}

h1('Directories', $isNginx);
$documentRoot = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
item('Document root: <span>' . $documentRoot . DIRECTORY_SEPARATOR . '</span>', '', 'info');
item('Is document root writeable: <span>' . (is_writeable($documentRoot) ? 'yes' : 'no') . '</span>', '', 'info');

foreach (array('tmp', 'upload') as $v) {
	if (!file_exists($documentRoot . DIRECTORY_SEPARATOR . $v . DIRECTORY_SEPARATOR)) {
		item($documentRoot . DIRECTORY_SEPARATOR . '<strong>' . $v . '</strong>' . DIRECTORY_SEPARATOR . ': <span>not exists</span>', '', 'info');
	}
	elseif (file_exists($documentRoot . DIRECTORY_SEPARATOR . $v . DIRECTORY_SEPARATOR) && !is_writeable($documentRoot . DIRECTORY_SEPARATOR . $v . DIRECTORY_SEPARATOR)) {
		item($documentRoot . DIRECTORY_SEPARATOR . '<strong>' . $v . '</strong>' . DIRECTORY_SEPARATOR . ': <span>exists, <em>not writeable</em></span>', '', 'error');
	}
	else {
		item($documentRoot . DIRECTORY_SEPARATOR . '<strong>' . $v . '</strong>' . DIRECTORY_SEPARATOR . ': <span>exists, writeable</span>', '', 'ok');
	}
}

h1('Mail');
if (!empty($_SESSION['htester']['mail'])) {
	$mail = $_SESSION['htester']['mail'];
	if (!empty($mail['error'])) {
		unset($_SESSION['htester']['mail']['error']);
	}
	if (!empty($mail['message'])) {
		unset($_SESSION['htester']['mail']['message']);
		if (!empty($mail['email'])) {
			unset($_SESSION['htester']['mail']['email']);
		}
	}
}
else {
	$mail = array('transport' => 'sendmail', 'params' => '', 'headers' => '', 'message' => '', 'error' => '', 'email' => '',);
}
itemForm('<form action="' . $_SERVER['REQUEST_URI'] . '" method="post"><input type="hidden" name="type" value="mail-params" />
	<p><strong>Settings</strong></p>
	<p>
		<input type="radio" name="transport" value="sendmail" id="tSendmail"' . ($mail['transport'] == 'sendmail' ? ' checked="checked"' : '') . ' /> <label for="tSendmail" class="forCheckbox">mail() (e.g. params: <code>-f d4mwww@dev4masses.com</code>)</label><br />
		<input type="radio" name="transport" value="smtp" id="tSmtp"' . ($mail['transport'] == 'smtp' ? ' checked="checked"' : '') . ' /> <label for="tSmtp" class="forCheckbox">SMTP (e.g. params: <code>login://user:password@host</code> or <code>noauth://host</code>)</label>
	</p>
	<p><label for="headers">Headers:</label> <textarea name="headers" id="headers">' . htmlspecialchars($mail['headers'], ENT_COMPAT, 'UTF-8') . '</textarea></p>
	<p><label for="params">Params:</label> <input name="params" id="params" value="' . htmlspecialchars($mail['params'], ENT_COMPAT, 'UTF-8') . '" /></p>
	<p><input type="submit" value="Save in Session" /></p>
</form>
<form action="' . $_SERVER['REQUEST_URI'] . '" method="post"><input type="hidden" name="type" value="mail" />
	<p><strong>Send Email</strong></p>
	' . (!empty($mail['error']) ? '<p class="error">' . $mail['error'] . '</p>' : '') . '
	' . (!empty($mail['message']) ? '<p class="message">' . $mail['message'] . '</p>' : '') . '
	<p><label for="email">Email:</label> <input name="email" id="email" value="' . htmlspecialchars($mail['email'], ENT_COMPAT, 'UTF-8') . '" /></p>
	<p><input type="submit" value="Send" /></p>
</form>', 'info');

footerHtml();

function setCorrectLineEndingInHeaders($headers) {
  $lines = explode("\n", $headers);
  foreach ($lines as $k => $v) {
  	$lines[$k] = trim($v);
  }
  return implode("\r\n", $lines);
}

function itemExtension($name, $isRequired = false, $notes = '') {
	static $extensions = null;
	if (is_null($extensions)) {
		$extensions = get_loaded_extensions();
	}
	$loaded = in_array($name, $extensions);
	item($name . ': <span>' . ($loaded ? 'yes' : 'no') . '</span>', (!empty($notes) ? '<span>' . $notes . '</span><br />' : '') . (!$loaded ? ($isRequired ? 'Required' : 'Recommended') . ': yes' : ''), (!$loaded ? ($isRequired ? 'error' : 'note') : 'ok'));
}

function itemConfigRequired($name, $required = 'disabled', $ifEnabled = '', $ifDisabled = '') {
	$state = 'disabled';

	$value = getConfigVar($name);
	if ($value === '1') {
		$state = 'enabled';
	}
	elseif ($value === '0') {
		$state = 'disabled';
	}
	elseif ($value === '' || $value === false) {
		$state = 'disabled';
	}
	item($name . ': <span>' . ($state == 'disabled' ? 'no' : 'yes') . '</span>', '<span>' . ($state == 'enabled' ? $ifEnabled : $ifDisabled) . ($required != $state ? '<br />Required: <strong>' . ($required == 'disabled' ? 'no' : 'yes') . '</strong>' : '') . '</span>', (($state == 'disabled' && $required == 'enabled') || ($state == 'enabled' && $required == 'disabled') ? 'error' : 'ok'));
}

function itemConfig($name, $recommended = '') {
	$value = getConfigVar($name);
	if ($value === '1') {
		$value = 'on';
	}
	elseif ($value === '0') {
		$value = 'off';
	}
	elseif ($value === '' || $value === false) {
		$value = 'off or not set';
	}
	$class = ' info';
	$val = preg_replace( "/\D/", '' , $value );
	if (($name=='upload_max_filesize' || $name=='post_max_size') && is_numeric($val) && $val < 32) {
		$class=' error';
	}
	item($name . ': <span>' . $value . '</span>', (!empty($recommended) ? 'Recommended: ' . $recommended : ''), $class);
}

function h1($value, $isNewColumn = false) {
	if ($isNewColumn) {
		echo '</ul><ul>' . PHP_EOL;
	}
	echo '<li class="h1">' . $value . '</li>' . PHP_EOL;
}

function item($main, $more = '', $type = '') {
	echo '<li class="' . $type . '"><p class="main">' . $main . '</p><p class="more">' . $more . '</p>' . getAdviceHtml($main, $type) . '</li>' . PHP_EOL;
}

function itemForm($main, $type = '') {
	echo '<li class="' . $type . '">' . $main . '</li>' . PHP_EOL;
}

function headerHtml() {
	echo '<!doctype html>' . PHP_EOL;
	echo '<meta charset="UTF-8" />' . PHP_EOL;
	echo '<title>Adex Hosting Tester</title>' . PHP_EOL;
	echo '<link rel="stylesheet" type="text/css" href="design.css" />' . PHP_EOL;
	echo '<body>' . PHP_EOL;
	echo '<ul>' . PHP_EOL;
}

function footerHtml() {
	echo '</ul>' . PHP_EOL;
	echo '</body>' . PHP_EOL;
}

function getConfigVar($name) {
	global $previousConfig;
	if (array_key_exists($name, $previousConfig)) {
		return $previousConfig[$name];
	}
	return ini_get($name);
}

function execute($cmd) {
	$handle = popen($cmd . ' 2>&1', 'r');
	if (!$handle) {
		return false;
	}

	$data = '';
	while(!feof( $handle )) {
		$data .= fread($handle, 4096);
	}
	$data = trim($data);
	if (strpos($data, 'sh:') === 0) {
		trigger_error($data, E_USER_WARNING);
		return false;
	}

	pclose($handle);

	return $data;
}

function phpFlag($name, $value) {
  return '.htaccess: php_flag ' . $name . ' ' . $value;
}

function phpValue($name, $value) {
  return '.htaccess: php_value ' . $name . ' ' .$value;
}

function iniSystem() {
  return 'Can be set in php.ini or httpd.conf';
}

function getAdviceHtml($topic, $status) {
	$answers = array(
  		'safe_mode' => iniSystem(),
  		'register_globals' => phpFlag('register_globals', 'off'),
  		'magic_quotes_gpc' => iniSystem(),
  		'short_open_tag' => phpFlag('short_open_tag', 'off'),
  		'file_uploads' => iniSystem(),
  		'upload_max_filesize' => phpValue('upload_max_filesize', '100M'),
  		'post_max_size' => phpValue('post_max_size', '100M'),
  		'disable_functions' => iniSystem(),
  		'display_errors' => phpFlag('display_errors', 'off'),
  		'log_errors' => phpFlag('log_errors', 'on'),
  		'output_buffering' => phpValue('output_buffering', '0'),
  		'mysql' => iniSystem(),
  		'mysqli' => iniSystem(),
  		'gd' => iniSystem(),
  		'iconv' => iniSystem(),
  		'mbstring' => iniSystem(),
  		'mbstring.func_overload' => iniSystem(),
  		'curl' => iniSystem(),
  		'tidy' => iniSystem(),
  	);
  	
  	$parts = explode(':', $topic);
  	if ($parts) {
  		$topic = $parts[0];
  	}
  
	if (($status == 'error' || $status == 'note') && array_key_exists($topic, $answers)) {
  		return '<p class="more"><br>' . $answers[$topic] . '</p>';
  	}
  	else {
  		return '';
  	}
}
