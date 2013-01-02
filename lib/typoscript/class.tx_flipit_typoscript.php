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
  * Path to this script
  *
  * @var string
  */
  public $scriptRelPath = 'lib/typoscript/class.tx_flipit_typoscript.php';
  
 /**
  * Current TypoScript configuration
  *
  * @var array
  */
  private $conf;

  
  
 /**
  * renderFlipit( ): 
  *
  * @param	string		Content input. Not used, ignore.
  * @param	array		TypoScript configuration
  * @return	mixed		HTML output.
  * @access public
  * @version 0.0.2
  * @since 0.0.1
  */
  public function renderFlipit( $content, $conf )
  {
      // Current TypoScript configuration
    $this->conf = $conf;
    
      // Init
    $arr_return = $this->init( $conf );

      // IF return  : return with an error prompt
    if( $arr_return['return'] )
    {
      return $arr_return['content'];
    }
      // IF return  : return with an error prompt
    
    if( $this->b_drs_todo )
    {    
      $prompt = 'Check if there is a PDF file. If not: return!';
      t3lib_div::devlog( '[INFO/TODO] ' . $prompt, $this->extKey, 2 );
    }

      // Generate and check SWF and XML files
    $this->flipit( );   

      // Return the content
    return $this->content( $conf );    
  }

  
  
 /**
  * content( ): 
  *
  * @param	array		TypoScript configuration
  * @return	mixed		HTML output.
  * @access private
  * @version 0.0.1
  * @since 0.0.1
  */
  private function content( )
  {
    $conf = $this->conf;

    $coa_name = $conf['userFunc.']['content'];
    $coa_conf = $conf['userFunc.']['content.'];
    $content  = $this->cObj->cObjGetSingle( $coa_name, $coa_conf );
    
    if( $this->b_drs_flipit )
    {
      switch( $content )
      {
        case( false ):
          $prompt = 'Flip it! is delivered without content.';
          t3lib_div::devlog( '[WARN/FLIPIT] ' . $prompt, $this->extKey, 2 );
          break;
        case( true ):
        default:
          $prompt = 'Flip it! is delivered with content.';
          t3lib_div::devlog( '[OK/FLIPIT] ' . $prompt, $this->extKey, -1 );
          break;
      }
    }

    return $content;
    
  }

  
  
 /**
  * flipit( ): 
  *
  * @param	array		TypoScript configuration
  * @return	mixed		HTML output.
  * @access   private
  * @version  0.0.2
  * @since    0.0.2
  */
  private function flipit( )
  {
      // Generate and check SWF
    $this->flipitSwf( );   
      // Generate and check XML
    $this->flipitXml( );   
  }

  
  
 /**
  * flipitSwf( ): 
  *
  * @param	array		TypoScript configuration
  * @return	mixed		HTML output.
  * @access   private
  * @version  0.0.2
  * @since    0.0.2
  */
  private function flipitSwf( )
  {
    $swfFilesAreDeprecated = false;
    
      // Render SWF files if they are deprecated or if there isn't any SWF file
    $swfFilesAreDeprecated = $this->flipitSwfFilesAreDeprecated( );
    if( $swfFilesAreDeprecated )
    {
      $this->flipitSwfFilesRenderIt( );
    }
      // Render SWF files if they are deprecated or if there isn't any SWF file
  }

  
  
 /**
  * flipitSwfFilesAreDeprecated( ): 
  *
  * @return   boolean		
  * @access   private
  * @version  0.0.2
  * @since    0.0.2
  */
  private function flipitSwfFilesAreDeprecated( )
  {
    $tx_flipit_swf_files  = $this->cObj->data['tx_flipit_swf_files'];
    $arr_swfFiles         = explode( ',', $tx_flipit_swf_files );
    
      // RETURN true : there isn't any SWF file
    if( empty ( $arr_swfFiles ) )
    {
      if( $this->b_drs_swf )
      {    
        $prompt = 'There isn\'t any SWF file.';
        t3lib_div::devlog( '[INFO/SWF] ' . $prompt, $this->extKey, 0 );
      }
      return true;
    }
      // RETURN true : there isn't any SWF file

    if( $this->b_drs_todo )
    {    
      $prompt = 'Check tstamp of current record.';
      t3lib_div::devlog( '[INFO/TODO] ' . $prompt, $this->extKey, 2 );
    }

      // Get timestamp of pdf file
    $tstampPdf = $this->tstampPdf( );
      // Get timestamp of last swf file
    $tstampSwf = $this->tstampSwf( );
    
    if( $tstampPdf >= $tstampSwf )
    {
      if( $this->b_drs_swf )
      {    
        $prompt = 'Pdf file is newer than the last swf file.';
        t3lib_div::devlog( '[INFO/SWF] ' . $prompt, $this->extKey, 0 );
      }
      return true;
    }
    
    if( $this->b_drs_todo )
    {    
      $prompt = 'Check, if SWF files are older than PDF file.';
      t3lib_div::devlog( '[INFO/TODO] ' . $prompt, $this->extKey, 2 );
    }
    
    if( $this->b_drs_xml )
    {    
      $prompt = 'SWF files are up to date.';
      t3lib_div::devlog( '[INFO/XML] ' . $prompt, $this->extKey, 0 );
    }
    return false;
  }

  
  
 /**
  * flipitSwfFilesRenderIt( ): 
  *
  * @param	array		TypoScript configuration
  * @return	mixed		HTML output.
  * @access   private
  * @version  0.0.2
  * @since    0.0.2
  */
  private function flipitSwfFilesRenderIt( )
  {
    if( $this->b_drs_swf )
    {    
      $prompt = 'There isn\'t any SWF file.';
      t3lib_div::devlog( '[INFO/SWF] ' . $prompt, $this->extKey, 0 );

      if( $this->objUserfunc->swfTools != 'installed' )
      {

      }
    }

    if( $this->b_drs_swf )
    {    
      $prompt = 'Render SWF files!';
      t3lib_div::devlog( '[INFO/SWF] ' . $prompt, $this->extKey, 2 );
    }

    return '<p>' . var_export( $this->cObj->data, true ) . ' </p>';
    
  }

  
  
 /**
  * flipitXml( ): 
  *
  * @param	array		TypoScript configuration
  * @return	mixed		HTML output.
  * @access   private
  * @version  0.0.2
  * @since    0.0.2
  */
  private function flipitXml( )
  {
    $xmlFileAreDeprecated = false;
    
      // Render SWF files if they are deprecated or if there isn't any SWF file
    $xmlFileAreDeprecated = $this->flipitXmlFileIsDeprecated( );
    if( $xmlFileAreDeprecated )
    {
      $this->flipitXmlFileRenderIt( );
    }
      // Render SWF files if they are deprecated or if there isn't any SWF file
  }

  
  
 /**
  * flipitXmlFileIsDeprecated( ): 
  *
  * @param	array		TypoScript configuration
  * @return	mixed		HTML output.
  * @access   private
  * @version  0.0.2
  * @since    0.0.2
  */
  private function flipitXmlFileIsDeprecated( )
  {
    $xmlFile = $this->cObj->data['tx_flipit_xml_file'];
    
      // RETURN true : there isn't any XML file
    if( empty ( $xmlFile ) )
    {
      if( $this->b_drs_xml )
      {    
        $prompt = 'There isn\'t any XML file.';
        t3lib_div::devlog( '[INFO/XML] ' . $prompt, $this->extKey, 0 );
      }
      return true;
    }
      // RETURN true : there isn't any XML file
    
    if( $this->b_drs_todo )
    {    
      $prompt = 'Check, if XML file is older than PDF file.';
      t3lib_div::devlog( '[INFO/TODO] ' . $prompt, $this->extKey, 2 );
    }
    
    if( $this->b_drs_xml )
    {    
      $prompt = 'XML file is up to date.';
      t3lib_div::devlog( '[INFO/XML] ' . $prompt, $this->extKey, 0 );
    }
    return false;
  }

  
  
 /**
  * flipitXmlFileRenderIt( ): 
  *
  * @param	array		TypoScript configuration
  * @return	mixed		HTML output.
  * @access   private
  * @version  0.0.2
  * @since    0.0.2
  */
  private function flipitXmlFileRenderIt( )
  {
    if( $this->b_drs_xml )
    {    
      $prompt = 'Render XML file!';
      t3lib_div::devlog( '[INFO/XML] ' . $prompt, $this->extKey, 2 );
    }

    return '<p>' . var_export( $this->cObj->data, true ) . ' </p>';
    
  }

  
  
 /**
  * init( ): 
  *
  * @return	mixed		HTML output.
  * @access private
  * @version 0.0.1
  * @since 0.0.1
  */
  private function init( )
  {
      // Init extension configuration array
    $this->arr_extConf = unserialize( $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey] );
    
      // Init the DRS
    $this->initDrs( );
    
      // RETURN : Flip it! is disabled or there is an error
    $arr_return = $this->initEnable( );
    if( $arr_return['return'] )
    {
      return $arr_return;
    }
      // RETURN : Flip it! is disabled or there is an error

      // Require class userfunc
    $this->initClasses( );

    return;
  }
  
  
  
