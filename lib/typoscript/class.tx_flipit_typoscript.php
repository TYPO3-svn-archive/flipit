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
    unset( $content );
    
      // Current TypoScript configuration
    $this->conf = $conf;
    
      // Get the global TCA
      /* BACKGROUND : t3lib_div::loadTCA($table) loads for the frontend
       * only 'ctrl' and 'feInterface' parts.
       */
    $GLOBALS['TSFE']->includeTCA( );

      // Init
    $arr_return = $this->init( $conf );

      // IF return  : return with an error prompt
    if( $arr_return['return'] )
    {
      return $arr_return['content'];
    }
      // IF return  : return with an error prompt
    
      // Get table.field, where media files are stored
    $table  = $conf['userFunc.']['configuration.']['currentTable'];
    $field  = $conf['userFunc.']['configuration.']['tables.'][$table . '.']['media'];
      // Get table.field, where files are stored

      // RETURN : no media files
    if( empty ( $this->cObj->data[$field] ) )
    {
      if( $this->b_drs_flipit )
      {    
        $prompt = $table . '.' . $field . ' is empty. Nothing todo. Return!';
        t3lib_div::devlog( '[INFO/FLIPIT] ' . $prompt, $this->extKey, 0 );
      }
      return;
    }
      // RETURN : no media files

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
      // Get swf files
    $tx_flipit_swf_files  = $this->cObj->data['tx_flipit_swf_files'];
    $arr_swfFiles         = explode( ',', $tx_flipit_swf_files );
      // Get swf files
    
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

      // Set timestamps
    $this->zz_tstampMedia( );
    $this->zz_tstampSwf( );
      // Set timestamps
    
      // RETURN true  : SWF files are deprecated
    if( $this->tstampMedia >= $this->tstampSwf )
    {
      if( $this->b_drs_swf )
      {    
        $prompt = 'A media file is newer than the last swf file.';
        t3lib_div::devlog( '[INFO/SWF] ' . $prompt, $this->extKey, 0 );
      }
      return true;
    }
      // RETURN true  : SWF files are deprecated
    
      // RETURN false : SWF files are up to date
    if( $this->b_drs_swf )
    {    
      $prompt = 'SWF files are up to date.';
      t3lib_div::devlog( '[INFO/SWF] ' . $prompt, $this->extKey, 0 );
    }
    return false;
      // RETURN false : SWF files are up to date
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
      // Get xml file
    $arr_xmlFiles = array( );
    $arr_xmlFiles[0] = $this->cObj->data['tx_flipit_xml_file'];
    
      // RETURN true : there isn't any XML file
    if( empty ( $arr_xmlFiles ) )
    {
      if( $this->b_drs_xml )
      {    
        $prompt = 'There isn\'t any XML file.';
        t3lib_div::devlog( '[INFO/SWF] ' . $prompt, $this->extKey, 0 );
      }
      return true;
    }
      // RETURN true : there isn't any XML file

      // Set timestamps
    $this->zz_tstampSwf( );
    $this->zz_tstampXml( );
      // Set timestamps
    
      // RETURN true  : XML file is deprecated
    if( $this->tstampSwf >= $this->tstampXml )
    {
      if( $this->b_drs_xml )
      {    
        $prompt = 'Swf files are newer than the xml file.';
        t3lib_div::devlog( '[INFO/XML] ' . $prompt, $this->extKey, 0 );
      }
      return true;
    }
      // RETURN true  : XML file is deprecated
    
      // Set timestamp
    $this->zz_tstampRecord( );
    
      // RETURN true  : XML file is deprecated
    if( $this->tstampRecord >= $this->tstampXml )
    {
      if( $this->b_drs_xml )
      {    
        $prompt = 'Record is newer than the xml file.';
        t3lib_div::devlog( '[INFO/XML] ' . $prompt, $this->extKey, 0 );
      }
      return true;
    }
      // RETURN true  : XML file is deprecated
    
      // RETURN false : XML file is up to date
    if( $this->b_drs_xml )
    {    
      $prompt = 'XML file is up to date.';
      t3lib_div::devlog( '[INFO/XML] ' . $prompt, $this->extKey, 0 );
    }
    return false;
      // RETURN false : XML file is up to date
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
  * zz_TCAload( ): Load the TCA, if we don't have an table.columns array
  *
  * @param	string		$table: name of table
  * @return	void
  * @access     private
  * 
  * @version   0.0.2
  * @since     0.0.2
  */
  private function zz_TCAload( $table )
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
    if ( $this->b_drs_init )
    {
      $prompt = '$GLOBALS[TCA]['.$table.'] is loaded.';
      t3lib_div::devlog( '[INFO/INIT] ' . $prompt, $this->extKey, 0 );
    }
      // DRS

  }

  
  
 /**
  * zz_tstampLatest( ): Get the latest timestamp of the given files
  *
  * @param    string    $field        : name of the field, where files are stored
  * @return   integer   $tstampLatest : timestamp of the latest file
  * @access   private
  * @version  0.0.2
  * @since    0.0.2
  */
  private function zz_tstampLatest( $field, $latest )
  {
    $conf = $this->conf;
    
      // Get the current table
    $table  = $conf['userFunc.']['configuration.']['currentTable'];

      // Get files
    $csvFiles = $this->cObj->data[$field];
    $files    = explode( ',', $csvFiles );
      // Get files
    
      // RETURN null : there isn't any file
    if( empty ( $files ) )
    {
      if( $this->b_drs_flipit )
      {    
        $prompt = 'There isn\'t any file.';
        t3lib_div::devlog( '[INFO/FLIPIT] ' . $prompt, $this->extKey, 0 );
      }
      return null;
    }
      // RETURN null : there isn't any file
    
      // Get path to the file
    $this->zz_TCAload( $table );
    $uploadFolder         = $GLOBALS['TCA'][$table]['columns'][$field]['config']['uploadfolder'];
    $typo3_document_root  = t3lib_div::getIndpEnv( 'TYPO3_DOCUMENT_ROOT' );
    $path                 = $typo3_document_root . '/' . $uploadFolder;
    
    $tstampLatest = null;
    $tstampFirst  = null;
    foreach( ( array ) $files as $file )
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
      switch( $latest )
      {
        case( true ):
          if( ( $tstampLatest === null ) || ( $tstampCurrent > $tstampLatest ) )
          {
            $tstampLatest = $tstampCurrent;
          }
          break;
        case( false ):
        default:
          if( ( $tstampFirst === null ) || ( $tstampCurrent < $tstampFirst ) )
          {
            $tstampFirst = $tstampCurrent;
          }
          break;
      }
    }
    
    switch( $latest )
    {
      case( true ):
        if( $this->b_drs_flipit )
        {    
          $prompt = 'latest ' . $table . '.' . $field . ': ' . date ( 'Y-m-d H:i:s.', $tstampLatest );
          t3lib_div::devlog( '[INFO/FLIPIT] ' . $prompt, $this->extKey, 0 );
        }

        return $tstampLatest;
        break;
      case( false ):
      default:
        if( $this->b_drs_flipit )
        {    
          $prompt = 'first ' . $table . '.' . $field . ': ' . date ( 'Y-m-d H:i:s.', $tstampFirst );
          t3lib_div::devlog( '[INFO/FLIPIT] ' . $prompt, $this->extKey, 0 );
        }

        return $tstampFirst;
        break;
    }
  }

  
  
 /**
  * zz_tstampMedia( ): 
  *
  * @return	void
  * @access     private
  * @version  0.0.2
  * @since    0.0.2
  */
  private function zz_tstampMedia( )
  {
    $conf = $this->conf;
    
    if( ! ( $this->tstampMedia === null ) )
    {
      return; 
    }
    
      // Get table.field, where files are stored
    $table  = $conf['userFunc.']['configuration.']['currentTable'];
    $field  = $conf['userFunc.']['configuration.']['tables.'][$table . '.']['media'];
      // Get table.field, where files are stored

      // Get latest timestamp of files in given field
    $latest = true;
    $this->tstampMedia = $this->zz_tstampLatest( $field, $latest );
  }

  
  
 /**
  * zz_tstampRecord( ): 
  *
  * @return	void
  * @access     private
  * @version  0.0.2
  * @since    0.0.2
  */
  private function zz_tstampRecord( )
  {
    $conf = $this->conf;
    
    if( ! ( $this->tstampRecord === null ) )
    {
      return; 
    }
    
      // Get table.field, where tmstamp is stored
    $table  = $conf['userFunc.']['configuration.']['currentTable'];
    $field  = $conf['userFunc.']['configuration.']['tables.'][$table . '.']['tstamp'];
      // Get table.field, where files are stored

      // Get timestamp of current record
    $this->tstampRecord = $this->cObj->data[$field];

    if( $this->b_drs_flipit )
    {    
      $prompt = $table . '.' . $field . ': ' . date ( 'Y-m-d H:i:s.', $this->tstampRecord );
      t3lib_div::devlog( '[INFO/FLIPIT] ' . $prompt, $this->extKey, 0 );
    }
    
  }

  
  
 /**
  * zz_tstampSwf( ): 
  *
  * @return	void
  * @access     private
  * @version  0.0.2
  * @since    0.0.2
  */
  private function zz_tstampSwf( )
  {
    if( ! ( $this->tstampSwf === null ) )
    {
      return; 
    }
    
      // Get latest timestamp of files in given field
    $latest = false;
    $this->tstampSwf = $this->zz_tstampLatest( 'tx_flipit_swf_files', $latest );
  }

  
  
 /**
  * zz_tstampXml( ): 
  *
  * @return	void
  * @access     private
  * @version  0.0.2
  * @since    0.0.2
  */
  private function zz_tstampXml( )
  {
    if( ! ( $this->tstampXml === null ) )
    {
      return; 
    }
      // Get latest timestamp of files in given field
    $latest = true;
    $this->tstampXml = $this->zz_tstampLatest( 'tx_flipit_xml_file', $latest );
  }
  


}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/flipit/lib/class.tx_flipit_typoscript.php'])
{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/flipit/lib/class.tx_flipit_typoscript.php']);
}

?>
