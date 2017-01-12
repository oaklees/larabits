# Larabits
A place for helpful collection of Laravel bits and bobs to reside.

##Eloquent
###AttributeEncryption

Simply import `Larabits\Eloquent\AttributeEncryption`, populate an `$encrypt[]` property, and `use` the trait on your Eloquent models. 
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

Encryption can be enabled and disabled by using your `.env` to set an `APP_ENABLE_ENCRYPTION` constant. 
