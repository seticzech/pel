<?php
/**
 * --------------------------------
 * PEL - Phalcon Extensions Library
 * --------------------------------
 *
 * This code is distributed under New BSD license.
 * License is bundled with this package in file LICENSE.txt.
 *
 * @author Jiri Pazdernik <jiri@pazdernik.net>
 */

namespace Pel\Forms\Element;

use \Pel\Tag;

/**
 * Class for submit
 *
 * @author Jiri Pazdernik <jiri@pazdernik.net>
 */
class Submit extends \Phalcon\Forms\Element\Submit
{
	
	/**
	 * Renders the element widget
	 * 
	 * @param array $attributes
	 * @return string
	 */
	public function render($attributes = null)
	{
		return Tag::submitButton($this->prepareAttributes($attributes));
	}
	
	
}
