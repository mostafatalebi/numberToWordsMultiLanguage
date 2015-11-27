<?php
ini_set("display_errors", 1);
require_once "loader.php";

echo "<meta charset='utf8' />";
/**
 * Using the NumberToWords in its default mode.
 */
$numberToWords = new \MT\Libraries\NumberToWordsUtility();
$number = 15847855;
$result = $numberToWords->runProcessor($number);
print "This is the number: <strong>{$number}</strong><br />This is its literal equivalence: <strong>{$result}</strong><br />";



/**
 * Change the language on the fly
 */
$numberToWords = new \MT\Libraries\NumberToWordsUtility();
$number = 15847855;
$result = $numberToWords->setCurrentLanguage("fa")->runProcessor($number);
print "This is the number: <strong>{$number}</strong><br />This is its literal equivalence: <strong>{$result}</strong><br />";


