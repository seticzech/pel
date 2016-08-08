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

/**
 * Class for group of checkboxes
 *
 * @author Jiri Pazdernik <jiri@pazdernik.net>
 */
class CheckGroup extends \Phalcon\Forms\Element implements \Phalcon\Forms\ElementInterface
{
	
	/**
	 * @var array
	 */
	protected $_checkboxes = array();
	
	/**
	 * Add checkbox element to the group
	 * 
	 * @param \Phalcon\Forms\Element\Check $element
	 */
	public function add($element)
	{
		if ($element instanceof \Phalcon\Forms\Element\Check) {
			if (null === $element->getForm()) {
				$form = $this->getForm();
				if (null !== $form) {
					$form->add($element);
				}
			}
			$this->_checkboxes[] = $element;
		}
	}
	
	/**
	 * Render HTML
	 * 
	 * @param array $attributes (OPTIONAL)
	 * @return string
	 */
	public function render($attributes = null)
	{
		$xhtml = "<div class=\"check-union\">";
		
		if (count($this->_checkboxes) > 0) {
			$lines = array();
			foreach ($this->_checkboxes as $element) {
				$line = "";
				$label = $element->getLabel();
				
				$line .= $element->render();
				if (! empty($label)) {
					$line .= "&nbsp;<label for=\"{$element->getName()}\">{$label}</label>";
				}
				
				$lines[] = $line;
			}
			
			$xhtml .= implode("<br />", $lines);
		}
		
		$xhtml .= "</div>";
		
		return $xhtml;
	}
	
}
