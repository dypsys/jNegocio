<?php
/**
* @version		$Id$
* @package		Joomla
* @subpackage	jNegocio
* @copyright	Copyright (C) 2005 - 2012 CESI Informàtica i comunicions. All rights reserved.
* @license		Comercial License
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Require the base controller
jFWBase::load( 'jFWControllerCRUD', 'controllers._crud' );

class jNegocioControllerZones extends jFWControllerCRUD
{
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
		$this->set('suffix', 'zones');
		
		$this->registerTask( 'selected_enable'	, 'selected_enabled' );
        $this->registerTask( 'selected_disable'	, 'selected_disabled' );	
	}
		
	/**
	 * Sets the model's default state based on value in the request
	 *
	 * @return unknown_type
	 */
	function _setModelState()
	{
		$state = parent::_setModelState();
		$app = JFactory::getApplication();
		$model = $this->getModel( $this->get('suffix') );
		$ns = $this->getNamespace();
	
		$state['filter_countryid'] 		= $app->getUserStateFromRequest( $ns.'.filter_countryid', 'filter_countryid', '', '');
		$state['filter_geozoneid'] 		= $app->getUserStateFromRequest( $ns.'.filter_geozoneid', 'filter_geozoneid', '', '');
	
		foreach (@$state as $key=>$value) {
			$model->setState( $key, $value );
		}
	
		return $state;
	}

	/**
     * 
     * @return unknown_type
     */
    function selected_enabled()
    {
		// Check for request forgeries
		JRequest::checkToken() or die( JText::_('JINVALID_TOKEN') );
		$tmpl	= JRequest::getCmd( 'tmpl' , 'index' );
		$layout	= JRequest::getCmd( 'layout' , 'default' );
		$cids 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$geozoneid 	= JRequest::getVar( 'filter_geozoneid', 0, 'post', 'int' );

		if (!is_array( $cids ) || count( $cids ) < 1) {
			JError::raiseError(500, JText::_( 'COM_JNEGOCIO_SELECT_ITEM_TO_PUBLISH' ) );
		}

		$error = false;
		JTable::addIncludePath( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_jnegocio' .DS. 'tables');
		if (count($cids)) {
    		foreach($cids as $id) {
    			$table = JTable::getInstance( 'geozonerelations', jFWBase::getTablePrefix());
    			$keynames = array();
    			$keynames["geozone_id"] = $geozoneid;
            	$keynames["zone_id"] = $id;
            	$table->load( $keynames );

            	if ( intval($table->geozonerelation_id)>=1) {
            		// Ja esta dado de alta
            	} else {
            		// $table->geozonerelation_id = 0;
            		$table->geozone_id = $geozoneid;
                    $table->zone_id = $id;
            		if (!$table->guardar()) {
						$this->message .= $cid.': '.$table->getError().'<br/>';
						$this->messagetype = 'notice';
                        $error = true;                      
                    }
            	}
    		}
		}
		
    	if ($error) {
            $this->message = JText::_('Error') . ": " . $this->message;
        } else {
            $this->message = "";
        }

        $redirect = 'index.php?option='.jFWBase::getComponentName().'&view='.$this->get('suffix').'&layout='.$layout;
		if ($tmpl) {
			$redirect .= '&tmpl='.$tmpl;
		}		
		$redirect = JRoute::_( $redirect, false ); 
		$this->setRedirect( $redirect, $this->message, $this->messagetype );
    }
    
	/**
     * 
     * @return unknown_type
     */
    function selected_disabled()
    {
		// Check for request forgeries
		JRequest::checkToken() or die( JText::_('JINVALID_TOKEN') );
		$tmpl	= JRequest::getCmd( 'tmpl' , 'index' );
		$layout	= JRequest::getCmd( 'layout' , 'default' );
		$cids 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$geozoneid 	= JRequest::getVar( 'filter_geozoneid', 0, 'post', 'int' );

		if (!is_array( $cids ) || count( $cids ) < 1) {
			JError::raiseError(500, JText::_( 'COM_JNEGOCIO_SELECT_ITEM_TO_PUBLISH' ) );
		}

		$error = false;
		JTable::addIncludePath( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_jnegocio' .DS. 'tables');
		if (count($cids)) {
    		foreach($cids as $id) {
    			$table = JTable::getInstance( 'geozonerelations', jFWBase::getTablePrefix());
    			$keynames = array();
    			$keynames["geozone_id"] = $geozoneid;
            	$keynames["zone_id"] = $id;
            	$table->load( $keynames );
            	
            	if ( intval($table->geozonerelation_id)>=1) {
            		// Ja esta dado de alta
            		if (!$table->delete()) {
						$this->message .= $cid.': '.$table->getError().'<br/>';
						$this->messagetype = 'notice';
                        $error = true;              			
            		}
            	} else {
            		// Ja NO esta en la tabla
            	}
    		}
		}
		
    	if ($error) {
            $this->message = JText::_('Error') . ": " . $this->message;
        } else {
            $this->message = "";
        }

        $redirect = 'index.php?option='.jFWBase::getComponentName().'&view='.$this->get('suffix').'&layout='.$layout;
		if ($tmpl) {
			$redirect .= '&tmpl='.$tmpl;
		}		
		$redirect = JRoute::_( $redirect, false ); 
		$this->setRedirect( $redirect, $this->message, $this->messagetype );
    }
}