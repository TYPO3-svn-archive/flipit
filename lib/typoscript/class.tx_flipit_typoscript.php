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
* Class provides methods for the extension manager.
*
* @author    Dirk Wildt <http://wildt.at.die-netzmacher.de>
* @package    TYPO3
* @subpackage    flipit
* @version  0.0.1
* @since    0.0.1
*/


  /**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   49: class tx_flipit_typoscript
 *   67:     function promptCheckUpdate()
 *  102:     function promptCurrIP()
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
class tx_flipit_typoscript
{
  
 /**
  * Extension key
  *
  * @var string
  */
  private $extKey = 'flipit';

 /**
  * Prefix id
  *
  * @var string
  */
  public $prefixId = 'tx_flipit_typoscript';
 
 /**
  * Configuration by the extension manager
  *
  * @var string
  */
  public $scriptRelPath = 'lib/typoscript/class.tx_flipit_typoscript.php';

  
  
 /**
  * renderFlipit( ): 
  *
  * @param	string		Content input. Not used, ignore.
  * @param	array		TypoScript configuration
  * @return	mixed		HTML output.
  * @access public
  * @version 0.0.1
  * @since 0.0.1
  */
  public function renderFlipit( $content, $conf )
  {

      // Init
    $arr_return = $this->init( $conf );

      // IF return  : return with an error prompt
    if( $arr_return['return'] )
    {
      return $arr_return['content'];
    }
      // IF return  : return with an error prompt

      // Content
    return $this->content( $conf );

    return '<p>' . var_export( $this->cObj->data, true ) . ' </p>';
    
  }

  
  
 /**
  * init( ): 
  *
  * @param	array		TypoScript configuration
  * @return	mixed		HTML output.
  * @access public
  * @version 0.0.1
  * @since 0.0.1
  */
  private function init( $conf )
  {

      // Enable the DRS by TypoScript
    if( ! empty ( $conf['userFunc.']['drs'] ) )
    {
      $this->helper_init_drs( );
    }
      // Enable the DRS by TypoScript
    
      // RETURN : Flip it! is disabled or there is an error
    $arr_return = $this->initEnable( $conf );
    if( $arr_return['return'] )
    {
      return $arr_return;
    }
      // RETURN : Flip it! is disabled or there is an error

    return;
  }

  
  
 /**
  * initEnable( ): 
  *
  * @param	array		TypoScript configuration
  * @return	mixed		HTML output.
  * @access public
  * @version 0.0.1
  * @since 0.0.1
  */
  private function initEnable( $conf )
  {

    $arr_return = array( );
    $arr_return['return'] = false;
    
    $coa_name = $conf['userFunc.']['enabled'];
    $coa_conf = $conf['userFunc.']['enabled.'];
    $enabled  = $this->cObj->cObjGetSingle( $coa_name, $coa_conf );
    
    switch( $enabled )
    {
      case( 'enabled' ):
        if( $this->b_drs_flipit )
        {
          $prompt = 'Flip it! is enabled.';
          t3lib_div::devlog( '[INFO/DRS] ' . $prompt, $this->extKey, 0 );
        }
        $arr_return['return'] = false;
        break;
      case( 'disabled' ):
        if( $this->b_drs_flipit )
        {
          $prompt = 'Flip it! is disabled.';
          t3lib_div::devlog( '[INFO/DRS] ' . $prompt, $this->extKey, 0 );
        }
        $arr_return['return'] = true;
        break;
      case( 'error' ):
      default:
        if( $this->b_drs_flipit )
        {
          $prompt = 'The enabling mode of Flip it! isn\'t part of the list: disabled,enabled,ts';
          t3lib_div::devlog( '[ERROR/DRS] ' . $prompt, $this->extKey, 3 );
          $prompt = 'Flip it! won\'t run!';
          t3lib_div::devlog( '[WARN/DRS] ' . $prompt, $this->extKey, 3 );
        }
        $arr_return['return']   = true;
        $arr_return['content']  = $enabled;
        break;
    }

    return $arr_return;
    
  }

  
  
 /**
  * content( ): 
  *
  * @param	array		TypoScript configuration
  * @return	mixed		HTML output.
  * @access public
  * @version 0.0.1
  * @since 0.0.1
  */
  private function content( $conf )
  {

    $coa_name = $conf['userFunc.']['content'];
    $coa_conf = $conf['userFunc.']['content.'];
    $content  = $this->cObj->cObjGetSingle( $coa_name, $coa_conf );
    
    if( $this->b_drs_flipit )
    {
      switch( $content )
      {
        case( false ):
          $prompt = 'Flip it! is delivered without content.';
          t3lib_div::devlog( '[WARN/DRS] ' . $prompt, $this->extKey, 2 );
          break;
        case( true ):
        default:
          $prompt = 'Flip it! is delivered with content.';
          t3lib_div::devlog( '[OK/DRS] ' . $prompt, $this->extKey, -1 );
          break;
      }
    }

    return $content;
    
  }
  
  
  
/**
 * helper_init_drs( ): Init the DRS - Development Reportinmg System
 *
 * @return	void
 * @access private
 */
  private function helper_init_drs( )
  {
    $this->b_drs_error          = true;
    $this->b_drs_warn           = true;
    $this->b_drs_info           = true;
    $this->b_drs_flipit         = true;
    $prompt_01 = 'The DRS - Development Reporting System is enabled by TypoScript.';
    $prompt_02 = 'Change it: Please look for userFunc = tx_flipit_typoscript->renderFlipit and for userFunc.drs.';
    t3lib_div::devlog( '[INFO/DRS] ' . $prompt_01, $this->extKey, 0 );
    t3lib_div::devlog( '[HELP/DRS] ' . $prompt_02, $this->extKey, 1 );
  }



}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/flipit/lib/class.tx_flipit_typoscript.php'])
{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/flipit/lib/class.tx_flipit_typoscript.php']);
}

?>
