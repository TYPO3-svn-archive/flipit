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
  * renderFlipit( ): The method enables to link to files of each language at the same time.
  *                   The method is based on $this->render_uploads_per_language( ). See below.
  *                   Conditions
  *                   * userFunc.renderCurrentLanguageOnly has to be true
  *                   * the table sys_language has to contain one record at least
  *
  * @param	string		Content input. Not used, ignore.
  * @param	array		TypoScript configuration
  * @return	string		HTML output.
  * @access public
  * @version 3.9.8
  * @since 3.9.3
  */
  public function renderFlipit( $content, $conf )
  {

      //////////////////////////////////////////////////////////////////////////
      //
      // Enable the DRS by TypoScript

    $bool_drs = false;
    if( isset( $conf['userFunc.']['drs'] ) )
    {
      $coa_name               = $conf['userFunc.']['drs'];
      $coa_conf_userFunc_drs  = $conf['userFunc.']['drs.'];
      $bool_drs               = intval( $this->cObj->cObjGetSingle( $coa_name, $coa_conf_userFunc_drs, $TSkey='__' ) );
    }
    if( $bool_drs )
    {
      $this->helper_init_drs( );
    }
      // Enable the DRS by TypoScript

    return '<h1>X</h1>';
    
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
    $prompt_01 = 'The DRS - Development Reporting System is enabled by TypoScript.';
    $prompt_02 = 'Change it: Please look for userFunc = tx_browser_cssstyledcontent->render_uploads and for userFunc.drs.';
    t3lib_div::devlog( '[INFO/DRS] ' . $prompt_01, $this->extKey, 0 );
    t3lib_div::devlog( '[HELP/DRS] ' . $prompt_02, $this->extKey, 1 );
  }



}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/flipit/lib/class.tx_flipit_typoscript.php'])
{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/flipit/lib/class.tx_flipit_typoscript.php']);
}

?>
