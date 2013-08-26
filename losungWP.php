<?php
/*
Plugin Name: LosungWP
Plugin URI: http://
Description: Zeigt die täglichen Losungsverse als Dashboard Widget im Admin Panel
Version: 0.1a
Author: Hans-Helge Buerger
Author URI: http://hanshelgebuerger.de
License: GPL2
*/

// Function that outputs the contents of the dashboard widget
function lwp_dashboard_widget_function() {
	$datum = getdate();
	$losungen = plugin_dir_path(__FILE__) . "/src/losungen" . $datum['year'] . ".xml";

	if (!file_exists($losungen)) {
		echo "<p>Die Losungen von diesem Jahr sind noch nicht da. Ein Update k&ouml;nnte helfen.</p>";
		return;
	}

	$xml = simplexml_load_file($losungen);
	$losung = $xml->Losungen[ $datum['yday'] ];
	if ( !is_null($losung) ) {
		echo '<p class="losung-text">' . $losung->Losungstext . '</p>';
		echo '<p class="losung-vers">' . $losung->Losungsvers . '</p>';
		echo '<p class="lehrtext-text">' . $losung->Lehrtext . '</p>';
		echo '<p class="lehrtext-vers">' . $losung->Lehrtextvers . '</p>';

		echo '<p class="losung-copy"><a href="http://www.ebu.de" target="_blank" title="Evangelische Br&uuml;der-Unit&auml;t">&copy; Evangelische Br&uuml;der-Unit&auml;t – Herrnhuter Br&uuml;dergemeine</a> <br> <a href="http://www.losungen.de" target="_blank" title="www.losungen.de">Weitere Informationen finden Sie hier</a></p>';
	} else {
		echo "<p>Komischer Fehler: Konnte keine Losungsverse für diesen Tag finden.</p>";
		return;
	}
}

// Function used in the action hook
function lwp_add_dashboard_widgets() {
	wp_add_dashboard_widget('losung_dashboard_widget', 'Tägliche Losung', 'lwp_dashboard_widget_function');
}

// Register the new dashboard widget with the 'wp_dashboard_setup' action
add_action('wp_dashboard_setup', 'lwp_add_dashboard_widgets' );
