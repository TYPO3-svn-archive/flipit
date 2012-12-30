<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 - Dirk Wildt <http://wildt.at.die-netzmacher.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/



/**
 * 'Hooks' for the 'flipit' extension. Hook methods for foreign extensions liek felogin.
 *
 * @author    Dirk Wildt <http://wildt.at.die-netzmacher.de>
 * @package    TYPO3
 * @subpackage  flipit
 *
 * @version 0.0.1
 * @since 0.0.1
 */

/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   52: class tx_flipit_hooks
 *   72:     public function felogin_postProcContent( $params, &$pObj )
 *  104:     private function zz_replaceFormMarker( $content, $formsMarker )
 *  151:     private function zz_removeMarker( $content )
 *
 * TOTAL FUNCTIONS: 3
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */



class tx_flipit_hooks
{
    // [object] parent object
  private $pObj = null;
    // [array] TypoScript configuration of the parent object
  private $conf = null;

 /**
  * Extension key
  *
  * @var string
  */
  private $extKey = 'flipit';
  
 /**
  * Configuration of the extension manager
  *
  * @var array
  */
  private $arr_extConf = null;
  
  
 /**
  * DRS mode all
  *
  * @var boolean
  */
  private $drsAll   = false;
  
 /**
  * DRS mode hooks
  *
  * @var boolean
  */
  private $drsHooks = false;
  
 /**
  * DRS mode session
  *
  * @var boolean
  */
  private $drsSession = false;
  
 /**
  * DRS mode sql
  *
  * @var boolean
  */
  private $drsSql = false;


  
/**
 * processData( ): Handle the login form of the felogin extension
 *                             before sending. Replace and remove markers.
 *
 * @param    array        $params:  Given parameter
 * @param    array        &$pObj:   Reference to the parent object
 * @return    string        $content: The rendered content
 * @version  0.0.1
 * @since    0.0.1
 */
  public function processData( $action, $cmdArr, $result, &$pObj )
  {
    $this->init( );

    if( $this->drsHooks || 1 )
    {
      $prompt = __METHOD__ . ' is called.';
      t3lib_div::devlog( '[INFO/HOOKS] ' . $prompt, $this->extKey, 0 );
    }
    
      // Class var $pObj
    $this->pObj = $pObj;
var_dump( $params, $pObj ); 
exit;
      // Class var $conf
    $this->conf = $pObj->conf['extensions.']['flipit.'];

      // Current content
    $content = $params['content'];

      // Return the content
    return $content;
  }

  
  
 /**
  * init( )
  *
  * @return   void
  * @version  0.0.1
  * @since    0.0.1
  */
  private function init( )
  {
      // Set class var $arr_extConf, the extmanager configuration
    $this->arr_extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);
    
    $this->initDRS( );
  }



 /**
  * initDRS( ): Set the booleans for Warnings, Errors and DRS - Development Reporting System
  *
  * @return    void
  * @version 0.0.1
  * @since 0.0.1
  */
  private function initDRS( )
  {
    switch( $this->arr_extConf['debuggingDrs'] )
    {
      case( 'All' ):
      case( 'Enabled (for debugging only!)' ):
      case( 'Hooks' ):
        $this->drsAll     = true;
        $this->drsError   = true;
        $this->drsWarn    = true;
        $this->drsInfo    = true;
        $this->drsHooks   = true;
        t3lib_div::devlog( '[OK/DRS] DRS is enabled: ' . $this->arr_extConf['debuggingDrs'], $this->extKey, -1 );
        break;
      default:
          // do nothing;
        break;
    }
  }

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/browser/pi1/class.tx_flipit_hooks.php'])
{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/browser/pi1/class.tx_flipit_hooks.php']);
}

?>