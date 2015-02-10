<?php

/**

 *
 * @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
 * @license			GNU/GPLv3 */
defined( '_JEXEC' ) or die( "Go away." );

jimport( 'joomla.application.component.controller' );

class DigiComAdminControllerEmailreminders extends DigiComAdminController
{

	var $_model = null;

	function __construct()
	{

		parent::__construct();
		$this->registerTask( "add", "edit" );
		$this->registerTask( "apply", "save" );
		$this->registerTask( "list", "listEmailreminders" );
		$this->registerTask( "", "listEmailreminders" );
		$this->registerTask( "orderup", "shiftorder" );
		$this->registerTask( "orderdown", "shiftorder" );
		$this->registerTask( "unpublish", "publish" );

		$this->_model = $this->getModel( "Emailreminder" );

	}

	function listEmailreminders()
	{

		$view = $this->getView( "Emailreminders", "html" );
		$view->setModel( $this->_model, true );

		$model = $this->getModel( "Config" );
		$view->setModel( $model );

		$view->display();

	}

	function save()
	{
		if($this->_model->store()){
			$msg = JText::_( 'EMAILREMINDERSAVED' );
		}else{
			$msg = JText::_( 'EMAILREMINDERSAVEFAILED' );
		}
		
		if(JRequest::getVar('task','') == 'save'){
			$link = "index.php?option=com_digicom&controller=emailreminders";
		}else{
			$email_id = JRequest::getVar('id','');
			$link = "index.php?option=com_digicom&controller=emailreminders&task=edit&cid[]=" . $email_id;
		}

		$this->setRedirect( $link, $msg );

	}

	function remove()
	{
		if ( !$this->_model->delete() ) {
			$msg = JText::_( 'LICREMERR' );
		} else {
			$msg = JText::_( 'LICREMSUCC' );
		}

		$link = "index.php?option=com_digicom&controller=emailreminders";
		$this->setRedirect( $link, $msg );

	}

	function cancel()
	{
		$msg = JText::_( 'LICCANCELED' );
		$link = "index.php?option=com_digicom&controller=emailreminders";
		$this->setRedirect( $link, $msg );

	}

	function publish()
	{
		$res = $this->_model->publish();
		if ( !$res ) {
			$msg = JText::_( 'EMAILREMBLOCKERR' );
		} elseif ( $res == -1 ) {
			$msg = JText::_( 'EMAILREMUNPUB' );
		} elseif ( $res == 1 ) {
			$msg = JText::_( 'EMAILREMPUB' );
		} else {
			$msg = JText::_( 'EMAILREMUNSPEC' );
		}
		$link = "index.php?option=com_digicom&controller=emailreminders";
		$this->setRedirect( $link, $msg );

	}

	function edit()
	{

		JRequest::setVar( "hidemainmenu", 1 );
		$view = $this->getView( "Emailreminders", "html" );

		$view->setLayout( "editForm" );
		$view->setModel( $this->_model, true );

		$model = $this->getModel( "Config" );
		$view->setModel( $model );

		$view->editForm();

	}

	function saveorder()
	{
		$res = $this->_model->saveorder();

		if ( !$res ) {
			$msg = JText::_( 'ERROR' );
		} else {
			$msg = JText::_( 'SUCCESS' );
		}
		$link = "index.php?option=com_digicom&controller=emailreminders";
		$this->setRedirect( $link, $msg );

	}

	function shiftorder()
	{
		$task = JRequest::getVar( "task", "orderup", "request" );
		$direct = ($task == "orderup") ? (-1) : (1);
		/* */
		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		$res = $this->_model->orderField( $cid[0], $direct );
		/* */
//		$res = $this->_model->shiftorder($direct);

		if ( !$res ) {
			$msg = JText::_( 'ERROR' );
		} else {
			$msg = JText::_( 'SUCCESS' );
		}
		$link = "index.php?option=com_digicom&controller=emailreminders";
		$this->setRedirect( $link, $msg );
	}

	function duplicate() {

		$cids = JRequest::getVar( 'cid', array(0), '', 'array' );

		foreach($cids as $cid) {
			$this->_model->duplicate( $cid );
		}

		$link = "index.php?option=com_digicom&controller=emailreminders";
		$this->setRedirect( $link, 'Dublicate OK' );
	}

}


?>