<?php
/**
 * BeeSquad widget integration for WordPress
 *
 * Plugin Name: BeeSquad
 * Plugin URI: https://www.beesquad.ch/
 * Description: Add your BeeSquad Widget
 * Author: FHYVE SàRL <integration-wordpress@beesquad.ch>
 * Version: 1.0.0
 */

if (!defined("ABSPATH")) {
    exit;
}

require_once "beesquad-widget.class.php";

(new \Fhyve\BeeSquad\WordpressWidget\Plugin(__FILE__))->register();
