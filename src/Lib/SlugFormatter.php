<?php

namespace Marcohern\Slugifier\Lib;

/**
 * Formats slugs
 */
class SlugFormatter {

  /**
   * Default Format
   */
  protected $format;

  /**
   * Format if the index is zero
   */
  protected $formatIfZero;

  /**
   * Constructor
   * @param string $format Default format
   * @param string $formatIfZero Format if the index is zero
   */
  public function __construct(string $format='%slug-%n', string $formatIfZero='%slug') {
    $this->format = $format;
    $this->formatIfZero = $formatIfZero;
  }

  /**
   * Set the Default Format
   * @param string $format Default format
   */
  public function setFormat(string $format) { $this->format = $format;}

  /**
   * Set the format for when index is zero
   * @param string $format Format
   */
  public function setFormatIfZero(string $format) { $this->formatIfZero = $format;}

  /**
   * Converts an integer to roman numerals
   * @param int $integer number to convert
   * @return string Roman Numerals
   */
  public function roman(int $integer) : string {
    $result = '';
    $lookup = [
      'm' => 1000, 'cm' => 900,
      'd' =>  500, 'cd' => 400,
      'c' =>  100, 'xc' =>  90,
      'l' =>   50, 'xl' =>  40,
      'x' =>   10, 'ix' =>   9,
      'v' =>    5, 'iv' =>   4,
      'i' =>    1
    ];

    foreach($lookup as $roman => $value){
      $matches = intval($integer/$value);
      $result .= str_repeat($roman,$matches);
      $integer = $integer % $value;
    }
    
    return $result;
  }

  /**
   * Returns a letter numerals. z is 0, a is 1, and so on.
   * @param int $integer number to convert
   * @return string letter numerals.
   */
  public function letters(int $integer) : string {
    $letters = "zabcdefghijklmnopqrstuvwxy";
    $len = strlen($letters);
    $exp=0;
    $result = '';
    if ($integer==0) return 'z';
    while ($integer > 0) {
      $index = $integer % $len;
      $result = $letters[$index].$result;
      $integer = intval($integer/$len);
    }
    return $result;
  }

  /**
   * Format the slug and index.
   * @param string $slug source slug
   * @param string $n index
   * @return string formatted string.
   */
  public function format(string $slug, string $n) : string {
    $result = ($n==0) ? $this->formatIfZero : $this->format;
    $roman = $this->roman($n);
    $letters = $this->letters($n);
    $result = str_replace('%slug', $slug, $result);
    $result = str_replace('%n', $n, $result);
    $result = str_replace('%i', $roman, $result);
    $result = str_replace('%a', $letters, $result);
    return $result;
  }

  /**
   * Generate a source slug.
   * @param string $slug source slug
   * @param string $format source slug format
   * @param array $fields fields to compliment format
   * @return string formatted source slug
   */
  public function slugFormat(string $slug, string $format, array &$fields) : string {
    $result = $format;
    foreach ($fields as $name => $value) {
      $result = str_replace("%$name", $value, $result);
    }
    $result = str_replace('%slug', $slug, $result);
    return $result;
  }
}