# marcohern/slugifier

Slug management for Laravel. *marcohern/slugifier* is not another slug generator (there are 
plenty of those), it manages slugs by keeping record of whether slugs are unique, and keep
a record of repeated slugs with an index.

## Installation

Install using composer:

```bash
$ composer require marcohern/slugifier
```
Run migrations to include the slugifier table.
```bash
$ php artisan migrate
```

Use the Slugifier management library.

```php
use Marcohern\Slugifier\Lib\Slugifier;
```

build the slugifier.
```php
$slugifier = new Slugifier();
```
you are all set to use the slugifier methods.

## slugify
It slugifies, by calling Laravel's standard **str_slug** helper function.
```php
echo $slugifier->slugify('Death Stranding');
//output: death-stranding
```

## check
Converts the source into a slug and verifies it's status in the database: if it being used, and it's sequence.

```php
$result = $slugifier->check('Death Stranding','Games');
/*
$result == [
  'entity' => 'games',
  'slug' => 'death-stranding',
  'sequence' => 0
]
*/
```

## store
Same as **check** except it also increments the sequience by 1. Calling it more than once causes 
the sequence to increase each time.

```php
$result = $slugifier->store('Death Stranding','Games');
/* first time
$result == [
  'entity' => 'games',
  'slug' => 'death-stranding',
  'sequence' => 0
]
*/

$result = $slugifier->store('Death Stranding','Games');
/* second time
$result == [
  'entity' => 'games',
  'slug' => 'death-stranding-1',
  'sequence' => 1
]
*/
```