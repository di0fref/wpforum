<?php

/**
 * Smarty {timesince} function plugin
 * Type:     modifier<br>
 * Name:     timesince<br>
 * Purpose:  timesince
 *
 * @param string $original
 * @param boolean $uc_digits also capitalize "x123" to "X123"
 * @param boolean $lc_rest capitalize first letters, lowercase all following letters "aAa" to "Aaa"
 *
 * @return string
 * @author Fredrik Fahlstad
 * @return string|null
 */
function smarty_modifier_timesince($string, $uc_digits = false, $lc_rest = false)
{
	// array of time period chunks
	$string = strtotime($string);
	$chunks = array(
		array(60 * 60 * 24 * 365, 'year'),
		array(60 * 60 * 24 * 30, 'month'),
		array(60 * 60 * 24 * 7, 'week'),
		array(60 * 60 * 24, 'day'),
		array(60 * 60, 'hour'),
		array(60, 'minute'),
	);

	$today = time(); /* Current unix time  */
	$since = $today - $string;

	if ($since > 604800) {
		$print = strftime(get_option(AppBase::OPTION_DATE_FORMAT), $string);

		if ($since > 31536000) {
			$print .= ", " . date("Y", $string);
		}

		return $print;
	}

	for ($i = 0, $j = count($chunks); $i < $j; $i++) {

		$seconds = $chunks[$i][0];
		$name = $chunks[$i][1];

		// finding the biggest chunk (if the chunk fits, break)
		if (($count = floor($since / $seconds)) != 0) {
			break;
		}
	}

	$print = ($count == 1) ? '1 ' . $name : "$count {$name}s";

	return $print . " ago";

}