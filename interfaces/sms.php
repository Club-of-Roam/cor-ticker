<?php
if ( ! empty( $_POST['msisdn'] ) ) {
	define('SHORTINIT', true);
	require 'wp-load.php';

	$type = 'MMS' == $_POST['type'] ? 2 : 1;
	$from = addslashes( urldecode( trim( $_POST['msisdn'] ) ) );
	$msg = ! empty( $_POST['msg'] ) ? addslashes( urldecode( trim( $_POST['msg'] ) ) ) : '';
	$img = '';

	if ( 2 === $type ) {
		if ( $_FILES['file'] && $_FILES['file']['error'] == UPLOAD_ERR_OK ) {
			$tmp_name = $_FILES['file']['tmp_name'];
			list($ext, $filename) = explode('.', strrev($_FILES['file']['name']));
			$name = md5($filename.time()).'.'.strrev($ext);
			move_uploaded_file($tmp_name, 'wp-content/uploads/mms/'.$name);
		}
		$img = $name;
	}

	$wpdb->insert(
		$wpdb->prefix.'sms_ticker',
		array(
			'from' => $from,
			'msg' => $msg,
			'type' => $type,
			'img_url' => $img,
			'timestamp' => time()
		),
		array('%s', '%s', '%d', '%s', '%d')
	);

	echo 'OK';

} else {
	echo '400';
}
?>
