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

namespace Pel\Mvc;

/**
 * Controller
 * 
 * @author Jiri Pazdernik <jiri@pazdernik.net>
 */
class Controller extends \Phalcon\Mvc\Controller
{
	
	/**
	 * Render specific template and return content
	 * 
	 * @param string $controllerName name of the controller
	 * @param string $template name of the template
	 * @return string
	 */
	public function renderTempate($controllerName, $template)
	{
		$view = clone $this->view;
		
		$view->start();
		$view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_ACTION_VIEW);
		$view->render($controllerName, $template);
		$view->finish();
		
		$content = $view->getContent();

		unset($view);
		
		return $content;
	}
	
}
