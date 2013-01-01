<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012-2013 - Dirk Wildt <http://wildt.at.die-netzmacher.de>
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
* Class provides methods for the extension manager.
*
* @author    Dirk Wildt <http://wildt.at.die-netzmacher.de>
* @package    TYPO3
* @subpackage    flipit
* @version  0.0.2
* @since    0.0.1
*/


  /**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   49: class tx_flipit_userfunc
 *   67:     function promptCheckUpdate()
 *  102:     function promptCurrIP()
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
class tx_flipit_userfunc
{
  
 /**
  * Configuration by the extension manager
  *
  * @var array
  */
  private $arr_extConf;
  
 /**
  * Current IP is met allowed IPs
  *
  * @var boolean
  */
  private $bool_accessByIP;
  
 /**
  * Status of operating system: linux, unix, windows, unsupported, undefined
  *
  * @var string
  */
  private $osStatus = null;

 /**
  * Status of SWFTOOLS
  *
  * @var string
  */
  private $swfToolsStatus = null;
 /**
  * Version of TYPO3 (sample: 4.7.7 -> 4007007)
  *
  * @var string
  */
  private $typo3Version = null;







  /**
   * pageWizard( ): Builds an input form that also includes the link popup wizard.
   * @param		array		Parameter array.  Contains fieldName and fieldValue.
   * @return		string		HTML output for form widget.
   * @version 0.0.1
   * @since   0.0.1
   */
  function pageWizard( $params ) 
  {
    /* Pull the current fieldname and value from constants */
    $fieldName  = $params['fieldName'];
    $fieldValue = $params['fieldValue'];
    
    $input = '<input style="margin-right: 3px;" name="'. $fieldName .'" value="'. $fieldValue .'" />';

    /* @todo 	Don't hardcode the inclusion of the wizard this way.  Use more backend APIs. */
    $wizard = '<a href="#" onclick="this.blur(); vHWin=window.open(\'../../../../typo3/browse_links.php?mode=wizard&amp;P[field]='. $fieldName .'&amp;P[formName]=editForm&amp;P[itemName]='. $fieldName .'&amp;P[fieldChangeFunc][typo3form.fieldGet]=null&amp;P[fieldChangeFunc][TBE_EDITOR_fieldChanged]=null\',\'popUpID478be36b64\',\'height=300,width=500,status=0,menubar=0,scrollbars=1\'); vHWin.focus(); return false;"><img src="../../../../typo3/sysext/t3skin/icons/gfx/link_popup.gif" width="16" height="15" border="0" alt="Link" title="Link" /></a>';

    return $input.$wizard;
  }

  
  
  /**
   * promptEvaluatorOS(): Displays the quick start message.
   *
   * @return  string    message wrapped in HTML
   * @version 0.0.1
   * @since   0.0.1
   */
  function promptEvaluatorOS()
  {
//.message-notice
//.message-information
//.message-ok
//.message-warning
//.message-error

    $this->set_osStatus( );
    
    $prompt = null;

    switch( $this->osStatus )
    {
      case( 'linux' ):
      case( 'unix' ):
      case( 'windows' ):
        $prompt = $prompt.'
<div class="typo3-message message-ok">
  <div class="message-body">
    ' . $GLOBALS['LANG']->sL('LLL:EXT:flipit/lib/locallang.xml:promptEvaluatorOSsupported'). '
  </div> 
</div>';
        break;
      case( 'unsupported' ):
        $prompt = $prompt.'
<div class="typo3-message message-warning">
  <div class="message-body">
    ' . $GLOBALS['LANG']->sL('LLL:EXT:flipit/lib/locallang.xml:promptEvaluatorOSunsupported'). '
  </div> 
</div>';
        break;
      case( 'undefined' ):
      default:
        $prompt = $prompt.'
<div class="typo3-message message-warning">
  <div class="message-body">
    ' . $GLOBALS['LANG']->sL('LLL:EXT:flipit/lib/locallang.xml:promptEvaluatorOSundefined'). '
  </div> 
</div>';
        break;
    }
    $prompt = str_replace( '%OS%', PHP_OS, $prompt );  

    return $prompt;
  }

  
  
  /**
   * promptEvaluatorSWFtools(): Displays the quick start message.
   *
   * @return  string    message wrapped in HTML
   * @version 0.0.2
   * @since   0.0.1
   */
  function promptEvaluatorSWFtools()
  {
//.message-notice
//.message-information
//.message-ok
//.message-warning
//.message-error

    $prompt = null;

    $arr_return = $this->set_swfToolsStatus( );
    
    if( $arr_return['error']['status'] )
    {
      $prompt = $prompt.'
<div class="typo3-message message-error">
  <div class="message-body">
    ' . $arr_return['error']['prompt'] . '
  </div> 
</div>
<div class="typo3-message message-warning">
  <div class="message-body">
    ' . $GLOBALS['LANG']->sL('LLL:EXT:flipit/lib/locallang.xml:promptEvaluatorSWFtoolsBugfix'). '
  </div> 
</div>';
      return $prompt;
    }
    
    switch( $this->swfToolsStatus )
    {
      case( 'installed' ):
        $prompt = $prompt.'
<div class="typo3-message message-ok">
  <div class="message-body">
    ' . $GLOBALS['LANG']->sL('LLL:EXT:flipit/lib/locallang.xml:promptEvaluatorOSsupported'). '
  </div> 
</div>';
        break;
      case( 'notInstalled' ):
      default:
        $prompt = $prompt.'
<div class="typo3-message message-warning">
  <div class="message-body">
    ' . $GLOBALS['LANG']->sL('LLL:EXT:flipit/lib/locallang.xml:promptEvaluatorSWFtoolsNotInstalled'). '
  </div> 
</div>';
        break;
    }

    return $prompt;
  }

  
  
  /**
   * promptEvaluatorTYPO3version(): Displays the quick start message.
   *
   * @return  string    message wrapped in HTML
   * @version 0.0.1
   * @since   0.0.1
   */
  function promptEvaluatorTYPO3version()
  {
//.message-notice
//.message-information
//.message-ok
//.message-warning
//.message-error

    $prompt = null;

    $this->set_TYPO3Version( );
    
    switch( true )
    {
      case( $this->typo3Version < 4006000 ):
          // Smaller than 4.6
        $prompt = $prompt.'
          <div class="typo3-message message-warning">
            <div class="message-body">
              ' . $GLOBALS['LANG']->sL('LLL:EXT:flipit/lib/locallang.xml:promptEvaluatorTYPO3version4006000smaller'). '
            </div>
          </div>
          ';
        break;
      case( $this->typo3Version >= 4007000 ):
          // Greater than 4.6
        $prompt = $prompt.'
          <div class="typo3-message message-warning">
            <div class="message-body">
              ' . $GLOBALS['LANG']->sL('LLL:EXT:flipit/lib/locallang.xml:promptEvaluatorTYPO3version4006000greater'). '
            </div>
          </div>
          ';
        break;
      case( ( $this->typo3Version >= 4006000 ) && ( $this->typo3Version < 4007000 ) ):
      default:
          // Equal to 4.6
        $prompt = $prompt.'
          <div class="typo3-message message-ok">
            <div class="message-body">
              ' . $GLOBALS['LANG']->sL('LLL:EXT:flipit/lib/locallang.xml:promptEvaluatorTYPO3version4006000equal'). '
            </div>
          </div>
          <div class="typo3-message message-warning">
            <div class="message-body">
              ' . $GLOBALS['LANG']->sL('LLL:EXT:flipit/lib/locallang.xml:promptEvaluatorTYPO3version4006000equalTS'). '
            </div>
          </div>
          ';
        break;
    }
        
    return $prompt;
  }

  
  
  /**
   * promptExternalLinks(): Displays the quick start message.
   *
   * @return  string    message wrapped in HTML
   * @version 0.0.1
   * @since   0.0.1
   */
  function promptExternalLinks()
  {
//.message-notice
//.message-information
//.message-ok
//.message-warning
//.message-error

      $prompt = null;

      $prompt = $prompt.'
<div class="message-body">
  ' . $GLOBALS['LANG']->sL('LLL:EXT:flipit/lib/locallang.xml:promptExternalLinksBody') . '
</div>';

    return $prompt;
  }
  
  
  
