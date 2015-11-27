<?php namespace MT\Libraries;

/**
 * @author Mostafa Talebi
 * @email most.talebi@gmail.com
 * @desc A multi-language tool which parses the numbers into fully
 * qualified literal words. It can be used for any number.
 * Class NumberToWordsUtility
 * @package MT\Libraries
 * @version 0.0.13
 */
class NumberToWordsUtility
{
    public $languageUtiliy;

    protected $number;
    protected $number_category;
    protected $conjunctive_phrase;

    /**
     * An instance of NumberToWordsUtility
     * NumberToWordsUtility constructor.
     * @param string $language_in_use a two letters sign of the language
     */
    function __construct($language_in_use = "en")
    {
        $this->languageUtiliy = new NumberToWordsLanguage($language_in_use);
        $this->number_category = $this->languageUtiliy->number_category;
        $this->conjunctive_phrase = $this->languageUtiliy->conjunctive_phrase;

    }

    /**
     * @param $root_number
     * @return string
     */
    function getGeneralCategory($root_number)
    {
        $root_number = (string)$root_number;
        $length = strlen($root_number);
        if( $length == 1 || $length == 2)
            return "";
        else
            return $this->number_category[$root_number];
    }

    /**
     * Finds the category of current numbers. Ones, hundreds, thousands, millions etc.
     * @param $number
     * @return string a word representing the current category of the number. For instance
     * "Thousands" [en] is returned (without [en]) as an English language equivalent of 8600
     */
    function getGeneralBase($number)
    {
        $length = strlen((string)$number);
        $number_new = "1";
        for($i = 1; $i < $length; $i++)
        {
            $number_new .= "0";
        }
        return $number_new;
    }

    /**
     * Checks to see how many number should be used to extract the base
     * number of the current numbers. For instance, for 1000 it is 1 and
     * for 25000 it is 25 and for 253820 it is 253. No matter how the number
     * is big, it does not exceed lengthier than 3. Because our base units are
     * ones, tens and hundreds.
     * @param $number
     * @return int
     */
    function getTheRootNumbers($number)
    {
        $len = strlen((string)$number);
        switch($len)
        {
            case 1 :
                return 1;
                break;
            case 2 :
                return 1;
                break;
            case 3 :
                return 1;
                break;
            case 4 :
                return 1;
                break;
            case 5 :
                return 2;
                break;
            case 6 :
                return 3;
                break;
            case 7 :
                return 1;
                break;
            case 8 :
                return 2;
                break;
            case 9 :
                return 3;
                break;
            case 10:
                return 1;
                break;
        }


    }


    /**
     * Redirects the number processing flow to another processor.
     * @param $number
     * @return string
     */
    function relayToCorrectProcessor($number)
    {
        $number = $this->removePrecedingZeros($number);
        $length = strlen((string)$number);
        $result = "";

        if( is_numeric($number) )
        {
            switch($length)
            {
                case 1:
                    $result = $this->processOnes($number);
                    break;
                case 2:
                    $result = $this->processTens($number);
                    break;
                case 3:
                    $result = $this->processHundreds($number);
                    break;
                default :
                    $result = $this->runProcessor($number);
                    break;
            }
        }
        return $result;
    }

    /**
     * Resets the variables and re-assigns the language strings. It is used
     * when the current language is changed.
     * @return $this
     */
    function reAssignLanguages()
    {
        $this->number_category = $this->languageUtiliy->number_category;
        $this->conjunctive_phrase = $this->languageUtiliy->conjunctive_phrase;

        return $this;
    }


    /**
     * Checks to see if the given number is appropriate for the current processor
     * or its processor must be changed. This checking is the result of simple
     * comparison between the length of the current number and a legal range
     * @param $current_number_length the length of current number
     * @param $length_min minimum allowed length
     * @param int $length_max maximum allowed lenght
     * @return bool true if it is not allowed and false if it is allowed
     */
    function ifNeedsRelayingTheProcessor($current_number_length, $length_min, $length_max = 0)
    {
        $length_max = ($length_max == 0 || $length_max == NULL)
            ? $length_min : $length_max;

        return ($current_number_length >= $length_min && $current_number_length <= $length_max)
            ? false : true;

    }

