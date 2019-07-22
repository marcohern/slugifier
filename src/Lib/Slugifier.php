<?php

namespace Marcohern\Slugifier\Lib;

use Marcohern\Slugifier\Slug;

class Slugifier {
  public function roman(int $integer) {
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

  public function letters(int $integer) {
    $result = '';
    $lookup = array_combine(range(1,26), range('a', 'z'));
    $exp = 0;
    while ($integer > 0) {
      $pow = pow(26, $exp);
      $num = intval($integer/$pow)*$pow;
      $result = $lookup[$num].$result;
      $integer -= $num;
    }
    
    return $result;
  }

  public function format(string $slug, string $n, string $format, string $formatIfZero) {
    $result = ($n==0) ? $formatIfZero : $format;
    $roman = $this->roman($n);
    $letters = $this->letters($n);
    $result = str_replace('%slug', $slug, $result);
    $result = str_replace('%n', $n, $result);
    $result = str_replace('%i', $roman, $result);
    $result = str_replace('%a', $letters, $result);
    return $result;
  }

  public function slugFormat($slug, $format, &$fields=[]) {
    $result = $format;
    foreach ($fields as $name => $value) {
      $result = str_replace("%$name", $value, $result);
    }
    $result = str_replace('%slug', $slug, $result);
    return $result;
  }

  public function slugify(string $target, string $sep = '-') : string {
    return str_slug($target,$sep);
  }

  public function check($slug, $entity='', $format='%slug-%n',$formatIfZero='%slug') {
    $dbslug = Slug::select()->where('entity','=', $entity)->where('slug','=', $slug)->first();
    if (!$dbslug) $sequence = 0;
    else $sequence = $dbslug->sequence;
    $rslug = $this->format($slug, $sequence, $format, $formatIfZero);
    return [
      'slug' => $rslug,
      'sequence' => $sequence
    ];
  }

  public function store($slug, $entity='', $format='%slug-%n',$formatIfZero='%slug') {
    $dbslug = Slug::select()->where('entity','=', $entity)->where('slug','=', $slug)->first();
    $sequence = 0;
    if ($dbslug) {
      $sequence = $dbslug->sequence;
      $dbslug->sequence++;
    } else {
      $dbslug = new Slug;
      $dbslug->entity = $entity;
      $dbslug->slug = $slug;
      $dbslug->sequence = $sequence;
    }
    $dbslug->save();

    $rslug = $this->format($slug, $sequence, $format, $formatIfZero);
    return [
      'slug' => $rslug,
      'sequence' => $sequence
    ];
  }

  public function storeWithContext(
    $slug, $entity='', 
    $slugFormats=[], $fields=[],
    $format='%slug-%n',$formatIfZero='%slug') {
    
    $rslug = null;
    $sequence = 0;
    foreach ($slugFormats as $format) {
      $gslug = $this->slugFormat($slug, $format, $fields);
      $dbslug = Slug::select()->where('entity','=', $entity)->where('slug','=', $gslug)->first();
      if (!$dbslug) {
        $rslug = $gslug;
        break;
      }
    }
    return $rslug;
  }
}