/**
 * initClasses( ): Init the DRS - Development Reportinmg System
 *
 * @return	void
 * @access private
 */
  private function initClasses( )
  {
      // Include class userfunc
    $typo3_document_root  = t3lib_div::getIndpEnv( 'TYPO3_DOCUMENT_ROOT' );
    $pathToUserfunc       = $typo3_document_root . '/typo3conf/ext/flipit/lib/userfunc/class.tx_flipit_userfunc.php';
    require_once( $pathToUserfunc );
    $this->objUserfunc = new tx_flipit_userfunc( $this );
    $this->objUserfunc->set_allParams( );
      // Include class userfunc

      // DRS
    if( $this->b_drs_init )
    {
      $prompt = 'OS: ' . $this->objUserfunc->os;
      t3lib_div::devlog( '[INFO/INIT] ' . $prompt, $this->extKey, 0 );
      $prompt = 'SWFTOOLS: ' . $this->objUserfunc->swfTools;
      t3lib_div::devlog( '[INFO/INIT] ' . $prompt, $this->extKey, 0 );
      $prompt = 'TYPO3 version: ' . $this->objUserfunc->typo3Version;
      t3lib_div::devlog( '[INFO/INIT] ' . $prompt, $this->extKey, 0 );    
    }
      // DRS
  }
  
  
  
