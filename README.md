# Larabits
A place for helpful collection of Laravel bits and bobs to reside.

## Eloquent - AttributeEncryption

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

## Broadcasts - ServerSideEvents

Allow your application to broadcast events directly to clients using the JavaScript `EventSource` API and the HTTP `event-stream`.
```javascript
// Instantiate the EventSource to listen on channel, e.g. 'default' 
var events = new EventSource('/broadcasts/default');

// Add listener for your specific Laravel events
events.addEventListener("App\\Events\\PostCreatedEvent", function(event) {
    
    // Access the event's payload, i.e. the public properties 
    // of the event class being broadcast.
    var payload = JSON.parse(event.data);
    var user = payload.user;
    var post = payload.post;
    
    // Use the broadcast data to drive your front end.
    console.log(user.id + " just created a new post titled " + post.title);
});
```

## Installing

To install Larabits, either add it your `composer.json` or do a `composer require jivemonkey2000/larabits` from your project root.
```
composer require jivemonkey2000/larabits
```
