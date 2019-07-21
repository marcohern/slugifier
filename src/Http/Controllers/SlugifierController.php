<?php

namespace Marcohern\Slugifier\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Marcohern\Slugifier\Slug;

class SlugifierController extends Controller
{
  protected function roman(int $integer) {
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

  protected function letters(int $integer) {
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
  
  protected function format(string $slug, string $n, string $format) {
    $result = $format;
    $roman = $this->roman($n);
    $letters = $this->letters($n);
    $result = str_replace('%slug', $slug, $result);
    $result = str_replace('%n', $n, $result);
    $result = str_replace('%i', $roman, $result);
    $result = str_replace('%a', $letters, $result);
    return $result;
  }

  public function index() {
    return Slug::all();
  }

  public function check(Request $request, string $entity, string $slug) {
    $dbslug = Slug::select()
      ->where('entity','=', $entity)
      ->where('slug','=', $slug)
      ->first();
    $format = $request->has('format') ? $request->format:'%slug%n';
    $rslug = $slug;
    if (!$dbslug) $sequence = 0;
    else {
      $sequence = $dbslug->sequence;
    }
    $rslug = $this->format($slug,$sequence,$format);
    return [
      'slug' => $rslug,
      'sequence' => $sequence
    ];
  }

  public function check_global(string $slug) {
    $dbslug = Slug::select()
      ->where('entity','=', '')
      ->where('slug','=', $slug)
      ->first();
    
    $rslug = $slug;
    if (!$dbslug) $sequence = 0;
    else {
      $sequence = $dbslug->sequence;
      $rslug = "$rslug$sequence";
    }
    return [
      'slug' => $rslug,
      'sequence' => $sequence
    ];
  }

  public function store(Request $request, string $entity, string $slug) {
    $dbslug = Slug::select()
      ->where('entity','=', $entity)
      ->where('slug','=', $slug)
      ->first();
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
    return ['sequence' => $sequence];
  }

  public function store_global(Request $request, string $slug) {
    $dbslug = Slug::select()
      ->where('entity','=', '')
      ->where('slug','=', $slug)
      ->first();
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
    return ['sequence' => $sequence];
  }
}
