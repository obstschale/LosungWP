<?php
/*
Plugin Name: LosungWP
Plugin URI: http://blog.hanshelgebuerger.de/losungwp/
Description: Zeigt die täglichen Losungsverse als Dashboard Widget im Admin Panel an
Version: 0.0.1
Author: Hans-Helge Buerger
Author URI: http://hanshelgebuerger.de
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/*
License:
==============================================================================
Copyright 2013 Hans-Helge Buerger  (email : mail@hig-podcast.de)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

The Losungen of the Herrnhuter Brüdergemeine are copyrighted. Owner of
copyright is the Evangelische Brüder-Unität – Herrnhuter Brüdergemeine.
The biblical texts from the Lutheran Bible, revised texts in 1984, revised
edition with a new spelling, subject to the copyright of the German Bible
Society, Stuttgart.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

Requirements:
==============================================================================
This plugin requires WordPress >= 3.0 and tested with PHP Interpreter >= 5.3
*/

/**
 * provides the content for the dashboard widget.
 * It looks for a XML file with all bible verses for the current year,
 * picks the right one for today and outputs the text.
 * @return used if an error occured to end the function
 */
function lwp_dashboard_widget_function() {
	// get today's date and find the xml file in the subfolder
	// /src/ in plugin's directory.
	$datum = getdate();
	$losungen = plugin_dir_path( __FILE__ ) . "/src/losungen" . $datum['year'] . ".xml";

	// if no file for the current year is found print an error message and quit
	if ( ! file_exists($losungen) ) {
		echo "<p>Die Losungen von diesem Jahr sind noch nicht da. Ein Update k&ouml;nnte helfen.</p>";
		return;
	}

	// load xml file and find losung for today
	$xml = simplexml_load_file($losungen);
	$losung = $xml->Losungen[ $datum['yday'] -1 ];

	// if today's losung is found display text
	if ( !is_null($losung) ) {
		// display text in a <p>-Tag
		echo '<p class="losung-text">' . lwp_apply_format($losung->Losungstext) . '</p>';

		// align Biblevers right and link it to bibleserver.com
		echo '<p class="losung-vers" style="text-align: right;"><a href="http://www.bibleserver.com/go.php?lang=de&amp;bible=LUT&amp;ref=' . urlencode($losung->Losungsvers) . '" target="_blank" title="Auf bibleserver.com nachschlagen">' . $losung->Losungsvers . '</a></p>';

		// display text in a <p>-Tag
		echo '<p class="lehrtext-text">' . lwp_apply_format($losung->Lehrtext) . '</p>';

		// align Biblevers right and link it to bibleserver.com
		echo '<p class="lehrtext-vers" style="text-align: right;"><a href="http://www.bibleserver.com/go.php?lang=de&amp;bible=LUT&amp;ref=' . urlencode($losung->Lehrtextvers) . '" target="_blank" title="Auf bibleserver.com nachschlagen">' . $losung->Lehrtextvers . '</a></p>';

		// print copyright information
		echo '<p class="losung-copy"><a href="http://www.ebu.de" target="_blank" title="Evangelische Br&uuml;der-Unit&auml;t">&copy; Evangelische Br&uuml;der-Unit&auml;t – Herrnhuter Br&uuml;dergemeine</a> <br> <a href="http://www.losungen.de" target="_blank" title="www.losungen.de">Weitere Informationen finden Sie hier</a></p>';

	} else {
		echo "<p>Komischer Fehler: Konnte keine Losungsverse für diesen Tag finden.</p>";
		return;
	}
}

/**
 * this function uses RegEx to clean and format the text given by
 * the XML file
 * @param  String $text Bible verse to be formatted
 * @return String       formatted bible verse
 */
function lwp_apply_format($text)
	{
		$text = preg_replace('#/(.*?:)/#', '<span class="losung-losungseinleitung">$1</span>', $text, 1);
		$text = preg_replace('/#(.*?)#/', '<em>$1</em>', $text);
		
		return $text;
	}

// Function used in the action hook
function lwp_add_dashboard_widgets() {
	// create a dashboard widget and use 'losung_dashboard_widget' as html ID,
	// 'Tägliche Losung' as Title
	// and 'lwp_dashboard_widget_function' is the callback which is the
	// function to be called to provide the content
	wp_add_dashboard_widget('losung_dashboard_widget', 'Tägliche Losung', 'lwp_dashboard_widget_function');
}

// Register the new dashboard widget with the 'wp_dashboard_setup' action
add_action('wp_dashboard_setup', 'lwp_add_dashboard_widgets' );
