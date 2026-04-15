<?php
defined( 'ABSPATH' ) || exit;

class Xtrsl_Deactivator {
	public static function deactivate() {
		flush_rewrite_rules();
	}
}