/**
 * initDrs( ): Init the DRS - Development Reportinmg System
 *
 * @return	void
 * @access private
 */
  private function initDrs( )
  {
    
      // Enable the DRS by TypoScript
    switch( $this->arr_extConf['debuggingDrs'] )
    {
      case( 'Disabled' ):
        return;
        break;
      case( 'Enabled (for debugging only!)' ):
          // Follow the workflow
        break;
      default:
        $prompt = 'Error: debuggingDrs is undefined.<br />
          value is ' . $this->arr_extConf['debuggingDrs'] . '<br />
          <br />
          ' . __METHOD__ . ' line(' . __LINE__. ')';
        die( $prompt );
    }

    $this->b_drs_error  = true;
    $this->b_drs_warn   = true;
    $this->b_drs_info   = true;
    $this->b_drs_ok     = true;
    $this->b_drs_flipit = true;
    $this->b_drs_init   = true;
    $this->b_drs_swf    = true;
    $this->b_drs_todo   = true;
    $this->b_drs_xml    = true;
    $prompt = 'The DRS - Development Reporting System is enabled: ' . $this->arr_extConf['debuggingDrs'];
    t3lib_div::devlog( '[INFO/DRS] ' . $prompt, $this->extKey, 0 );
  }

  
  
 /**
  * initEnable( ): 
  *
  * @return	mixed		HTML output.
  * @access   private
  * @version  0.0.1
  * @since    0.0.1
  */
  private function initEnable( )
  {
    $conf = $this->conf;

    $arr_return = array( );
    $arr_return['return'] = false;
    
    $coa_name = $conf['userFunc.']['enabled'];
    $coa_conf = $conf['userFunc.']['enabled.'];
    $enabled  = $this->cObj->cObjGetSingle( $coa_name, $coa_conf );
    
    switch( $enabled )
    {
      case( 'enabled' ):
        if( $this->b_drs_init )
        {
          $prompt = 'Flip it! is enabled.';
          t3lib_div::devlog( '[INFO/INIT] ' . $prompt, $this->extKey, 0 );
        }
        $arr_return['return'] = false;
        break;
      case( 'disabled' ):
        if( $this->b_drs_init )
        {
          $prompt = 'Flip it! is disabled.';
          t3lib_div::devlog( '[INFO/INIT] ' . $prompt, $this->extKey, 0 );
        }
        $arr_return['return'] = true;
        break;
      case( 'error' ):
      default:
        if( $this->b_drs_init )
        {
          $prompt = 'The enabling mode of Flip it! isn\'t part of the list: disabled,enabled,ts';
          t3lib_div::devlog( '[ERROR/INIT] ' . $prompt, $this->extKey, 3 );
          $prompt = 'Flip it! won\'t run!';
          t3lib_div::devlog( '[WARN/INIT] ' . $prompt, $this->extKey, 3 );
        }
        $arr_return['return']   = true;
        $arr_return['content']  = $enabled;
        break;
    }

    return $arr_return;
    
  }



 /**
  * TCAload( ): Load the TCA, if we don't have an table.columns array
  *
  * @param	string		$table: name of table
  * @return	void
  * @access     private
  * 
  * @version   0.0.2
  * @since     0.0.2
  */
  private function TCAload( $table )
  {
      // RETURN : TCA is loaded
    if( is_array( $GLOBALS['TCA'][$table]['columns'] ) )
    {
      return;
    }
      // RETURN : TCA is loaded
    
      // Load the TCA
    t3lib_div::loadTCA( $table );

      // DRS
    if ( $this->pObj->b_drs_init )
    {
      $prompt = '$GLOBALS[TCA]['.$table.'] is loaded.';
      t3lib_div::devlog( '[INFO/INIT] ' . $prompt, $this->pObj->extKey, 0 );
    }
      // DRS

  }

  
  
 /**
  * tstampPdf( ): 
  *
  * @return	integer
  * @access     private
  * @version  0.0.2
  * @since    0.0.2
  */
  private function tstampPdf( )
  {
    $conf = $this->conf;
      // Woher weiss ich, in welcher Tabelle und welchem Feld die Dateien abgelegt sind?
        // Wenn CType, colPos und media -> tt_content? 

    $files      = $this->cObj->data['media'];
    $arr_files  = explode( ',', $files );
    
      // RETURN 0 : there isn't any media file
    if( empty ( $arr_files ) )
    {
      if( $this->b_drs_error )
      {    
        $prompt = 'There isn\'t any media file.';
        t3lib_div::devlog( '[ERROR/FLIPIT] ' . $prompt, $this->extKey, 3 );
      }
      return 0;
    }
      // RETURN 0 : there isn't any XML file
    
      // Get path to PDF file
    $table = 'tt_content';
    $this->TCAload( $table );
    $uploadFolder         = $GLOBALS['TCA']['tt_content']['columns']['media']['config']['uploadfolder'];
    $typo3_document_root  = t3lib_div::getIndpEnv( 'TYPO3_DOCUMENT_ROOT' );
    $path                 = $typo3_document_root . '/' . $uploadFolder;
    
    $tstampLatest = null;
    foreach( ( array ) $arr_files as $file )
    {
      $pathToFile = $path . '/' . $file;
      if( ! file_exists( $pathToFile ) )
      {
        if( $this->b_drs_error )
        {    
          $prompt = 'Does not exist: ' . $pathToFile;
          t3lib_div::devlog( '[ERROR/FLIPIT] ' . $prompt, $this->extKey, 3 );
        }
        continue;
      }
      $tstampCurrent = filemtime( $pathToFile );
      if( $tstampCurrent > $tstampLatest )
      {
        $tstampLatest = $tstampCurrent;
      }

//      if( $this->b_drs_flipit )
//      {    
//        $prompt = $pathToFile;
//        t3lib_div::devlog( '[INFO/FLIPIT] ' . $prompt, $this->extKey, 0 );
//      }
    }
    
    if( $this->b_drs_error )
    {    
      $prompt = 'Datetime of latest file: ' . date ( 'F d Y H:i:s.', $tstampLatest );
      t3lib_div::devlog( '[INFO/FLIPIT] ' . $prompt, $this->extKey, 0 );
    }
    
    
  }

  
  
 /**
  * tstampSwf( ): 
  *
  * @return	integer
  * @access     private
  * @version  0.0.2
  * @since    0.0.2
  */
  private function tstampSwf( )
  {
  }

  
  
 /**
  * tstampXml( ): 
  *
  * @return	integer
  * @access     private
  * @version  0.0.2
  * @since    0.0.2
  */
  private function tstampXml( )
  {
  }
  


}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/flipit/lib/class.tx_flipit_typoscript.php'])
{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/flipit/lib/class.tx_flipit_typoscript.php']);
}

?>
