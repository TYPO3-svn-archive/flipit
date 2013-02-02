<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 - Dirk Wildt <http://wildt.at.die-netzmacher.de>
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
* Class provides methods for the flexform
*
* @author    Dirk Wildt <http://wildt.at.die-netzmacher.de>
* @package    TYPO3
* @subpackage    flipit
* @version  1.0.1
* @since    1.0.1
*/


  /**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   49: class tx_flipit_flexform
 *   67:     function promptCheckUpdate()
 *  102:     function promptCurrIP()
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
class tx_flipit_flexform
{
  
 /**
  * Extension key
  *
  * @var string
  */
  private $extKey = 'flipit';

 /**
  * Extension configuration
  *
  * @var array
  */
  private $arr_extConf = null;
  
 /**
  * Max width for prompts
  *
  * @var string
  */
  private $pid = null;

 /**
  * Max width for prompts
  *
  * @var string
  */
  private $maxWidth = '600px';



/**
 * Constructor. The method initiate the parent object
 *
 * @param    object        The parent object
 * @return    void
 */
  function __construct( $pObj )
  {
    $this->pObj = $pObj;
  }






/**
 * evaluate_plugin: Evaluates the plugin, flexform, TypoScript
 *                  Returns a HTML report
 *
 * Tab [evaluate]
 *
 * @param	array		$arr_pluginConf:  Current plugin/flexform configuration
 * @param	array		$obj_TCEform:     Current TCE form object
 * @return	string		$str_prompt: HTML prompt
 * @version 1.0.1
 * @since 1.0.1
 */
  public function evaluate( $arr_pluginConf )
  {
      // Require classes, init page id, page object and TypoScript object
    $bool_success = $this->init( $arr_pluginConf );

      // RETURN error with init()
    if(!$bool_success)
    {
      return $arr_pluginConf;
    }

      //.message-notice
      //.message-information
      //.message-ok
      //.message-warning
      //.message-error
    $str_prompt                       = null;
    $str_promptDrsEnabled             = null;
    $str_promptDrsDisabled            = null;
    $str_prompt_info_tutorialAndForum = null;
    $str_promptPdfOnly                = null;



      ///////////////////////////////////////////////////////////////////////////////
      //
      // General information

      // INFO: Link to the tutorial and to the Flip it! forum
    $str_prompt_info_tutorialAndForum = '
      <div class="typo3-message message-notice" style="max-width:' . $this->maxWidth . ';">
        <div class="message-body">
          ' . $GLOBALS['LANG']->sL('LLL:EXT:flipit/locallang_db.xml:sheetFlipit_evaluate_ok_tutorialAndForum') . '
        </div>
      </div>
      ';

    $str_promptPdfOnly = '
      <div class="typo3-message message-notice" style="max-width:' . $this->maxWidth . ';">
        <div class="message-body">
          ' . $GLOBALS['LANG']->sL('LLL:EXT:flipit/locallang_db.xml:sheetFlipit_evaluate_ok_pdfonly') . '
        </div>
      </div>
      ';

//    $str_prompt_inCaseOfAnError = '
//      <div class="typo3-message message-warning" style="max-width:' . $this->maxWidth . ';">
//        <div class="message-body">
//          ' . $GLOBALS['LANG']->sL('LLL:EXT:flipit/locallang_db.xml:sheetFlipit_evaluate_warn_fixThisBug') . '
//        </div>
//      </div>
//      ';

      // DRS
    $arr_extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['flipit']);
    if ($arr_extConf['debuggingDrs'] != 'Disabled')
    {
      $str_promptDrsEnabled = '
        <div class="typo3-message message-warning" style="max-width:' . $this->maxWidth . ';">
          <div class="message-body">
            ' . $GLOBALS['LANG']->sL('LLL:EXT:flipit/locallang_db.xml:sheetFlipit_evaluate_drs_enabled') . '
          </div>
        </div>
        ';
      $str_promptDrsEnabled = str_replace( '%status%', $arr_extConf['debuggingDrs'], $str_promptDrsEnabled );
    }
    else
    {
      $str_promptDrsDisabled = '
        <div class="typo3-message message-notice" style="max-width:' . $this->maxWidth . ';">
          <div class="message-body">
            ' . $GLOBALS['LANG']->sL('LLL:EXT:flipit/locallang_db.xml:sheetFlipit_evaluate_drs_disabled') . '
          </div>
        </div>
        ';
    }
      // DRS

      // General information



      ///////////////////////////////////////////////////////////////////////////////
      //
      // Check the plugin

