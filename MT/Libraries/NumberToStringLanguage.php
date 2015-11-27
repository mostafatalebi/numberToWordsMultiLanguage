<?php namespace MT\Libraries;
use Symfony\Component\Config\Definition\Exception\Exception;


/**
 * Class NumberToStringLanguage
 * @package MT\Libraries
 */
class NumberToStringLanguage
{

    protected $configurations;

    /**
     * Current directory
     * @var
     */
    public $directory;


    /**
     * Currently used language. A two letter representing the language.
     * You may want to load your own language by following the Array
     * structure in the file.
     * @var string
     */
    protected $current_language = "";



    /**
     * A phrase which is used to connect two phrases
     * @var string
     */
    public $conjunctive_phrase = " و ";



    /**
     * An array of numbers with their string equivalent
     * @var array
     */
    public $number_category;


    /**
     * All languages loaded into an array
     * @var
     */
    public $languages;


    function __construct($current_language = "en")
    {
        $this->current_language = $current_language;
        $this->directory = __DIR__;

        try {
            $this->languages = include($this->directory."/languages.php");
            $this->configurations = include($this->directory."/configurations.php");
        }
        catch (Exception $e)
        {
            throw new Exception("Cannot find the languages file.");
        }

        $this->number_category = $this->languages['numbers'][$this->current_language];
        $this->conjunctive_phrase = $this->languages['conjunctive_phrases'][$this->current_language];
    }


    function reAssignLanguages()
    {
        $this->number_category = $this->languages['numbers'][$this->current_language];
        $this->conjunctive_phrase = $this->languages['conjunctive_phrases'][$this->current_language];

        return $this;
    }

    /**
     * Used to set a string equivalent for a number. It can be used
     * to override the existing values or to create something anew.
     * For example.
     * @param $number a number
     * @param $value the string, literal value of the number
     * @param $language the language which is going to have this new
     * equivalence (or override its existing equivalence)
     * @return NumberToStringLanguage
     */
    function setNumberLiteralEquivalent($number, $value, $language)
    {
        $this->languages[$language][$number] = $value;
        $this->reAssignLanguages();
        return $this;
    }

    /**
     * Gets the category of the numbers' literal equivalences
     * @return array
     */
    function getNumbersCategory()
    {
        return $this->number_category;
    }

    /**
     * Setting the current language. Use a two letters for
     * representing the main language.
     * @param $current_language
     * @return NumberToStringLanguage
     */
    function setCurrentLanguage($current_language)
    {
        $this->current_language = $current_language;
        $this->reAssignLanguages();
        return $this;
    }


    function isConjunctionAllowed($length)
    {
        $values = array_values($this->configurations['conjunctions']);
        return ( in_array($length, $values) ) ? true : false;
    }

    function getConjunction($length, $return_value = " ")
    {
        $values = array_values($this->configurations['conjunctions']);
        return ( array_key_exists($length, $this->conjunctive_phrase) ) ? $this->conjunctive_phrase[$length] : $return_value;
    }
}