/**
 * set_osStatus( ): 
 *
 * @return  void
 * @version 0.0.2
 * @since 0.0.2
 */
  private function set_osStatus( )
  {
      // RETURN  : $this->osStatus was set before
    if( ! ( $this->osStatus === null ) )
    {
      return;
    }
      // RETURN  : $this->osStatus was set before
    
    switch( true )
    {
      case( stristr( PHP_OS, 'amiga' ) ):
      case( stristr( PHP_OS, 'android' ) ):
      case( stristr( PHP_OS, 'chrome' ) ):
        $this->osStatus = 'unsupported';
        break;
      case( stristr( PHP_OS, 'darwin' ) ):
      case( stristr( PHP_OS, 'iOS' ) ):
      case( stristr( PHP_OS, 'mac' ) ):
        $this->osStatus = 'unsupported';
        break;
      case( stristr( PHP_OS, 'linux' ) ):
        $this->osStatus = 'linux';
        break;
      case( stristr( PHP_OS, 'unix' ) ):
        $this->osStatus = 'unix';
        break;
      case( stristr( PHP_OS, 'win' ) && ! stristr( PHP_OS, 'darwin' ) ):
        $this->osStatus = 'windows';
        break;
      default:
        $this->osStatus = 'undefined';
        break;
    }
  }
  
  
  
