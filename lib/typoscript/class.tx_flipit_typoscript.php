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
    $this->update( );   

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
  * update( ): 
  *
  * @param	array		TypoScript configuration
  * @return	mixed		HTML output.
  * @access   private
  * @version  0.0.2
  * @since    0.0.2
  */
  private function update( )
  {
    if( ! $this->updateEnabled( ) )
    {
      return;
    }
      // Generate and check SWF
    $this->updateSwf( );   
      // Generate and check XML
    $this->updateXml( );   
  }

  
  
 /**
  * updateEnabled( ): 
  *
  * @return	boolean		
  * @access   private
  * @version  0.0.3
  * @since    0.0.3
  */
  private function updateEnabled( )
  {
    $conf = $this->conf;

    $coa_name = $conf['userFunc.']['configuration.']['updateSwfXml'];
    $coa_conf = $conf['userFunc.']['configuration.']['updateSwfXml.'];
var_dump( __METHOD__, __LINE__, $coa_conf, $this->cObj->data['tx_flipit_updateswfxml'] );
    $updateSwfXml  = $this->cObj->cObjGetSingle( $coa_name, $coa_conf );
    
    switch( $updateSwfXml )
    {
      case( 'enabled' ):
        if( $this->b_drs_init )
        {
          $prompt = 'Auto-update of SWF files and XML file is enabled.';
          t3lib_div::devlog( '[INFO/INIT] ' . $prompt, $this->extKey, 0 );
        }
        return true;
        break;
      case( 'disabled' ):
        if( $this->b_drs_init )
        {
          $prompt = 'Auto-update of SWF files and XML files is disabled.';
          t3lib_div::devlog( '[INFO/INIT] ' . $prompt, $this->extKey, 0 );
        }
        return false;
        break;
      case( 'error' ):
      default:
        if( $this->b_drs_init )
        {
          $prompt = 'Undefined: ' . "['userFunc.']['configuration.']['updateSwfXml']" . ' ' .
                    'is ' . $updateSwfXml;
          t3lib_div::devlog( '[ERROR/INIT] ' . $prompt, $this->extKey, 3 );
          $prompt = 'Auto-update of SWF files and XML files is disabled.';
          t3lib_div::devlog( '[WARN/INIT] ' . $prompt, $this->extKey, 3 );
        }
        return false;
        break;
    }

      // RETURN : default is false;
    return false;
    
  }

  
  
 /**
  * updateSwf( ): 
  *
  * @param	array		TypoScript configuration
  * @return	mixed		HTML output.
  * @access   private
  * @version  0.0.2
  * @since    0.0.2
  */
  private function updateSwf( )
  {
    $swfFilesAreDeprecated = false;
    
      // Render SWF files if they are deprecated or if there isn't any SWF file
    $swfFilesAreDeprecated = $this->updateSwfFilesAreDeprecated( );
    if( $swfFilesAreDeprecated )
    {
      $this->updateSwfFilesRenderAll( );
    }
      // Render SWF files if they are deprecated or if there isn't any SWF file
  }

  
  
 /**
  * updateSwfFilesAreDeprecated( ): 
  *
  * @return   boolean		
  * @access   private
  * @version  0.0.2
  * @since    0.0.2
  */
  private function updateSwfFilesAreDeprecated( )
  {
      // Get swf files
    $tx_flipit_swf_files  = $this->cObj->data['tx_flipit_swf_files'];
    $arr_swfFiles         = explode( ',', $tx_flipit_swf_files );
      // Get swf files
    
      // RETURN true : there isn't any SWF file
    if( empty ( $arr_swfFiles ) )
    {
      if( $this->b_drs_updateSwfXml )
      {    
        $prompt = 'There isn\'t any SWF file.';
        t3lib_div::devlog( '[INFO/SWF+XML] ' . $prompt, $this->extKey, 0 );
      }
      return true;
    }
      // RETURN true : there isn't any SWF file

      // Set timestamps
    $this->zz_tstampMedia( );
    $this->zz_tstampSwf( );
      // Set timestamps
    
      // RETURN true  : SWF files are deprecated
    if( $this->tstampMedia > $this->tstampSwf )
    {
      if( $this->b_drs_updateSwfXml )
      {    
        $prompt = 'A media file is newer than the last swf file.';
        t3lib_div::devlog( '[INFO/SWF+XML] ' . $prompt, $this->extKey, 0 );
      }
      return true;
    }
      // RETURN true  : SWF files are deprecated
    
      // Set timestamp for the current record
    $this->zz_tstampRecord( );
    
      // RETURN true  : SWF files are deprecated
    if( $this->tstampRecord > $this->tstampSwf )
    {
      if( $this->b_drs_updateSwfXml )
      {    
        $prompt = 'Record is newer than the swf file.';
        t3lib_div::devlog( '[INFO/SWF+XML] ' . $prompt, $this->extKey, 0 );
      }
      return true;
    }
      // RETURN true  : SWF files are deprecated

      // RETURN false : SWF files are up to date
    if( $this->b_drs_updateSwfXml )
    {    
      $prompt = 'SWF files are up to date.';
      t3lib_div::devlog( '[INFO/SWF+XML] ' . $prompt, $this->extKey, 0 );
    }
    return false;
      // RETURN false : SWF files are up to date
  }

  
  
 /**
  * updateSwfFilesRemove( ): 
  *
  * @return
  * @access   private
  * @version  0.0.3
  * @since    0.0.3
  */
  private function updateSwfFilesRemove( )
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
//      if( $this->b_drs_updateSwfXml )
//      {    
//        $prompt = 'Unexpected result: no SWF file!';
//        t3lib_div::devlog( '[INFO/SWF+XML] ' . $prompt, $this->extKey, 3 );
//      }
//        // DRS
      return;
