<?php defined('_JEXEC') or die;

// http://docs.joomla.org/Plugin/Events/System
// http://docs.joomla.org/J2.5:Creating_a_System_Plugin_to_augment_JRouter
// http://docs.joomla.org/J3.x:Creating_a_Plugin_for_Joomla
//~ 
//~ function pre($var) { return "<pre>".print_r($var,true)."</pre>"; }
//~ error_reporting(E_ALL&~E_NOTICE);
//~ ini_set('display_errors',1);

class PlgSystemDisableCiviCRMStyles extends JPlugin {

	protected $log;
		
	public function __construct(&$subject, $config) {
		
		parent::__construct($subject, $config);

		$this->log = $this->params->get('log', 0);
		if ($this->log) $this->addLogger();
	}

	public function onAfterDispatch() {

		$app = JFactory::getApplication(); 
		$user = JFactory::getUser();

		$option = $app->input->get('option');
		// $task = $app->input->get('task');
		// $view = $app->input->get('view');
		// $layout = $app->input->get('layout');

		if (
			!JComponentHelper::isInstalled('com_civicrm') || 
            !JComponentHelper::isEnabled('com_civicrm') ||
			$app->isAdmin() ||
			$option != 'com_civicrm'
		) return;

		if (!defined('CIVICRM_SETTINGS_PATH')) define('CIVICRM_SETTINGS_PATH', JPATH_ADMINISTRATOR . '/components/com_civicrm/civicrm.settings.php');
		if (!defined('CIVICRM_CORE_PATH')) define('CIVICRM_CORE_PATH', JPATH_ADMINISTRATOR . '/components/com_civicrm/civicrm/');
		require_once CIVICRM_SETTINGS_PATH;
		require_once CIVICRM_CORE_PATH .'CRM/Core/Config.php';
		$config = CRM_Core_Config::singleton();
		
		if (!class_exists('CRM_Core_Resources')){
			$this->log('CRM_Core_Resources not found');
			return;
		}

		$this->log('Disabled the CSS!');
		
		$civicrm_css = CRM_Core_Resources::singleton()->getUrl('civicrm', 'css/civicrm.css', TRUE); 
		CRM_Core_Region::instance('html-header')->update($civicrm_css, array('disabled' => TRUE));
	}

	protected function log($msg) {

		if (!$this->log) return;
		
		JLog::add($msg, JLog::DEBUG, 'DisableCiviCRMStyles');
	}

	protected function addLogger() {
		JLog::addLogger(
			array(
				 // Sets file name
				 'text_file' => 'DisableCiviCRMStyles.log.php'
			),
			// Sets messages of all log levels to be sent to the file
			JLog::ALL,
			// The log category/categories which should be recorded in this file
			// In this case, it's just the one category from our extension, still
			// we need to put it inside an array
			['DisableCiviCRMStyles']
		);
	}
}
