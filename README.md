# Larabits
A place for helpful collection of Laravel bits and bobs to reside.

##Eloquent
###AttributeEncryption

Add attribute level encryption to your Eloquent models in just a few steps:
- Import `Larabits\Eloquent\AttributeEncryption` at the top of your class
- Add an `$encrypt` array property and populate it with attributes you wish to encrypt
- Add `use AttributeEncryption` to your Eloquent models

Just like this:
```php
<?php

use Illuminate\Database\Eloquent\Model;
use Larabits\Eloquent\AttributeEncryption;

class User extends Model
{
	use AttributeEncryption;
  
	/**
	 * The attributes that should be encrypted.
	 *
	 * @var array
	 */
	protected $encrypt = [
		'email',
		'secret',
	];
  
}
```

If required, encryption can be enabled and disabled by using your `.env` file to set an `APP_ENABLE_ENCRYPTION` constant `false`. This is sometimes useful for testing, for example when using `seeInDatabase('table',['foo' => 'bar']);`.

## Installing

To install Larabits, either add it your `composer.json` or do a `composer require jivemonkey2000/larabits` from your project root.
```
composer require jivemonkey2000/larabits