//      // RETURN plugin isn't never saved
//    if( empty ( $arr_pluginConf['row']['pi_flexform'] ) )
//    {
//      $str_prompt = '
//        <div class="typo3-message message-error">
//          <div class="message-body">
//            ' . $GLOBALS['LANG']->sL('LLL:EXT:flipit/locallang_db.xml:sheetFlipit_evaluate_error_saved_never') . '
//          </div>
//        </div>
//        <div class="typo3-message message-information">
//          <div class="message-body">
//            ' . $GLOBALS['LANG']->sL('LLL:EXT:flipit/locallang_db.xml:sheetFlipit_evaluate_ok_saved_never') . '
//          </div>
//        </div>
//        ';
//      return $str_prompt . $str_prompt_inCaseOfAnError;
//    }
//      // RETURN plugin isn't never saved

      // RETURN TypoScript static template isn't included
    if( ! is_array ( $this->obj_TypoScript->setup['plugin.']['tx_flipit.'] ) )
    {
      $str_prompt = '
        <div class="typo3-message message-warning" style="max-width:' . $this->maxWidth . ';">
          <div class="message-body">
            ' . $GLOBALS['LANG']->sL('LLL:EXT:flipit/locallang_db.xml:sheetFlipit_evaluate_warn_no_ts_template') . '
          </div>
        </div>
        <div class="typo3-message message-information" style="max-width:' . $this->maxWidth . ';">
          <div class="message-body">
            ' . $GLOBALS['LANG']->sL('LLL:EXT:flipit/locallang_db.xml:sheetFlipit_evaluate_ok_no_ts_template') . '
          </div>
        </div>
        ';
      //return $str_prompt . $str_prompt_inCaseOfAnError . $str_prompt_info_tutorialAndForum;
    }
      // RETURN TypoScript static template isn't included

    if( $str_prompt || $str_promptDrsEnabled )
    {
      $str_prompt = $str_prompt . $str_promptDrsEnabled . $str_promptDrsDisabled;
      $str_prompt = $str_prompt . $this-> evaluate_promptSwftools( );
      $str_prompt = $str_prompt . $this-> evaluate_promptConstantEditor( );
      $str_prompt = $str_prompt . $str_prompt_info_tutorialAndForum;
      return $str_prompt;
    }
    
      // Evaluation result: default message in case of success
    $str_prompt = '
      <div class="typo3-message message-ok" style="max-width:' . $this->maxWidth . ';">
        <div class="message-body">
          ' . $GLOBALS['LANG']->sL('LLL:EXT:flipit/locallang_db.xml:sheetFlipit_evaluate_ok') . '
        </div>
      </div>
      ';
      // Evaluation result: default message in case of success

      // Check the plugin
    $str_prompt = $str_prompt . $str_promptDrsEnabled . $str_promptDrsDisabled;
    $str_prompt = $str_prompt . $this-> evaluate_promptSwftools( );
    $str_prompt = $str_prompt . $this-> evaluate_promptConstantEditor( );
    $str_prompt = $str_prompt . $str_prompt_info_tutorialAndForum;
    return $str_prompt;
  }






/**
 * evaluate_plugin: Evaluates the plugin, flexform, TypoScript
 *                  Returns a HTML report
 *
 * Tab [evaluate]
 *
 * @param	array		$arr_pluginConf:  Current plugin/flexform configuration
 * @param	array		$obj_TCEform:     Current TCE form object
 * @return	string		$str_prompt: HTML prompt
 * @version 1.0.1
 * @since 1.0.1
 */
  private function evaluate_promptConstantEditor( )
  {
      //.message-notice
      //.message-information
      //.message-ok
      //.message-warning
      //.message-error

    $str_prompt = '
      <div class="typo3-message message-notice" style="max-width:' . $this->maxWidth . ';">
        <div class="message-body">
          ' . $GLOBALS['LANG']->sL('LLL:EXT:flipit/locallang_db.xml:sheetFlipit_evaluate_ok_constantEditor') . '
        </div>
      </div>
      ';
    
    $str_prompt = str_replace( '%pid%', $this->pid, $str_prompt );
    return $str_prompt;
  }
  
