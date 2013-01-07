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
  * Array with current files from media, tx_flipit_swf_files, tx_flipit_xml_file
  *
  * @var array
  */
  private $files;
  
 /**
  * Current table: tt_content, tx_org_downloads
  *
  * @var string
  */
  private $table;
  
 /**
  * Global tstamp for updates. It muts be older than the tstamp of generated files
  *
  * @var integer
  */

  private $tstamp;
  

  
  
 /**
  * main( ): 
  *
  * @param	string		Content input. Not used, ignore.
  * @param	array		TypoScript configuration
  * @return	mixed		HTML output.
  * @access public
  * @version 0.0.2
  * @since 0.0.1
  */
  public function main( $content, $conf )
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
    
      // Get field, where media files are stored
    $field  = $conf['userFunc.']['configuration.']['tables.'][$this->table . '.']['media'];
      // Get table.field, where files are stored

      // RETURN : no media files
    if( empty ( $this->cObj->data[$field] ) )
    {
      if( $this->b_drs_flipit )
      {    
        $prompt = $this->table . '.' . $field . ' is empty. Nothing todo. Return!';
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
      $this->flipitSwfFilesRenderAll( );
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
    
      // Set timestamp for the current record
    $this->zz_tstampRecord( );
    
      // RETURN true  : SWF files are deprecated
    if( $this->tstampRecord >= $this->tstampSwf )
    {
      if( $this->b_drs_swf )
      {    
        $prompt = 'Record is newer than the swf file.';
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
  * flipitSwfFilesRemove( ): 
  *
  * @return
  * @access   private
  * @version  0.0.3
  * @since    0.0.3
  */
  private function flipitSwfFilesRemove( )
  {
    $conf         = $this->conf;
    $table        = $this->table;
    $fieldFiles   = 'tx_flipit_swf_files';
    $fieldTstamp  = $conf['userFunc.']['configuration.']['tables.'][$table . '.']['tstamp'];
    
    $arrExec      = array( );

      // RETURN : no swf files, any swf file can't remove
    if( empty ( $this->files[$fieldFiles] ) ) 
    {
//        // DRS
//      if( $this->b_drs_swf )
//      {    
//        $prompt = 'Unexpected result: no SWF file!';
//        t3lib_div::devlog( '[INFO/SWF] ' . $prompt, $this->extKey, 3 );
//      }
//        // DRS
      return;
//      if( $this->b_drs_error )
//      {    
//        $prompt = 'Unexpected result: no SWF file!';
//        t3lib_div::devlog( '[ERROR/SWF] ' . $prompt, $this->extKey, 3 );
//      }
    }
      // RETURN : no swf files, any swf file can't remove
    
      // FOREACH files  : get exec command
    foreach( $this->files[$fieldFiles] as $swffileWiPath )
    {
      $arrExec[] = 'rm ' . $swffileWiPath;
      
    }
      // FOREACH files  : get exec command
    
      // Remove swf files
    $exec = implode( ';' . PHP_EOL, ( array ) $arrExec );
    $lines = $this->zz_exec( $exec );
    unset( $lines );
      // Remove swf files
    
      // Update database
    $where = "uid = " . $this->cObj->data['uid'];
    $fields_values = array(
      $fieldTstamp  => $this->tstamp,
      $fieldFiles   => null
    );
      // DRS
    if( $this->b_drs_sql || $this->b_drs_swf )
    {    
      $prompt = $GLOBALS['TYPO3_DB']->UPDATEquery( $table, $where, $fields_values );
      t3lib_div::devlog( '[INFO/SQL+SWF] ' . $prompt, $this->extKey, 0 );
    }
      // DRS
    $GLOBALS['TYPO3_DB']->exec_UPDATEquery( $table, $where, $fields_values );
      // Update database

      // Update cObj->data
    $this->cObj->data[$fieldTstamp] = $this->tstamp;
    $this->cObj->data[$fieldFiles]  = null;

    return;
  }

  
  
 /**
  * flipitSwfFilesRenderAll( ): 
  *
  * @return
  * @access   private
  * @version  0.0.3
  * @since    0.0.2
  */
  private function flipitSwfFilesRenderAll( )
  {
    $conf         = $this->conf;
    $table        = $this->table;
    $fieldFiles   = 'tx_flipit_swf_files';
    $fieldTstamp  = $conf['userFunc.']['configuration.']['tables.'][$table . '.']['tstamp'];

      // filesCounter is needed for unique filenames
    $filesCounter = 0;
    
      // Remove all swfFiles
    $this->flipitSwfFilesRemove( );
    
      // SWITCH : extension
      // jpeg, pdf, png
    $swfFiles = array( );
    
      // FOREACH  : file
    foreach( $this->files['media'] as $fileWiPath )
    {
      $pathParts = pathinfo( $fileWiPath );
      switch( $pathParts['extension'] )
      {
        case('jpg'):
        case('jpeg'):
          $filesCounter = $filesCounter + 1;
          $swfFiles = array_merge( $swfFiles, ( array ) $this->flipitSwfFilesRenderJpg( $fileWiPath, $filesCounter ) );
          break;
        case('pdf'):
          $filesCounter = $filesCounter + 1;
          $swfFiles = array_merge( $swfFiles, ( array ) $this->flipitSwfFilesRenderPdf( $fileWiPath, $filesCounter ) );
          break;
        case('png'):
          $filesCounter = $filesCounter + 1;
          $swfFiles = array_merge( $swfFiles, ( array ) $this->flipitSwfFilesRenderPng( $fileWiPath, $filesCounter ) );
          break;
        default:
          if( $this->b_drs_swf )
          {    
            $prompt = $pathParts['basename'] . ': ' . $pathParts['extension'] . ' can not converted to SWF.';
            t3lib_div::devlog( '[INFO/SWF] ' . $prompt, $this->extKey, 2 );
          }
          break;
      }
    }
      // FOREACH  : file
    
      // FOREACH  : remove empty element
    foreach( $swfFiles as $key => $value )
    {
      if( empty ( $value ) )
      {
        unset( $swfFiles[$key] );
      }
    }
      // FOREACH  : remove empty element

      // DRS
    if( $this->b_drs_swf )
    {    
      $prompt = 'Rendered SWF files: ' . var_export( $swfFiles, true );
      t3lib_div::devlog( '[INFO/SWF] ' . $prompt, $this->extKey, 0 );
    }
      // DRS
    
      // RETURN : there isn't any SWF file
    if( empty ( $swfFiles ) )
    {
      if( $this->b_drs_error )
      {    
        $prompt = 'There isn\'t any SWF file!';
        t3lib_div::devlog( '[ERROR/SWF] ' . $prompt, $this->extKey, 3 );
      }
      return;
    }
      // RETURN : there isn't any SWF file

      // Update database
    $where = "uid = " . $this->cObj->data['uid'];
    $fields_values = array(
      $fieldTstamp  => $this->tstamp,
      $fieldFiles   => implode( ',', $swfFiles )
    );
      // DRS
    if( $this->b_drs_sql || $this->b_drs_swf )
    {    
      $prompt = $GLOBALS['TYPO3_DB']->UPDATEquery( $table, $where, $fields_values );
      t3lib_div::devlog( '[INFO/SQL+SWF] ' . $prompt, $this->extKey, 0 );
    }
      // DRS
    $GLOBALS['TYPO3_DB']->exec_UPDATEquery( $table, $where, $fields_values );
      // Update database
    
      // Update cObj->data
    $this->cObj->data[$fieldTstamp] = $this->tstamp;
    $this->cObj->data[$fieldFiles]  = implode( ',', $swfFiles );

    // Reset tstamp for swf files
    $this->tstampSwf = null;
    
    $this->cObj->data[$fieldFiles] = implode( ',', $swfFiles );
      // Init files again
    $this->initFiles( );


    return;
  }

  
  
 /**
  * flipitSwfFilesRenderJpg( ): 
  *
  * @param    string    $fileWiPath : full path
  * @return   array     $arrReturn  : rendered swf files
  * @access   private
  * @version  0.0.3
  * @since    0.0.3
  */
  private function flipitSwfFilesRenderJpg( $fileWiPath, $filesCounter )
  {
    $arrReturn = null;
    
    if( $this->b_drs_swf )
    {    
      $pathParts = pathinfo( $fileWiPath );
      $prompt = $pathParts['basename'] . ': ' . $pathParts['extension'] . ' is not supported now.';
      t3lib_div::devlog( '[INFO/SWF] ' . $prompt, $this->extKey, 2 );
    }
    return $arrReturn;
  }

  
  
 /**
  * flipitSwfFilesRenderPdf( ): 
  *
  * @param    string    $fileWiPath : full path
  * @return   array     $arrReturn  : rendered swf files
  * @access   private
  * @version  0.0.3
  * @since    0.0.3
  */
  private function flipitSwfFilesRenderPdf( $pdffileWiPath, $filesCounter )
  {
    $arrReturn = null;
    
      // DRS  : PDF info
    if( $this->b_drs_pdf )
    {    
      $exec   = 'pdf2swf -I ' . $pdffileWiPath;
      $lines  = $this->zz_exec( $exec );
        //    pdf2swf -I /home/www/htdocs/www.typo3-browser-forum.de/typo3/uploads/media/manual.pdf
        // $lines:
        //    page = 1 width = 595.00 height = 842.00
        //    page = 2 width = 595.00 height = 842.00
        //    ...
        //    page = 14 width = 595.00 height = 842.00
    }
      // DRS  : PDF info
    
      // SWF output file
    $swfFile =  $this->table . '_' . $this->cObj->data['uid'] . 
                '_doc_' . $filesCounter . '_part_%.swf';
    $field   = 'tx_flipit_swf_files';
    $swfPath = $this->zz_getPath( $field );

      // Render PDF to SWF
    $exec   = 'pdf2swf ' . $pdffileWiPath . ' ' . $swfPath . '/' . $swfFile;
    $lines  = $this->zz_exec( $exec );
      //    pdf2swf /home/www/htdocs/www.typo3-browser-forum.de/typo3/uploads/media/manual.pdf /home/www/htdocs/www.typo3-browser-forum.de/typo3/uploads/tx_flipit/tt_content_1589_%.swf
      // $lines:
      //    NOTICE outputting one file per page
      //    NOTICE File contains links
      //    NOTICE processing PDF page 1 (595x842:0:0)
      //    NOTICE File contains jpeg pictures
      //    NOTICE File contains pbm pictures
      //    FATAL Could not create "1589_1.swf".
    
      // DRS
    if( $this->b_drs_error )
    {
      $csvLines = implode( ', ' . PHP_EOL, $lines );
      $pos = strpos( $csvLines, 'FATAL' );
      if( ! ( $pos === false ) )
      {
        $prompt = $csvLines;
        t3lib_div::devlog( '[WARN/SWF] ' . $prompt, $this->extKey, 2 );
        $prompt = 'There is an error in the prompt before. Please search for FATAL.';
        t3lib_div::devlog( '[ERROR/SWF] ' . $prompt, $this->extKey, 3 );
      }
    }
      // DRS
    
      // get list of rendered swf files
    $swfFile =  $this->table . '_' . $this->cObj->data['uid'] . 
                '_doc_' . $filesCounter . '_part_*.swf';
    $exec   = 'ls -t ' . $swfPath . '/' . $swfFile;
    $lines  = $this->zz_exec( $exec );
      // get list of rendered swf files
      
      // list of swf files ordered by time ascending
    krsort( $lines );

      // FOREACH  : swfFile
    foreach( $lines as $swfFileWiPath )
    {
      $pathParts    = pathinfo( $swfFileWiPath );
      $arrReturn[]  = $pathParts['basename'];
      
    }
      // FOREACH  : swfFile

      // RETURN : swf files without path
    return $arrReturn;
  }

  
  
 /**
  * flipitSwfFilesRenderPng( ): 
  *
  * @param    string    $fileWiPath : full path
  * @return   array     $arrReturn  : rendered swf files
  * @access   private
  * @version  0.0.3
  * @since    0.0.3
  */
  private function flipitSwfFilesRenderPng( $fileWiPath, $filesCounter )
  {
    $arrReturn = null;
    
    if( $this->b_drs_swf )
    {    
      $pathParts = pathinfo( $fileWiPath );
      $prompt = $pathParts['basename'] . ': ' . $pathParts['extension'] . ' is not supported now.';
      t3lib_div::devlog( '[INFO/SWF] ' . $prompt, $this->extKey, 2 );
    }
    return $arrReturn;
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
    $conf = $this->conf;
    
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

      // Init global vars
    $this->table = $conf['userFunc.']['configuration.']['currentTable'];
      // Global tstamp for updates. It muts be older than the tstamp of generated files
    $this->tstamp = time( );


      // DRS
    if( $this->b_drs_flipit )
    {    
      $prompt =  'current record is ' . $this->table. ' ( uid = ' . $this->cObj->data['uid'] . ')';
      t3lib_div::devlog( '[INFO/DRS] ' . $prompt, $this->extKey, 0 );
    }
      // DRS

      // Init file lists
    $this->initFiles( );
    
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
    $this->b_drs_php    = true;
    $this->b_drs_pdf    = true;
    $this->b_drs_sql    = true;
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
  * initFiles( ): 
  *
  * @return	
  * @access   private
  * @version  0.0.3
  * @since    0.0.3
  */
  private function initFiles( )
  {
    $conf = $this->conf;

      // Get files from media
    $field    = $conf['userFunc.']['configuration.']['tables.'][$this->table . '.']['media'];
    $csvFiles = $this->cObj->data[$field];
    $files    = explode( ',', $csvFiles );
    $path     = $this->zz_getPath( $field );
      // Set global var $files
    $this->files[$field] = $this->zz_getFilesWiPath( $files, $path );
      // Get files from media

      // Get files from tx_flipit_swf_files
    $field    = 'tx_flipit_swf_files';
    $csvFiles = $this->cObj->data[$field];
    $files    = explode( ',', $csvFiles );
    $path     = $this->zz_getPath( $field );
      // Set global var $files
    $this->files[$field] = $this->zz_getFilesWiPath( $files, $path );
      // Get files from tx_flipit_swf_files

      // Get files from tx_flipit_xml_file
    $field    = 'tx_flipit_xml_file';
    $csvFiles = $this->cObj->data[$field];
    $files    = explode( ',', $csvFiles );
    $path     = $this->zz_getPath( $field );
      // Set global var $files
    $this->files[$field] = $this->zz_getFilesWiPath( $files, $path );
      // Get files from tx_flipit_xml_file

    return;
  }
  
  
  
/**
 * zz_exec( ): 
 *
 * @return  array   $lines
 * @version 0.0.3
 * @since   0.0.3
 */
  private function zz_exec( $exec )
  {
    $lines = null;
    
      // DIE  : function exec doesn't exist
    if( ! ( function_exists('exec') ) )  
    {
      $prompt = '
        <h1>
          PHP ERROR
        </h1>
        <h1>
          ' . $exec . '
        </h1>
        <p>
          exec( ' . $exec . ', $lines ) can\'t executed!
        </p>
        <p>
          The PHP function exec doesn\'t exist.<br />
          <br />
          Please check your PHP configuration (php.ini):
        </p>
        <ul>
          <li style="margin-bottom:0;">
            The function exec must not be an element of the ini property disable_functions.
          </li>
          <li style="margin-bottom:0;">
            If safe_mode is on, the safe_mode_exec_dir must contain the function exec.
          </li>
        </ul>
        <p>
          TYPO3 extension Flip it!<br />
          Method: ' . __METHOD__ . ' <br />
          Line: ' . __LINE__ . ' 
        </p>
';
      die( $prompt );
    }
      // DIE  : function exec doesn't exist
    
      // DRS
    if( $this->b_drs_php )
    {
      $prompt = $exec;
      t3lib_div::devlog( '[INFO/PHP] exec: ' . $prompt, $this->extKey, 0 );
    }
      // DRS

      // Execute!
    exec( $exec, $lines );
    
      // DRS
    if( $this->b_drs_php )
    {
      $prompt = var_export( $lines, true );
      t3lib_div::devlog( '[INFO/PHP] lines: ' . $prompt, $this->extKey, 0 );
    }
      // DRS
   
      // RETURN : lines
    return $lines;
  }

  
  
 /**
  * zz_getFilesWiPath( ): 
  *
  * @return	
  * @access   private
  * @version  0.0.3
  * @since    0.0.3
  */
  private function zz_getFilesWiPath( $files, $path )
  {
    $arr_return = null; 
    
      // FOREACH  : files
    foreach( ( array ) $files as $file )
    {
      $pathWiFile = $path . '/' . $file;
      if( ! file_exists( $pathWiFile ) )
      {
        if( $this->b_drs_error )
        {
          $prompt = 'Does not exist: ' . $pathWiFile;
          t3lib_div::devlog( '[ERROR/FLIPIT] ' . $prompt, $this->extKey, 3 );
        }
        continue;
      }
        // Get files only not paths
      if( is_file( $pathWiFile ) )
      {
        $arr_return[] = $pathWiFile;
      }
        // Get files only not paths
    }
      // FOREACH  : files

      // RETURN : filesWiPath
    return $arr_return;
  }
  
  
 /**
  * zz_getPath( ): 
  *
  * @return	
  * @access   private
  * @version  0.0.3
  * @since    0.0.3
  */
  private function zz_getPath( $field )
  {
      // Get path
    $this->zz_TCAload( $this->table );
    $uploadFolder         = $GLOBALS['TCA'][$this->table]['columns'][$field]['config']['uploadfolder'];
    $typo3_document_root  = t3lib_div::getIndpEnv( 'TYPO3_DOCUMENT_ROOT' );
    $path                 = $typo3_document_root . '/' . $uploadFolder;

    return $path;
  }



 /**
  * zz_TCAload( ): Load the TCA, if we don't have an table.columns array
  *
  * @return	void
  * @access     private
  * 
  * @version   0.0.2
  * @since     0.0.2
  */
  private function zz_TCAload( )
  {
      // RETURN : TCA is loaded
    if( is_array( $GLOBALS['TCA'][$this->table]['columns'] ) )
    {
      return;
    }
      // RETURN : TCA is loaded
    
      // Load the TCA
    t3lib_div::loadTCA( $this->table );

      // DRS
    if ( $this->b_drs_init )
    {
      $prompt = '$GLOBALS[TCA]['.$this->table.'] is loaded.';
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
      // Get files
    $files = $this->files[$field];
    
      // Get files
    
      // RETURN null : there isn't any file
    if( empty ( $files ) )
    {
      if( $this->b_drs_flipit )
      {    
        $prompt = $this->table. '.' . $field . ' doesn\'t contain any file.';
        t3lib_div::devlog( '[INFO/FLIPIT] ' . $prompt, $this->extKey, 0 );
      }
      return null;
    }
      // RETURN null : there isn't any file
    
    $tstampLatest = null;
    $tstampFirst  = null;
    foreach( ( array ) $files as $file )
    {
      if( ! file_exists( $file ) )
      {
        if( $this->b_drs_error )
        {
          $prompt = 'Does not exist: ' . $file;
          t3lib_div::devlog( '[ERROR/FLIPIT] ' . $prompt, $this->extKey, 3 );
        }
        continue;
      }
      $tstampCurrent = filemtime( $file );
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
          $prompt = 'latest ' . $this->table . '.' . $field . ': ' . date ( 'Y-m-d H:i:s.', $tstampLatest );
          t3lib_div::devlog( '[INFO/FLIPIT] ' . $prompt, $this->extKey, 0 );
        }
        return $tstampLatest;
        break;
      case( false ):
      default:
        if( $this->b_drs_flipit )
        {    
          $prompt = 'first ' . $this->table . '.' . $field . ': ' . date ( 'Y-m-d H:i:s.', $tstampFirst );
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
    $field  = $conf['userFunc.']['configuration.']['tables.'][$this->table . '.']['media'];
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
    $field  = $conf['userFunc.']['configuration.']['tables.'][$this->table . '.']['tstamp'];
      // Get table.field, where files are stored

      // Get timestamp of current record
    $this->tstampRecord = $this->cObj->data[$field];

    if( $this->b_drs_flipit )
    {    
      $prompt = $this->table . '.' . $field . ': ' . date ( 'Y-m-d H:i:s.', $this->tstampRecord );
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