//      if( $this->b_drs_error )
//      {    
//        $prompt = 'Unexpected result: no SWF file!';
//        t3lib_div::devlog( '[ERROR/SWF+XML] ' . $prompt, $this->extKey, 3 );
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
    if( $this->b_drs_sql || $this->b_drs_updateSwfXml )
    {    
      $prompt = $GLOBALS['TYPO3_DB']->UPDATEquery( $table, $where, $fields_values );
      t3lib_div::devlog( '[INFO/SQL+SWF+XML] ' . $prompt, $this->extKey, 0 );
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
  * updateSwfFilesRenderAll( ): 
  *
  * @return
  * @access   private
  * @version  0.0.3
  * @since    0.0.2
  */
  private function updateSwfFilesRenderAll( )
  {
    $conf         = $this->conf;
    $table        = $this->table;
    $fieldFiles   = 'tx_flipit_swf_files';
    $fieldTstamp  = $conf['userFunc.']['configuration.']['tables.'][$table . '.']['tstamp'];

      // filesCounter is needed for unique filenames
    $filesCounter = 0;
    
      // Remove all swfFiles
    $this->updateSwfFilesRemove( );
    
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
          $swfFiles = array_merge( $swfFiles, ( array ) $this->updateSwfFilesRenderJpg( $fileWiPath, $filesCounter ) );
          break;
        case('pdf'):
          $filesCounter = $filesCounter + 1;
          $swfFiles = array_merge( $swfFiles, ( array ) $this->updateSwfFilesRenderPdf( $fileWiPath, $filesCounter ) );
          break;
        case('png'):
          $filesCounter = $filesCounter + 1;
          $swfFiles = array_merge( $swfFiles, ( array ) $this->updateSwfFilesRenderPng( $fileWiPath, $filesCounter ) );
          break;
        default:
          if( $this->b_drs_updateSwfXml )
          {    
            $prompt = $pathParts['basename'] . ': ' . $pathParts['extension'] . ' can not converted to SWF.';
            t3lib_div::devlog( '[INFO/SWF+XML] ' . $prompt, $this->extKey, 2 );
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
    if( $this->b_drs_updateSwfXml )
    {    
      $prompt = 'Rendered SWF files: ' . var_export( $swfFiles, true );
      t3lib_div::devlog( '[OK/SWF+XML] ' . $prompt, $this->extKey, -1 );
    }
      // DRS
    
      // RETURN : there isn't any SWF file
    if( empty ( $swfFiles ) )
    {
      if( $this->b_drs_error )
      {    
        $prompt = 'There isn\'t any SWF file!';
        t3lib_div::devlog( '[ERROR/SWF+XML] ' . $prompt, $this->extKey, 3 );
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
    if( $this->b_drs_sql || $this->b_drs_updateSwfXml )
    {    
      $fields_valuesWiSpace = str_replace(',', ', ', $fields_values );
      $prompt = '"," is replaced with ", " for prompting only! ' . $GLOBALS['TYPO3_DB']->UPDATEquery( $table, $where, $fields_valuesWiSpace );
      t3lib_div::devlog( '[INFO/SQL+SWF+XML] ' . $prompt, $this->extKey, 0 );
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
  * updateSwfFilesRenderJpg( ): 
  *
  * @param    string    $fileWiPath : full path
  * @return   array     $arrReturn  : rendered swf files
  * @access   private
  * @version  0.0.3
  * @since    0.0.3
  */
  private function updateSwfFilesRenderJpg( $fileWiPath, $filesCounter )
  {
    $arrReturn = null;
    
    if( $this->b_drs_updateSwfXml )
    {    
      $pathParts = pathinfo( $fileWiPath );
      $prompt = $pathParts['basename'] . ': ' . $pathParts['extension'] . ' is not supported now.';
      t3lib_div::devlog( '[INFO/SWF+XML] ' . $prompt, $this->extKey, 2 );
    }
    return $arrReturn;
  }

  
  
 /**
  * updateSwfFilesRenderPdf( ): 
  *
  * @param    string    $fileWiPath : full path
  * @return   array     $arrReturn  : rendered swf files
  * @access   private
  * @version  0.0.3
  * @since    0.0.3
  */
  private function updateSwfFilesRenderPdf( $pdffileWiPath, $filesCounter )
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
    $exec   = 'pdf2swf ' . $pdffileWiPath . ' ' . $swfPath . DIRECTORY_SEPARATOR . $swfFile;
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
        t3lib_div::devlog( '[WARN/SWF+XML] ' . $prompt, $this->extKey, 2 );
        $prompt = 'There is an error in the prompt before. Please search for FATAL.';
        t3lib_div::devlog( '[ERROR/SWF+XML] ' . $prompt, $this->extKey, 3 );
      }
    }
      // DRS
    
      // get list of rendered swf files
    $swfFile =  $this->table . '_' . $this->cObj->data['uid'] . 
                '_doc_' . $filesCounter . '_part_*.swf';
    $exec   = 'ls -t ' . $swfPath . DIRECTORY_SEPARATOR . $swfFile;
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
  * updateSwfFilesRenderPng( ): 
  *
  * @param    string    $fileWiPath : full path
  * @return   array     $arrReturn  : rendered swf files
  * @access   private
  * @version  0.0.3
  * @since    0.0.3
  */
  private function updateSwfFilesRenderPng( $fileWiPath, $filesCounter )
  {
    $arrReturn = null;
    
    if( $this->b_drs_updateSwfXml )
    {    
      $pathParts = pathinfo( $fileWiPath );
      $prompt = $pathParts['basename'] . ': ' . $pathParts['extension'] . ' is not supported now.';
      t3lib_div::devlog( '[INFO/SWF+XML] ' . $prompt, $this->extKey, 2 );
    }
    return $arrReturn;
  }

  
  
 /**
  * updateXml( ): 
  *
  * @param	array		TypoScript configuration
  * @return	mixed		HTML output.
  * @access   private
  * @version  0.0.2
  * @since    0.0.2
  */
  private function updateXml( )
  {
    $xmlFileAreDeprecated = false;
    
      // Render SWF files if they are deprecated or if there isn't any SWF file
    $xmlFileAreDeprecated = $this->updateXmlFileIsDeprecated( );
    if( $xmlFileAreDeprecated )
    {
      $this->updateXmlFileRenderIt( );
    }
      // Render SWF files if they are deprecated or if there isn't any SWF file
  }

  
  
 /**
  * updateXmlFileIsDeprecated( ): 
  *
  * @param	array		TypoScript configuration
  * @return	mixed		HTML output.
  * @access   private
  * @version  0.0.2
  * @since    0.0.2
  */
  private function updateXmlFileIsDeprecated( )
  {
      // Get xml file
    $arr_xmlFiles = array( );
    $arr_xmlFiles[0] = $this->cObj->data['tx_flipit_xml_file'];
    
      // RETURN true : there isn't any XML file
    if( empty ( $arr_xmlFiles ) )
    {
      if( $this->b_drs_updateSwfXml )
      {    
        $prompt = 'There isn\'t any XML file.';
        t3lib_div::devlog( '[INFO/SWF+XML] ' . $prompt, $this->extKey, 0 );
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
      if( $this->b_drs_updateSwfXml )
      {    
        $prompt = 'Swf files are newer than the xml file or there isn\'t any xml file.';
        t3lib_div::devlog( '[INFO/SWF+XML] ' . $prompt, $this->extKey, 0 );
      }
      return true;
    }
      // RETURN true  : XML file is deprecated
    
      // Set timestamp
    $this->zz_tstampRecord( );
    
      // RETURN true  : XML file is deprecated
    if( $this->tstampRecord >= $this->tstampXml )
    {
      if( $this->b_drs_updateSwfXml )
      {    
        $prompt = 'Record is newer than the xml file.';
        t3lib_div::devlog( '[INFO/SWF+XML] ' . $prompt, $this->extKey, 0 );
      }
      return true;
    }
      // RETURN true  : XML file is deprecated
    
      // RETURN false : XML file is up to date
    if( $this->b_drs_updateSwfXml )
    {    
      $prompt = 'XML file is up to date.';
      t3lib_div::devlog( '[INFO/SWF+XML] ' . $prompt, $this->extKey, 0 );
    }
    return false;
      // RETURN false : XML file is up to date
  }

  
  
 /**
  * updateXmlFileRenderIt( ): 
  *
  * @return	void
  * @access   private
  * @version  0.0.3
  * @since    0.0.2
  */
  private function updateXmlFileRenderIt( )
  {
    $conf         = $this->conf;
    $table        = $this->table;
    $fieldFiles   = 'tx_flipit_xml_file';
    $fieldTstamp  = $conf['userFunc.']['configuration.']['tables.'][$table . '.']['tstamp'];

      // Get content parameters
    $contentParams = $this->updateXmlFileRenderItParams( );

      // Get pages
    $pages    = implode( "'/>" . PHP_EOL . "  <page src='", ( array ) $this->files['tx_flipit_swf_files'] );
    $pages    = "  <page src='" . $pages . "'/>";
      // Get pages
    
      // pages: replace absolute path with relative path
    $uploads  = t3lib_div::getIndpEnv( 'TYPO3_DOCUMENT_ROOT' ) . DIRECTORY_SEPARATOR;
    $pages    = str_replace( $uploads, null, $pages );
      // pages: replace absolute path with relative path
    
      // Set xml content and write xml file
    $xmlContent = '' .
'<content %contentParams%>
%pages%
</content>';
    $xmlContent = str_replace( '%contentParams%',  $contentParams, $xmlContent );
    $xmlContent = str_replace( '%pages%',          $pages,         $xmlContent );
    $xmlFile    = $this->updateXmlFileRenderItWriteFile( $xmlContent );
      // Set xml content and write xml file
    
      // Update database
    $where = "uid = " . $this->cObj->data['uid'];
    $fields_values = array(
      $fieldTstamp  => $this->tstamp,
      $fieldFiles   => $xmlFile
    );
      // DRS
    if( $this->b_drs_sql || $this->b_drs_updateSwfXml )
    {    
      $prompt = $GLOBALS['TYPO3_DB']->UPDATEquery( $table, $where, $fields_values );
      t3lib_div::devlog( '[INFO/SQL+SWF+XML] ' . $prompt, $this->extKey, 0 );
    }
      // DRS
    $GLOBALS['TYPO3_DB']->exec_UPDATEquery( $table, $where, $fields_values );
      // Update database

      // Update cObj->data
    $this->cObj->data[$fieldTstamp] = $this->tstamp;
    $this->cObj->data[$fieldFiles]  = $xmlFile;

    return;
  }

  
  
 /**
  * updateXmlFileRenderIt( ): 
  *
  * @return	string		$contentParams  : Contente parameters
  * @access   private
  * @version  0.0.3
  * @since    0.0.2
  */
  private function updateXmlFileRenderItParams( )
  {
    $conf = $this->conf;
    
    $contentParams    = null;
    $arrContentParams = array( );
    
      // Content parameters from TypoScript 
    $confXml = $conf['userFunc.']['configuration.']['xml.'];
    
      // FOREACH :  content param from TypoScript
    foreach( array_keys ( ( array ) $confXml ) as $param )
    {
        // CONTINUE : param has an dot
      if( rtrim( $param, '.' ) != $param )
      {
        continue;
      }
        // CONTINUE : param has an dot

      $cObj_name  = $conf['userFunc.']['configuration.']['xml.'][$param];
      $cObj_conf  = $conf['userFunc.']['configuration.']['xml.'][$param . '.'];
      $value      = $this->cObj->cObjGetSingle($cObj_name, $cObj_conf);
      
      $arrContentParams[] = $param . " = '" . $value . "'"; 
      
    }
      // FOREACH :  content param from TypoScript

      // Move array to string
    $contentParams = implode( PHP_EOL . '  ', $arrContentParams );
    $contentParams = PHP_EOL . '  ' . $contentParams;
      // Move array to string

      // RETURN : Content parameters as string
    return $contentParams;
  }

  
  
 /**
  * updateXmlFileRenderItWriteFile( ): 
  *
  * @return	string          $xmlFile  : xml file
  * @access   private
  * @version  0.0.3
  * @since    0.0.3
  */
  private function updateXmlFileRenderItWriteFile( $xmlContent )
  {
      // xml output file
    $xmlFile =  $this->table . '_' . $this->cObj->data['uid'] . '.xml';
      
      // xml output path
    $field   = 'tx_flipit_xml_file';
    $xmlPath = $this->zz_getPath( $field );

      // xml file with path
    $xmlFileWiPath =  $xmlPath . DIRECTORY_SEPARATOR . $xmlFile;
    
      // DIE  : file can't open
    if( ! ( $handle = fopen( $xmlFileWiPath, 'wb' ) ) )
    {
      $prompt = '
        <p>
          ACCESS ERROR: ' . $xmlFileWiPath . ' can not open.<br />
          Please fix the bug!<br />
          TYPO3 extension Flip it!<br />
          Method: ' . __METHOD__ . ' <br />
          Line: ' . __LINE__ . ' 
        </p>
';
      die( $prompt ); 
    }
      // DIE  : file can't open

      // DIE  : file isn't writeable
    if( ! fwrite( $handle, $xmlContent ) )
    {
      $prompt = '
        <p>
          ACCESS ERROR: ' . $xmlFileWiPath . ' is not writeable.<br />
          Please fix the bug!<br />
          TYPO3 extension Flip it!<br />
          Method: ' . __METHOD__ . ' <br />
          Line: ' . __LINE__ . ' 
        </p>
';
      die( $prompt ); 
    }
      // DIE  : file isn't writeable

    fclose( $handle );

      // DRS
    if( $this->b_drs_updateSwfXml )
    {    
      $prompt = $xmlFileWiPath . ' is written.';
      t3lib_div::devlog( '[OK/SWF+XML] ' . $prompt, $this->extKey, -1 );
    }
      // DRS

      // RETURN
    return $xmlFile;
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
      $prompt =  'current record is ' . $this->table. ' ( uid = ' . $this->cObj->data['uid'] . ' )';
      t3lib_div::devlog( '[INFO/DRS] ' . $prompt, $this->extKey, 0 );
    }
      // DRS

      // Init file lists
    $this->initFiles( );
    
      // RETURN : $firstFile != $currentFile
    $arr_return = $this->initIfFirstFileOnly( );
    if( $arr_return['return'] )
    {
      return $arr_return;
    }
      // RETURN : $firstFile != $currentFile

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
    $this->b_drs_updateSwfXml    = true;
    $this->b_drs_todo   = true;
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
  * initIfFirstFileOnly( ): 
  *
  * @return	mixed		HTML output.
  * @access   private
  * @version  0.0.1
  * @since    0.0.1
  */
  private function initIfFirstFileOnly( )
  {
    $conf = $this->conf;

    $arr_return = array( );
    $arr_return['return'] = false;

      // Get first file from media
    $field            = $conf['userFunc.']['configuration.']['tables.'][$this->table . '.']['media'];
    $firstkey         = key( ( array ) $this->files[$field] );
    $firstFileWiPath  = $this->files[$field][$firstkey];
    $pathParts        = pathinfo( $firstFileWiPath );
    $firstFile        = $pathParts['basename'];
      // Get first file from media
  
      // Get current file
    $currentFile = $GLOBALS['TSFE']->register['filename'];
    
      // SWITCH : $firstFile == $currentFile
    switch( true )
    {
      case( $firstFile == $currentFile ):
        if( $this->b_drs_init )
        {
          $prompt = 'The current file is the first file. Flip it! will run.';
          t3lib_div::devlog( '[INFO/INIT] ' . $prompt, $this->extKey, -1 );
        }
        $arr_return['return'] = false;
        break;
      case( $firstFile != $currentFile ):
      default:
        if( $this->b_drs_init )
        {
          $prompt = 'The current file ' . $currentFile . ' isn\'t the first file ' . $firstFile . '. Flip it! won\'t run. This is OK.';
          t3lib_div::devlog( '[INFO/INIT] ' . $prompt, $this->extKey, 0 );
        }
        $arr_return['return']   = true;
        $arr_return['content']  = null;
        break;
    }
      // SWITCH : $firstFile == $currentFile
    
    unset( $firstFile );
    unset( $currentFile );

    return $arr_return;
    
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
        <h2>
          ' . $exec . '
        </h2>
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
      $pathWiFile = $path . DIRECTORY_SEPARATOR . $file;
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
  private function zz_getPath( $field)
  {
      // Get path
    $this->zz_TCAload( $this->table );
    $uploadFolder         = $GLOBALS['TCA'][$this->table]['columns'][$field]['config']['uploadfolder'];
    $typo3_document_root  = t3lib_div::getIndpEnv( 'TYPO3_DOCUMENT_ROOT' );
    $path                 = $typo3_document_root . DIRECTORY_SEPARATOR . $uploadFolder;

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