    /**
     * Removes any zero from the beginning of the number
     * @param $number
     * @return bool|int|string
     */
    function removePrecedingZeros($number)
    {
        if(!is_numeric($number))
            return false;

        $number = (string)$number;
        $number = preg_replace("/^0+/", "", $number);
        return (!is_numeric($number)) ? "" : (int)$number;
    }

    /**
     * Processes the numbers from 0 to 9
     * @param $number the number which is going to be converted into literal
     * equivalence
     * @return string a string of word(s) representing the literal equivalence
     * of the given $number
     */
    function processOnes($number)
    {
        return $this->number_category[$number];
    }

    /**
     * Processes the numbers from 0 to 99
     * @param $number the number which is going to be converted into literal
     * equivalence
     * @return string a string of word(s) representing the literal equivalence
     * of the given $number
     */
    function processTens($number)
    {
        $initial_length = strlen((string)$number);
        $number = $this->removePrecedingZeros($number);
        if($this->ifNeedsRelayingTheProcessor($initial_length, 2, 2))
        {
            return $this->relayToCorrectProcessor($number);
        }

        $final_string = "";
        $number_category = $this->number_category;
        if($number < 20)
        {
            $final_string = $number_category[(int)$number];
        }
        else
        {
            $number_str_array = (string)$number;
            if( preg_match("/0$/", $number_str_array) )
            {
                $final_string = $number_category[(int)$number];
            }
            else
            {
                $tens_str = $number_category[(int)$number_str_array[0]*10];
                $tens_str .= $this->languageUtiliy->getConjunction($initial_length);
                $tens_str .= $number_category[(int)$number_str_array[1]];
                $final_string = $tens_str;
            }
        }

        return $final_string;
    }

    /**
     * Processes the numbers from 0 to 999
     * @param $number the number which is going to be converted into literal
     * equivalence
     * @return string a string of word(s) representing the literal equivalence
     * of the given $number
     */
    function processHundreds($number)
    {
        $initial_length = strlen((string)$number);
        $number = $this->removePrecedingZeros($number);
        if($this->ifNeedsRelayingTheProcessor($initial_length, 3, 3))
        {
            return $this->relayToCorrectProcessor($number);
        }

        $final_string = "";
        $number_category = $this->number_category;
        $number_str_array = (string)$number;
        if( preg_match("/00$/", $number_str_array) )
        {
            $final_string = $number_category[(int)$number];
        }
        else
        {
            $main_num_str  = $number_category[(int)$number_str_array[0]*100];
            $number_str_array = preg_replace("/^[0-9]{1}/", "", $number_str_array);
            $main_num_str .= $this->languageUtiliy->getConjunction($initial_length);
            $main_num_str .= $this->relayToCorrectProcessor((int)$number_str_array);
            $final_string = $main_num_str;
        }

        return $final_string;
    }

    /**
     * The main processor. It parses the integers into a fully qualified
     * literal equivalence. This processors hands down numbers of smaller
     * than thousand to other two processors, nevertheless it is still
     * the main entry point and should be used in any case.
     * @param $number the number which is going to be converted into literal
     * equivalence
     * @return string a string of word(s) representing the literal equivalence
     * of the given $number
     */
    function runProcessor($number)
    {
        $initial_length = strlen((string)$number);
        $number = $this->removePrecedingZeros($number);

        $final_string = "";
        $number_str_array = (string)$number;
        $general_category = $this->getGeneralCategory($this->getGeneralBase($number));
        $root_length = $this->getTheRootNumbers($number);
        $extracted = substr($number, 0, $root_length);
        $final_string .= $this->relayToCorrectProcessor($extracted);
        $final_string .= " ".$general_category;
        $remainder = preg_replace("/^{$extracted}/", "", $number_str_array);

        $remainder_string = $this->relayToCorrectProcessor($remainder);
        if(!empty($remainder_string))
        {
            $remainder_string = $this->languageUtiliy->getConjunction($initial_length) . $remainder_string;
        }
        $final_string = $final_string . $remainder_string;

        return $final_string;
    }

    /**
     * Sets the current language and updates the variables to hold
     * the equivalences of current language.
     * @param $language
     * @return $this
     */
    function setCurrentLanguage($language)
    {
        $this->languageUtiliy->setCurrentLanguage($language);
        $this->reAssignLanguages();
        return $this;
    }
}