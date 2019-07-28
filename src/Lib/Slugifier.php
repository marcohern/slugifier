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
      $slugValue = $this->slugify($value);
      $result = str_replace("%$name", $slugValue, $result);
    }
    $result = str_replace('%slug', $slug, $result);
    return $result;
  }

  public function slugify(string $target, string $sep = '-') : string {
    return str_slug($target,$sep);
  }

  public function check(string $slug, string $entity='', string $format='%slug-%n', string $formatIfZero='%slug') {
    $slugRecord = Slug::select()->where('entity','=', $entity)->where('slug','=', $slug)->first();
    if (!$slugRecord) $sequence = 0;
    else $sequence = $slugRecord->sequence;
    $slugFormatted = $this->format($slug, $sequence, $format, $formatIfZero);
    return [
      'entity' => $entity,
      'slug' => $slugFormatted,
      'sequence' => $sequence
    ];
  }

  public function store(string $slug, string $entity='', string $format='%slug-%n', string $formatIfZero='%slug') {
    $slugRecord = Slug::select()->where('entity','=', $entity)->where('slug','=', $slug)->first();
    $sequence = 0;
    if ($slugRecord) {
      $sequence = $slugRecord->sequence;
      $slugRecord->sequence++;
    } else {
      $slugRecord = new Slug;
      $slugRecord->entity = $entity;
      $slugRecord->slug = $slug;
      $slugRecord->sequence = $sequence+1;
    }
    $slugRecord->save();

    $formattedSlug = $this->format($slug, $sequence, $format, $formatIfZero);
    return [
      'entity' => $entity,
      'slug' => $formattedSlug,
      'sequence' => $sequence
    ];
  }

  public function contextualize(
    string $slug, string $entity='', 
    array $slugFormats=[], array $fields=[],
    string $format='%slug-%n', string $formatIfZero='%slug') {
    
    $contextSlug = null;
    foreach ($slugFormats as $slugFormat) {
      $contextSlug = $this->slugFormat($slug, $slugFormat, $fields);
      $slugRecord = Slug::select()->where('entity','=', $entity)->where('slug','=', $contextSlug)->first();
      if (!$slugRecord) break;
    }
    return $this->store($contextSlug, $entity, $format, $formatIfZero);
  }
}