/**
 * evaluate_promptSwftools: Evaluates the plugin, flexform, TypoScript
 *                  Returns a HTML report
 *
 * Tab [evaluate]
 *
 * @param	array		$arr_pluginConf:  Current plugin/flexform configuration
 * @param	array		$obj_TCEform:     Current TCE form object
 * @return	string		$str_prompt: HTML prompt
 * @version 1.0.1
 * @since 1.0.1
 */
  private function evaluate_promptSwftools( )
  {
      //.message-notice
      //.message-information
      //.message-ok
      //.message-warning
      //.message-error

      // Include class userfunc
    $typo3_document_root  = t3lib_div::getIndpEnv( 'TYPO3_DOCUMENT_ROOT' );
    $pathToUserfunc       = $typo3_document_root . '/typo3conf/ext/flipit/lib/userfunc/class.tx_flipit_userfunc.php';
    require_once( $pathToUserfunc );
    $this->objUserfunc = new tx_flipit_userfunc( $this );
    $this->objUserfunc->set_allParams( );
      // Include class userfunc

    switch( $this->objUserfunc->swfTools )
    {
      case( 'installed' ):
        $type   = 'ok';
        $prompt = $GLOBALS['LANG']->sL('LLL:EXT:flipit/locallang_db.xml:sheetFlipit_evaluate_ok_swftools');
        break;
      case( 'notInstalled' ):
      default:
        $type   = 'warning';
        $prompt = $GLOBALS['LANG']->sL('LLL:EXT:flipit/locallang_db.xml:sheetFlipit_evaluate_warn_swftools');
        break;
    }

    $str_prompt = '
      <div class="typo3-message message-'. $type . '" style="max-width:' . $this->maxWidth . ';">
        <div class="message-body">
          ' . $prompt . '
        </div>
      </div>
      ';
    
    $str_prompt = str_replace( '%pid%', $this->pid, $str_prompt );
    return $str_prompt;
  }
  
  
  /***********************************************
  *
  * Init
  *
  **********************************************/
  
/**
 * init(): Initiate this class.
 *
 * @param	array		$arr_pluginConf: Current plugin/flexform configuration
 * @return	boolean		TRUE: success. FALSE: error.
 * @since 1.0.1
 * @version 1.0.1
 */
  private function init($arr_pluginConf)
  {
      // Require classes
    require_once(PATH_t3lib.'class.t3lib_page.php');
    require_once(PATH_t3lib.'class.t3lib_tstemplate.php');
    require_once(PATH_t3lib.'class.t3lib_tsparser_ext.php');

      // Init page id and the page object
    $this->init_pageUid($arr_pluginConf);
    $this->init_pageObj($arr_pluginConf);

      // Init agregrated TypoScript
    $arr_rows_of_all_pages_inRootLine = $this->obj_page->getRootLine($this->pid);
    if (empty($arr_rows_of_all_pages_inRootLine))
    {
      return false;
    }
    $this->init_tsObj($arr_rows_of_all_pages_inRootLine);

    $this->init = true;
    return true;
  }

/**
 * init_pageObj(): Initiate an page object.
 *
 * @param	array		$arr_pluginConf: Current plugin/flexform configuration
 * @return	boolean		FALSE
 * @since 1.0.1
 * @version 1.0.1
 */
  private function init_pageObj( )
  {
    if(!empty($this->obj_page))
    {
      return false;
    }

      // Set current page object
    $this->obj_page = t3lib_div::makeInstance('t3lib_pageSelect');

    return false;
  }

/**
 * init_pageUid(): Initiate the page uid.
 *
 * @param	array		$arr_pluginConf: Current plugin/flexform configuration
 * @return	boolean		FALSE
 * @since 1.0.1
 * @version 1.0.1
 */
  private function init_pageUid($arr_pluginConf)
  {
    if(!empty($this->pid))
    {
      return false;
    }

      // Update: Get current page id from the plugin
    $int_pid = false;
    if($arr_pluginConf['row']['pid'] > 0)
    {
      $int_pid = $arr_pluginConf['row']['pid'];
    }
      // Update: Get current page id from the plugin

      // New: Get current page id from the current URL
    if(!$int_pid)
    {
        // Get backend URL - something like .../alt_doc.php?returnUrl=db_list.php&id%3D2926%26table%3D%26imagemode%3D1&edit[tt_content][1734]=edit
      $str_url    = $_GET['returnUrl'];
        // Get curent page id
      $int_pid = intval(substr($str_url, strpos($str_url, 'id=')+3));
    }
      // New: Get current page id from the current URL

      // Set current page id
    $this->pid = $int_pid;

    return false;
  }

/**
 * init_tsObj(): Initiate the TypoScript of the current page.
 *
 * @param	array		$arr_rows_of_all_pages_inRootLine: Agregate the TypoScript of all pages in the rootline
 * @return	boolean		FALSE
 * @since 1.0.1
 * @version 1.0.1
 */
  private function init_tsObj($arr_rows_of_all_pages_inRootLine)
  {
    if(!empty($this->obj_TypoScript))
    {
      return false;
    }

    $this->obj_TypoScript = t3lib_div::makeInstance('t3lib_tsparser_ext');
    $this->obj_TypoScript->tt_track = 0;
    $this->obj_TypoScript->init();
    $this->obj_TypoScript->runThroughTemplates($arr_rows_of_all_pages_inRootLine);
    $this->obj_TypoScript->generateConfig();

    return false;
  }



}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/flipit/flexform/class.tx_flipit_flexform.php'])
{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/flipit/flexform/class.tx_flipit_flexform.php']);
}

?>