<?php

namespace Marcohern\Slugifier\Lib;

class SlugFormatter {

  protected $format;
  protected $formatIfZero;

  public function __construct(string $format='%slug-%n', string $formatIfZero='%slug') {
    $this->format = $format;
    $this->formatIfZero = $formatIfZero;
  }

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

  public function slugFormat(string $slug, string $format, array &$fields) : string {
    $result = $format;
    foreach ($fields as $name => $value) {
      $result = str_replace("%$name", $value, $result);
    }
    $result = str_replace('%slug', $slug, $result);
    return $result;
  }
}