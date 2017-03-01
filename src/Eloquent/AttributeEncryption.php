<?php

namespace Larabits\Eloquent;

trait AttributeEncryption
{
	/**
	 * Called when attribute accessed by model.
	 *
	 * @param $attribute
	 * @return string
	 */
	public function getAttribute($attribute)
	{
        if ($this->shouldEncrypt($attribute)
            && !empty($this->attributes[$attribute])) {
			// As this attribute is encrypted, return decrypted version
			return decrypt($this->getAttributeValue($attribute));
		} else {
			// Let Laravel handle the request.
			return parent::getAttribute($attribute);
		}
	}

	/**
	 * Called when attribute being written to by model.
	 *
	 * @param $attribute
	 * @param $value
	 * @return mixed
	 */
	public function setAttribute($attribute, $value)
	{
		if ($this->shouldEncrypt($attribute)) {
			// As this attribute is encrypted, write encrypted version
			$this->attributes[$attribute] = encrypt($value);
		} else {
			// Let Laravel handle the request.
			return parent::setAttribute($attribute,$value);
		}
	}

	/**
	 * Determine whether given attribute should be encrypted.
	 *
	 * @param $attribute
	 * @return bool
	 */
	private function shouldEncrypt($attribute)
	{
		return in_array($attribute,$this->encrypt,true) && env('APP_ENABLE_ENCRYPTION',true);
	}
}
