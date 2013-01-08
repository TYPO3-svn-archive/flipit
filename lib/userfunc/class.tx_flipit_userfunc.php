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
  * Status of operating system: linux, unix, windows, unsupported, undefined
  *
  * @var string
  */
  public $os = null;

 /**
  * Status of SWFTOOLS
  *
  * @var string
  */
  public $swfTools = null;
  
 /**
  * Version of TYPO3 (sample: 4.7.7 -> 4007007)
  *
  * @var string
  */
  public $typo3Version = null;



/**
 * Constructor. The method initiate the parent object
 *
 * @param	object		The parent object
 * @return	void
 */
  function __construct( $pObj )
  {
    $this->pObj = $pObj;
  }



//  /**
//   * pageWizard( ): Builds an input form that also includes the link popup wizard.
//   * @param		array		Parameter array.  Contains fieldName and fieldValue.
//   * @return		string		HTML output for form widget.
//   * @version 0.0.1
//   * @since   0.0.1
//   */
//  public function pageWizard( $params ) 
//  {
//    /* Pull the current fieldname and value from constants */
//    $fieldName  = $params['fieldName'];
//    $fieldValue = $params['fieldValue'];
//    
//    $input = '<input style="margin-right: 3px;" name="'. $fieldName .'" value="'. $fieldValue .'" />';
//
//    /* @todo 	Don't hardcode the inclusion of the wizard this way.  Use more backend APIs. */
//    $wizard = '<a href="#" onclick="this.blur(); vHWin=window.open(\'../../../../typo3/browse_links.php?mode=wizard&amp;P[field]='. $fieldName .'&amp;P[formName]=editForm&amp;P[itemName]='. $fieldName .'&amp;P[fieldChangeFunc][typo3form.fieldGet]=null&amp;P[fieldChangeFunc][TBE_EDITOR_fieldChanged]=null\',\'popUpID478be36b64\',\'height=300,width=500,status=0,menubar=0,scrollbars=1\'); vHWin.focus(); return false;"><img src="../../../../typo3/sysext/t3skin/icons/gfx/link_popup.gif" width="16" height="15" border="0" alt="Link" title="Link" /></a>';
//
//    return $input.$wizard;
//  }

  
  
  /**
   * promptEvaluatorDetectionBug(): Displays the quick start message.
   *
   * @return  string    message wrapped in HTML
   * @version 0.0.1
   * @since   0.0.1
   */
  public function promptEvaluatorDetectionBug()
  {
//.message-notice
//.message-information
//.message-ok
//.message-warning
//.message-error

    $prompt = $prompt.'
<div class="typo3-message message-notice">
  <div class="message-body">
    ' . $GLOBALS['LANG']->sL('LLL:EXT:flipit/lib/locallang.xml:promptEvaluatorDetectionBug'). '
  </div> 
</div>';

    return $prompt;
  }

  
  
  /**
   * promptEvaluatorOS(): Displays the quick start message.
   *
   * @return  string    message wrapped in HTML
   * @version 0.0.1
   * @since   0.0.1
   */
  public function promptEvaluatorOS()
  {
//.message-notice
//.message-information
//.message-ok
//.message-warning
//.message-error

    $this->set_os( );
    
    $prompt = null;

    switch( $this->os )
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
  public function promptEvaluatorSWFtools()
  {
//.message-notice
//.message-information
//.message-ok
//.message-warning
//.message-error

    $prompt = null;

    $arr_return = $this->set_swfTools( );
    
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
    
    switch( $this->swfTools )
    {
      case( 'installed' ):
        $prompt = $prompt.'
<div class="typo3-message message-ok">
  <div class="message-body">
    ' . $GLOBALS['LANG']->sL('LLL:EXT:flipit/lib/locallang.xml:promptEvaluatorSWFtoolsInstalled'). '<br />
    ' . $arr_return['data']['lines'][0] . '
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
        switch( $this->os )
        {
          case( 'linux'):
          case( 'unix'):
            $prompt = $prompt.'
<div class="typo3-message message-information">
  <div class="message-body">
    ' . $GLOBALS['LANG']->sL('LLL:EXT:flipit/lib/locallang.xml:promptEvaluatorSWFtools4Linux'). '
  </div> 
</div>';
            break;
          case( 'windows'):
            $prompt = $prompt.'
<div class="typo3-message message-information">
  <div class="message-body">
    ' . $GLOBALS['LANG']->sL('LLL:EXT:flipit/lib/locallang.xml:promptEvaluatorSWFtools4Windows'). '
  </div> 
</div>';
            break;
        }
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
  public function promptEvaluatorTYPO3version()
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
  public function promptExternalLinks()
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
 * set_os( ): 
 *
 * @return  void
 * @version 0.0.2
 * @since 0.0.2
 */
  private function set_os( )
  {
      // RETURN  : $this->os was set before
    if( ! ( $this->os === null ) )
    {
      return;
    }
      // RETURN  : $this->os was set before
    
      // SWITCH PHP_OS  : set os
    switch( true )
    {
      case( stristr( PHP_OS, 'amiga' ) ):
      case( stristr( PHP_OS, 'android' ) ):
      case( stristr( PHP_OS, 'chrome' ) ):
        $this->os = 'unsupported';
        break;
      case( stristr( PHP_OS, 'darwin' ) ):
      case( stristr( PHP_OS, 'iOS' ) ):
      case( stristr( PHP_OS, 'mac' ) ):
        $this->os = 'unsupported';
        break;
      case( stristr( PHP_OS, 'linux' ) ):
        $this->os = 'linux';
        break;
      case( stristr( PHP_OS, 'unix' ) ):
        $this->os = 'unix';
        break;
      case( stristr( PHP_OS, 'win' ) && ! stristr( PHP_OS, 'darwin' ) ):
        $this->os = 'windows';
        break;
      default:
        $this->os = 'undefined';
        break;
    }
      // SWITCH PHP_OS  : set os
  }
  
  
  
/**
 * set_swfTools( ): 
 *
 * @return  void
 * @version 0.0.2
 * @since 0.0.2
 */
  private function set_swfTools( )
  {
      // RETURN  : $this->os was set before
    if( ! ( $this->swfTools === null ) )
    {
      return;
    }
      // RETURN  : $this->os was set before

    $this->set_os( );
    
    switch( $this->os )
    {
      case( 'linux' ):
      case( 'unix' ):
          // 130109, dwildt, 1-
        //$arr_return = $this->zz_exec( '/usr/local/bin/pdf2swf --version' );
          // 130109, dwildt, 1+
        $arr_return = $this->zz_exec( 'pdf2swf --version' );
        break;
      case( 'windows' ):
          // 130109, dwildt, 3-
//        $arr_return['error']['status'] = true;
//        $arr_return['error']['prompt'] = 
//          $GLOBALS['LANG']->sL('LLL:EXT:flipit/lib/locallang.xml:promptEvaluatorSWFtoolsWindowsError');
          // 130109, dwildt, 1+
        $arr_return = $this->zz_exec( 'pdf2swf --version' );
        break;
      default:
        break;
      
    }
    
    if( $arr_return['error']['status'] )
    {
      return $arr_return;
    }

    $lines = $arr_return['data']['lines'];
    
    if( empty ( $lines ) )
    {
      $this->swfTools = 'notInstalled';
      return $arr_return;
    }
      
    $this->swfTools = 'installed';
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
 * set_allParams( ): 
 *
 * @return  void
 * @version 0.0.2
 * @since   0.0.2
 */
  public function set_allParams( )
  {
    $this->set_TYPO3Version( );
    $this->set_swfTools( );     // set_swfTools sets os too
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
    $lines      = null;
    
      // RETURN : function exec doesn't exist
    if( ! ( function_exists('exec') ) )  
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
