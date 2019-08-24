<?php

namespace Marcohern\Slugifier\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Marcohern\Slugifier\Lib\SlugFormatter;

class SlugFormatterTest extends TestCase
{

  protected function setUp(): void {
    parent::setUp();
  }

  protected function tearDown(): void {
    parent::tearDown();
  }

  public function test_roman() {
    $formatter = new SlugFormatter();
    $this->assertSame("viii", $formatter->roman(8));
    $this->assertSame("mcmlxxx", $formatter->roman(1980));
    $this->assertSame("mcmxcviii", $formatter->roman(1998));
  }

  public function test_letters() {
    $formatter = new SlugFormatter();
    $this->assertSame("z", $formatter->letters(0));
    $this->assertSame("a", $formatter->letters(1));
    $this->assertSame("b", $formatter->letters(2));
    $this->assertSame("x", $formatter->letters(24));
    $this->assertSame("y", $formatter->letters(25));
    $this->assertSame("az", $formatter->letters(26));
    $this->assertSame("aa", $formatter->letters(27));
    $this->assertSame("ab", $formatter->letters(28));

    $this->assertSame("bxv", $formatter->letters(1998));
    $this->assertSame("bxw", $formatter->letters(1999));
    $this->assertSame("bxx", $formatter->letters(2000));
    $this->assertSame("bxy", $formatter->letters(2001));
    $this->assertSame("byz", $formatter->letters(2002));
    $this->assertSame("bya", $formatter->letters(2003));
  }

  public function test_format() {
    $formatter = new SlugFormatter();
    $this->assertSame("tom-cruise", $formatter->format("tom-cruise",0));
    $this->assertSame("tom-cruise-42", $formatter->format("tom-cruise",42));

    $formatter = new SlugFormatter("%slug-%n");
    $this->assertSame("tom-cruise", $formatter->format("tom-cruise",0));
    $this->assertSame("tom-cruise-42", $formatter->format("tom-cruise",42));

    $formatter = new SlugFormatter("%slug-%i");
    $this->assertSame("tom-cruise", $formatter->format("tom-cruise",0));
    $this->assertSame("tom-cruise-xlii", $formatter->format("tom-cruise",42));

    $formatter = new SlugFormatter("%slug-%a");
    $this->assertSame("tom-cruise", $formatter->format("tom-cruise",0));
    $this->assertSame("tom-cruise-ap", $formatter->format("tom-cruise",42));
  }
}