/**
 * set_swfToolsStatus( ): 
 *
 * @return  void
 * @version 0.0.2
 * @since 0.0.2
 */
  private function set_swfToolsStatus( )
  {
      // RETURN  : $this->osStatus was set before
    if( ! ( $this->swfToolsStatus === null ) )
    {
      return;
    }
      // RETURN  : $this->osStatus was set before

    switch( $this->osStatus )
    {
      case( 'linux' ):
      case( 'unix' ):
        $arr_return = $this->zz_exec( '/usr/local/bin/pdf2swf --version' );
        break;
      case( 'windows' ):
        $arr_return['error']['status'] = true;
        $arr_return['error']['prompt'] = 
          $GLOBALS['LANG']->sL('LLL:EXT:flipit/lib/locallang.xml:promptEvaluatorSWFtoolsWindowsError');
        break;
      default:
        break;
      
    }
    
    if( $arr_return['error']['status'] )
    {
      return $arr_return;
    }

    $last_line  = $arr_return['data']['last_line'];
    $retval     = $arr_return['data']['retval'];
    
    if( $retval == 0 )
    {
      $this->swfToolsStatus = 'notInstalled';
    }
      
    return $arr_return;
  }
  
  
  
/**
 * set_TYPO3Version( ): 
 *
 * @return  void
 * @version 0.0.1
 * @since 0.0.1
 */
  private function set_TYPO3Version( )
  {
      // RETURN : typo3Version is set
    if( $this->typo3Version !== null )
    {
      return;
    }
      // RETURN : typo3Version is set
    
      // Set TYPO3 version as integer (sample: 4.7.7 -> 4007007)
    list( $main, $sub, $bugfix ) = explode( '.', TYPO3_version );
    $version = ( ( int ) $main ) * 1000000;
    $version = $version + ( ( int ) $sub ) * 1000;
    $version = $version + ( ( int ) $bugfix ) * 1;
    $this->typo3Version = $version;
      // Set TYPO3 version as integer (sample: 4.7.7 -> 4007007)

    if( $this->typo3Version < 3000000 ) 
    {
      $prompt = '<h1>ERROR</h1>
        <h2>Unproper TYPO3 version</h2>
        <ul>
          <li>
            TYPO3 version is smaller than 3.0.0
          </li>
          <li>
            constant TYPO3_version: ' . TYPO3_version . '
          </li>
          <li>
            integer $this->typo3Version: ' . ( int ) $this->typo3Version . '
          </li>
        </ul>
          ';
      die ( $prompt );
    }
  }
  
  
  
/**
 * zz_exec( ): 
 *
 * @return  array
 * @version 0.0.2
 * @since 0.0.2
 */
  private function zz_exec( $exec )
  {
    $arr_return = null;
    
      // RETURN : function exec doesn't exist
    if( empty( function_exists('exec') ) )
    {
      $arr_return['error']['status'] = true;
      $arr_return['error']['prompt'] = 
        $GLOBALS['LANG']->sL('LLL:EXT:flipit/lib/locallang.xml:promptEvaluatorPhpExecIsFalse');
      return $arr_return;
    }
      // RETURN : function exec doesn't exist
    
    exec( $exec, $lines);
    $arr_return['data']['lines'] = $lines;
    
    return $arr_return;
  }



}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/flipit/lib/class.tx_flipit_userfunc.php'])
{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/flipit/lib/class.tx_flipit_userfunc.php']);
}

?>
