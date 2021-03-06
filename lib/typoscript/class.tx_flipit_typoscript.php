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
* @version  1.0.7
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
  * Quality: high, low
  *
  * @internal #45471
  * @var string
  */
  private $quality;

 /**
  * Current table: tt_content, tx_org_downloads
  *
  * @var string
  */
  private $table;

 /**
  * Global tstamp for updates. It must be older than the tstamp of generated files
  *
  * @var integer
  */
  private $tstamp;

 /**
  * Width of the PDF page with the biggest size
  *
  * @var integer
  */
  private $pdfMaxWidth  = null;

 /**
  * Height of the PDF page with the biggest size
  *
  * @var integer
  */
  private $pdfMaxHeight = null;


 /**
  * Backup of cObj->data
  *
  * @var array
  */
  private $bakCObjData = null;
//
// /**
//  * Backup of $GLOBALS['TSFE']->currentRecord
//  *
//  * @var array
//  */
//  private $bakCurrRecord = null;
//
// /**
//  * Backup of $GLOBALS['TSFE']->cObj->data
//  *
//  * @var array
//  */
//  private $bakTsfeData = null;

 /**
  * Current record in table:uid syntax like tt_content:25
  *
  * @var string
  */
  private $currentRecord  = null;

  /**
  * List of required fields. Should corresponds with ext_tables.sql.
  *
  * @var array
  */
  private $arrRequiredFields = array(
    'tstamp',
    'tx_flipit_fancybox',
    'tx_flipit_layout',
    'tx_flipit_swf_files',
    'tx_flipit_updateswfxml',
    'tx_flipit_xml_file'
  );





 /**
  * main( ):
  *
  * @param    string        Content input. Not used, ignore.
  * @param    array        TypoScript configuration
  * @return    mixed        HTML output.
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
      $content = $arr_return['content'];
      if( empty( $content ) )
      {
        $content =  $this->content( $conf );
      }
      $this->cObjDataReset( );
      return $content;
    }
      // IF return  : return with an error prompt


      // RETURN : no media files
    if( empty ( $this->cObj->data[$this->fieldLabelForMedia] ) )
    {
      if( $this->b_drs_flipit )
      {
        $prompt = $this->table . '.' . $field . ' is empty. Nothing todo. Return!';
        t3lib_div::devlog( '[INFO/FLIPIT] ' . $prompt, $this->extKey, 0 );
      }
      $content =  $this->content( $conf );
      $this->cObjDataReset( );
      return;
    }
      // RETURN : no media files

      // ...
    $this->jquery( );

      // Generate and check SWF and XML files
    $this->update( );

      // Return the content
    $content =  $this->content( $conf );
    $this->cObjDataReset( );
    return $content;
  }



  /***********************************************
  *
  * cObjData
  *
  **********************************************/

/**
 * cObjDataSet( ):
 *
 * @return    void
 * @internal  #44896
 * @version 1.0.1
 * @since   1.0.0
 */
  private function cObjDataSet(  )
  {
      // Backup data, which will changed below
    $this->cObjDataBackup( );

// #i0008
//var_dump( __METHOD__, __LINE__, $this->cObj->data['filelink_size'],
//        $this->cObj->data['tx_flipit_layout'],
//        $this->cObj->data['tx_org_downloads.documentssize'] );
//var_dump( __METHOD__, __LINE__, $this->cObj->data['filelink_size'],
//        $this->cObj->data['tx_flipit_layout'],
//        $this->cObj->data['tx_org_downloads.documentssize'] );
      // SWITCH : Set cObj->data
    switch( true )
    {
      case( ! empty ( $GLOBALS['TSFE']->tx_browser_pi1->cObj->data ) ):
          // #i0008, 13-02-06, dwildt, 1-
        //$this->cObj->data = $GLOBALS['TSFE']->tx_browser_pi1->cObj->data;
          // #i0008, 13-02-06, dwildt, 1+
        $this->cObj->data = array_merge( $GLOBALS['TSFE']->tx_browser_pi1->cObj->data, $this->cObj->data );
        break;
      default:
          // Do nothing: $this->cObj->data is set by the TYPO3 core
        break;
    }
      // SWITCH : Set cObj->data

// #i0008
//var_dump( __METHOD__, __LINE__, $this->cObj->data['filelink_size'],
//        $this->cObj->data['tx_flipit_layout'],
//        $this->cObj->data['tx_org_downloads.documentssize'] );
      // Add to the header field
    if( $this->fieldLabelForTitle )
    {
      if( isset( $this->cObj->data[$this->fieldLabelForTitle] ) )
      {
        $this->cObj->data['header'] = $this->cObj->data[$this->fieldLabelForTitle];
        if( $this->b_drs_warn )
        {
          if( empty( $this->cObj->data[$this->fieldLabelForTitle] ) )
          {
            $prompt = 'Title is empty.';
            t3lib_div::devlog( '[WARN/FLIPIT] ' . $prompt, $this->extKey, 2 );
            $prompt = 'Value of the title field in the Constant Editor is ' . $this->fieldLabelForTitle . '. Is this proper?';
            t3lib_div::devlog( '[INFO/FLIPIT] ' . $prompt, $this->extKey, 1 );
          }
        }
      }
    }
      // Add to the header field

      // FOREACH  : Add all fields of the current table with table.field syntax without table
    foreach( array_keys( $this->cObj->data ) as $tableField )
    {
      list( $table, $field ) = explode( '.', $tableField );
      if( $table != $this->table )
      {
        continue;
      }
      $this->cObj->data[$field] = $this->cObj->data[$tableField];
    }
      // FOREACH  : Add all fields of the current table with table.field syntax without table
//    $GLOBALS['TSFE']->cObj->data = $this->cObj->data;

      // DRS
    if( $this->b_drs_init )
    {
      $prompt = 'cObj->data are set (overriden).';
      t3lib_div::devlog( '[INFO/INIT] ' . $prompt, $this->extKey, 0 );
      $prompt = var_export( $this->cObj->data, true );
      $prompt = str_replace( ',', ', ', $prompt );
      $prompt = str_replace( ',  ', ', ', $prompt );
      t3lib_div::devlog( '[INFO/INIT] ' . $prompt, $this->extKey, 0 );
    }
      // DRS

      // SWITCH : set current record in table:uid syntax
    switch( true )
    {
      case( ! empty ( $GLOBALS['TSFE']->tx_browser_pi1->currentRecord ) ):
        $this->currentRecord = $GLOBALS['TSFE']->tx_browser_pi1->currentRecord;
        break;
      default:
        $this->currentRecord = $GLOBALS['TSFE']->currentRecord;
        break;
    }
      // SWITCH : set current record in table:uid syntax

      // DRS
    if( $this->b_drs_init )
    {
      $prompt = 'current record is: ' . $this->currentRecord;
      t3lib_div::devlog( '[INFO/INIT] ' . $prompt, $this->extKey, 0 );
    }
      // DRS
  }

/**
 * cObjDataBackup( ):
 *
 * @return    void
 * @internal  #44896
 * @version 1.0.1
 * @since   1.0.0
 */
  private function cObjDataBackup(  )
  {
    if( ! ( $this->bakCObjData === null ) )
    {
      return;
    }
//  // #44858
//$pos = strpos( '87.177.65.251', t3lib_div :: getIndpEnv( 'REMOTE_ADDR' ) );
//if ( ! ( $pos === false ) )
//{
//  echo '<pre>';
//  var_dump( __METHOD__, __LINE__, $GLOBALS['TSFE']->currentRecord );
//  echo '</pre>' . PHP_EOL;
//}
    $this->bakCObjData    = $this->cObj->data;
//    $this->bakCurrRecord  = $GLOBALS['TSFE']->currentRecord;
//    $this->bakTsfeData = $GLOBALS['TSFE']->cObj->data;
  }

/**
 * cObjDataReset( ):
 *
 * @return    void
 * @internal  #44896
 * @version 1.0.0
 * @since   1.0.0
 */
  private function cObjDataReset( )
  {
    if( $this->bakCObjData === null )
    {
      return;
    }
    $this->cObj->data               = $this->bakCObjData;
//    $GLOBALS['TSFE']->currentRecord = $this->bakCurrRecord;
//    $GLOBALS['TSFE']->cObj->data    = $this->bakTsfeData;

      // DRS
    if( $this->b_drs_init )
    {
      $prompt = 'cObj->data are reset.';
      t3lib_div::devlog( '[INFO/INIT] ' . $prompt, $this->extKey, 0 );
    }
      // DRS
  }



  /***********************************************
  *
  * Content
  *
  **********************************************/

 /**
  * content( ):
  *
  * @param    array        TypoScript configuration
  * @return    mixed        HTML output.
  * @access private
  * @version 0.0.1
  * @since 0.0.1
  */
  private function content( )
  {
    $conf = $this->conf;

    $coa_name = $conf['userFunc.']['content'];
    $coa_conf = $conf['userFunc.']['content.'];
    $content  = $this->zz_cObjGetSingle( $coa_name, $coa_conf );

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
  * @param    array        TypoScript configuration
  * @return    mixed        HTML output.
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
  * @return    boolean
  * @access   private
  * @version  0.0.3
  * @since    0.0.3
  */
  private function updateEnabled( )
  {
    $conf = $this->conf;

    $coa_name = $conf['userFunc.']['constant_editor.']['configuration.']['updateSwfXml'];
    $coa_conf = $conf['userFunc.']['constant_editor.']['configuration.']['updateSwfXml.'];
    $updateSwfXml  = $this->zz_cObjGetSingle( $coa_name, $coa_conf );

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
      case( null ):
      case( 'disabled' ):
        if( $this->b_drs_init )
        {
          $prompt = 'Auto-update of SWF files and XML files is disabled.';
          t3lib_div::devlog( '[INFO/INIT] ' . $prompt, $this->extKey, 2 );
        }
        return false;
        break;
      case( 'error' ):
      default:
        if( $this->b_drs_init )
        {
          $prompt = 'Undefined: ' . "['userFunc.']['constant_editor.']['configuration.']['updateSwfXml']" .
                    ' is ' . $updateSwfXml;
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
  * @param    array        TypoScript configuration
  * @return    mixed        HTML output.
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
    $table        = $this->table;
    $fieldFiles   = 'tx_flipit_swf_files';

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
      $this->fieldLabelForTstamp  => $this->tstamp,
      $fieldFiles                 => null
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
    $this->cObj->data[$this->fieldLabelForTstamp] = $this->tstamp;
    $this->cObj->data[$fieldFiles]                = null;

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
    $table        = $this->table;
    $fieldFiles   = 'tx_flipit_swf_files';

      // filesCounter is needed for unique filenames
    $filesCounter = 0;

      // Remove all swfFiles
    $this->updateSwfFilesRemove( );

      // SWITCH : extension
      // jpeg, pdf, png
    $swfFiles = array( );

      // FOREACH  : file
    foreach( $this->files[$this->fieldLabelForMedia] as $fileWiPath )
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
      if( ! ( empty ( $swfFiles ) ) )
      {
        $prompt = 'Rendered SWF files: ' . var_export( $swfFiles, true );
        t3lib_div::devlog( '[OK/SWF+XML] ' . $prompt, $this->extKey, -1 );
      }
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
      $this->fieldLabelForTstamp  => $this->tstamp,
      $fieldFiles                 => implode( ',', $swfFiles )
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
    $this->cObj->data[$this->fieldLabelForTstamp] = $this->tstamp;
    $this->cObj->data[$fieldFiles]                = implode( ',', $swfFiles );

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

    unset( $filesCounter );
    return $arrReturn;
  }



 /**
  * updateSwfFilesRenderPdf( ):
  *
  * @param    string    $fileWiPath : full path
  * @return   array     $arrReturn  : rendered swf files
  * @access   private
  * @version  1.0.8
  * @since    0.0.3
  */
  private function updateSwfFilesRenderPdf( $pdffileWiPath, $filesCounter )
  {
    $arrReturn  = null;
      // #45712, 130221, dwildt, 1+
    $params     = null;
      // #45763, 130222, dwildt, 1+
    $tstamp     = time( );

    $pathToSwftools = $this->objUserfunc->pathToSwfTools;

      // SWF output file
      // #45763, 130222, dwildt
    $swfFile =  $this->table . '_' . $this->cObj->data['uid'] .
                '_doc_' . $filesCounter . '_part_%_' . $tstamp . '.swf';
    $field   = 'tx_flipit_swf_files';
    $swfPath = $this->zz_getPath( $field );
    $swfPathToFile = $swfPath . '/' . $swfFile;

    switch( true )
    {
      case( $this->objUserfunc->os == 'windows' ):
        $pdffileWiPath = str_replace('/', '\\\\', $pdffileWiPath );
        $swfPathToFile = str_replace('/', '\\', $swfPathToFile );
        break;
      default:
        break;
    }

      // #45712, 130221, dwildt, 1+
    $params = $this->updateSwfFilesRenderPdfSetParams( );

      // #45170, 130205, dwildt
      // Set the PDF info array
    $this->updateSwfFilesRenderPdfSetInfo( $pdffileWiPath, $params );

      // Render PDF to SWF
    switch( true )
    {
      case( $this->objUserfunc->os == 'windows' ):
          // #45471, 130214, dwildt, 1-
//        $exec   = '"'. $pathToSwftools . 'pdf2swf.exe" ' . $pdffileWiPath . ' ' . $swfPathToFile;
          // #45471, 130214, dwildt, 1+
        $exec   = '"'. $pathToSwftools . 'pdf2swf.exe" ' . $params . $pdffileWiPath . ' ' . $swfPathToFile;
        break;
      default:
          // #45471, 130214, dwildt, 1-
//        $exec   = 'pdf2swf ' . $pdffileWiPath . ' ' . $swfPathToFile;
          // #45471, 130214, dwildt, 1+
        $exec   = 'pdf2swf ' . $params . $pdffileWiPath . ' ' . $swfPathToFile;
        break;
    }
    $pdf2swfReport = $this->zz_exec( $exec );
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

      // get list of ordered rendered swf files
      // #45763, 130222, dwildt
    $swfFiles =  $this->table . '_' . $this->cObj->data['uid'] .
                '_doc_' . $filesCounter . '_part_*_' . $tstamp . '.swf';
    $swfPathToFiles = $swfPath . '/' . $swfFiles;

      // order swf files by time
    switch( true )
    {
      case( $this->objUserfunc->os == 'windows' ):
        $swfPathToFiles = str_replace('/', '\\', $swfPathToFiles );
          // #45470, 130213, dwildt
        $exec   = 'dir /OD '. $swfPathToFiles . ' /s';
        break;
      default:
        $exec   = 'ls -t ' . $swfPathToFiles;
        break;
    }
      // order swf files by time

    $lines  = $this->zz_exec( $exec );
      // get list of ordered rendered swf files

      // 130117, dwildt
    foreach( $lines as $key => $line )
    {
        // CONTINUE : remove line without an ending ".swf"
      $pos = strpos( $line, '.swf' );
      if( $pos === false )
      {
        unset( $lines[$key] );
        continue;
      }
        // CONTINUE : remove line without an ending ".swf"

      $arrLine = explode( ' ', $line );
      $lines[$key] = $arrLine[ count( $arrLine ) - 1 ];
    }

      // OS depending ordering
    switch( true )
    {
      case( $this->objUserfunc->os == 'windows' ):
          // No need for ordering
        break;
      default:
          // list of swf files ordered by time ascending
        krsort( $lines );
        break;
    }
      // OS depending ordering

      // FOREACH  : swfFile
    foreach( $lines as $swfFileWiPath )
    {
      $pathParts    = pathinfo( $swfFileWiPath );
      $arrReturn[]  = $pathParts['basename'];

    }
      // FOREACH  : swfFile

      // #45712, 130221, dwildt, 1+
    $this->updateSwfFilesRenderPdfUnproperAsBitmap( $pdf2swfReport, $pathToSwftools, $params, $pdffileWiPath, $swfPathToFile );

      // #45712, 130221, dwildt, 1+
    $this->updateSwfFilesRenderPdfPagelistAsBitmap( $pathToSwftools, $params, $pdffileWiPath, $swfPathToFile );

      // RETURN : swf files without path
    return $arrReturn;
  }



 /**
  * updateSwfFilesRenderPdfPagelistAsBitmap( ):
  *
  * @param    array    $pdf2swfReport
  * @return   void
  * @internal #45712
  * @access   private
  * @version  1.0.9
  * @since    1.0.9
  */
  private function updateSwfFilesRenderPdfPagelistAsBitmap( $pathToSwftools, $params, $pdffileWiPath, $swfPathToFile )
  {
    $pdf2swfReport  = null;
    $csvPageList    = $this->cObj->data['tx_flipit_pagelist'];

      // RETURN : tx_flipit_pagelist is empty
    if( empty ( $csvPageList ) )
    {
        // DRS
      if( $this->b_drs_pdf )
      {
        $prompt = 'tx_flipit_pagelist is empty. Any page won\'t rendered as bitmap.';
        t3lib_div::devlog( '[INFO/PDF] ' . $prompt, $this->extKey, 0 );
      }
      return;
    }
      // RETURN : tx_flipit_pagelist is empty

      // DRS
    if( $this->b_drs_pdf )
    {
      $prompt = 'tx_flipit_pagelist is ' . $csvPageList . '. These pages will rendered as bitmap.';
      t3lib_div::devlog( '[INFO/PDF] ' . $prompt, $this->extKey, 0 );
    }
      // DRS

      // Render PDF to bitmap SWF
    $params = $params . '--set bitmap ';
    $pages  = explode( ',', $csvPageList );
    foreach( $pages  as $page )
    {
      $currParams = '--pages ' . trim( $page ) . ' ' . $params;
      switch( true )
      {
        case( $this->objUserfunc->os == 'windows' ):
          $exec   = '"'. $pathToSwftools . 'pdf2swf.exe" ' . $currParams . $pdffileWiPath . ' ' . $swfPathToFile;
          break;
        default:
          $exec   = 'pdf2swf ' . $currParams . $pdffileWiPath . ' ' . $swfPathToFile;
          break;
      }
      $pdf2swfReport = $this->zz_exec( $exec );
    }
    unset( $pdf2swfReport );
      // Render PDF to bitmap SWF

  }



 /**
  * updateSwfFilesRenderPdfUnproperAsBitmap( ): PDF pages, which contains
  *                                               //* forms
  *                                               * shaded fills
  *                                               //* transparency groups
  *                                             should rendered as bitmap swf
  *
  * @param    array    $pdf2swfReport
  * @return   void
  * @internal #45712
  * @access   private
  * @version  1.0.9
  * @since    1.0.9
  */
  private function updateSwfFilesRenderPdfUnproperAsBitmap( $pdf2swfReport, $pathToSwftools, $params, $pdffileWiPath, $swfPathToFile )
  {
    $strPdf2swfReport = implode( null, $pdf2swfReport );

    switch( true )
    {
//      case( strpos( $strPdf2swfReport, 'forms' ) ):
      case( strpos( $strPdf2swfReport, 'shaded fills' ) ):
//      case( strpos( $strPdf2swfReport, 'transparency groups' ) ):
          // DRS
        if( $this->b_drs_warn )
        {
//          $prompt = 'SWF files are unproper: They contain forms, shaded fills or transparency groups.';
          $prompt = 'SWF files are unproper: They contain shaded fills.';
          t3lib_div::devlog( '[WARN/PDF] ' . $prompt, $this->extKey, 2 );
        }
          // DRS
        break;
      default:
          // RETURN : PDF file doesn't contain shaded fills
          // DRS
        if( $this->b_drs_pdf )
        {
//          $prompt = 'SWF files are proper: They don\'t contain forms, shaded fills or transparency groups.';
          $prompt = 'SWF files are proper: They don\'t contain shaded fills.';
          t3lib_div::devlog( '[OK/PDF] ' . $prompt, $this->extKey, -1 );
        }
          // DRS
        return;
        break;
          // RETURN : PDF file doesn't contain shaded fills
    }
    unset( $strPdf2swfReport );


    $pageCounter        = 0;
    $pagesWiShadedFills = null;

    foreach( $pdf2swfReport as $line )
    {
      if( strpos( $line, 'processing PDF page' ) )
      {
        $pageCounter = $pageCounter + 1;
      }
      switch( true )
      {
//        case( strpos( $line, 'forms' ) ):
        case( strpos( $line, 'shaded fills' ) ):
//        case( strpos( $line, 'transparency groups' ) ):
          $pagesWiShadedFills[] = $pageCounter;
          break;
        default:
          // Do nothing
          break;
      }
    }
    $pagesWiShadedFills = array_unique( ( array) $pagesWiShadedFills );

      // DRS
    if( $this->b_drs_warn )
    {
//      $prompt = 'pages with forms, shaded fills or transparency groups are ' . implode( ', ', ( array ) $pagesWiShadedFills );
      $prompt = 'pages with shaded fills are ' . implode( ', ', ( array ) $pagesWiShadedFills );
      t3lib_div::devlog( '[WARN/PDF] ' . $prompt, $this->extKey, 2 );
      $prompt = 'pages will rendered as bitmap SWF files.';
      t3lib_div::devlog( '[WARN/PDF] ' . $prompt, $this->extKey, 2 );
      $prompt = 'You can avoid this effect: save your PDF in version 1.3 and upload it!';
      t3lib_div::devlog( '[HELP/PDF] ' . $prompt, $this->extKey, 1 );
    }
      // DRS

      // Render PDF to bitmap SWF
    $params = $params . '--set bitmap ';
    foreach( ( array ) $pagesWiShadedFills as $page )
    {
      $currParams = '--pages ' . $page . ' ' . $params;
      switch( true )
      {
        case( $this->objUserfunc->os == 'windows' ):
          $exec   = '"'. $pathToSwftools . 'pdf2swf.exe" ' . $currParams . $pdffileWiPath . ' ' . $swfPathToFile;
          break;
        default:
          $exec   = 'pdf2swf ' . $currParams . $pdffileWiPath . ' ' . $swfPathToFile;
          break;
      }
      $pdf2swfReport = $this->zz_exec( $exec );
    }
      // Render PDF to bitmap SWF

  }



 /**
  * updateSwfFilesRenderPdfSetInfo( )
  *
  * @param    string    $pdfFileWiPath : full path
  * @param    string    $params
  * @return   array     $arrReturn  : rendered swf files
  * @internal #45170, #45712
  * @access   private
  * @version  1.0.9
  * @since    1.0.9
  */
  private function updateSwfFilesRenderPdfSetInfo( $pdffileWiPath, $params )
  {
    $pathToSwftools = $this->objUserfunc->pathToSwfTools;

      // SWITCH : OS
    switch( true )
    {
      case( $this->objUserfunc->os == 'windows' ):
        $exec   = '"'. $pathToSwftools . 'pdf2swf.exe" -I ' . $params . $pdffileWiPath;
        break;
      default:
        $exec   = 'pdf2swf -I ' . $params . $pdffileWiPath;
        break;
    }
      // SWITCH : OS

      // exec( pdf2swf -I /home/www/htdocs/www.typo3-browser-forum.de/typo3/uploads/media/manual.pdf )
    $lines  = $this->zz_exec( $exec );

      // $lines:
      //    page = 1 width = 595.00 height = 842.00
      //    page = 2 width = 595.00 height = 842.00
      //    ...
      //    page = 14 width = 595.00 height = 842.00

    $this->updateSwfFilesRenderPdfSetMaxSize( $lines );

  }



 /**
  * updateSwfFilesRenderPdfSetParams( )
  *
  * @return   string     $params
  * @internal #45712
  * @access   private
  * @version  1.0.9
  * @since    1.0.9
  */
  private function updateSwfFilesRenderPdfSetParams( )
  {
    $params = null;

      // #45471, 130214, dwildt, 1+
    $paramBitmap = $this->updateSwfFilesRenderPdfSetParamsBitmap( );
      // #45712, 130221, dwildt, 2+
    $paramZoom  = $this->updateSwfFilesRenderPdfSetParamsDpi( );
    $params     = $paramBitmap . $paramZoom;

    return $params;
  }



 /**
  * updateSwfFilesRenderPdfSetParamsBitmap( )
  *
  * @return   string     $param
  * @internal #45171
  * @access   private
  * @version  1.0.8
  * @since    1.0.8
  */
  private function updateSwfFilesRenderPdfSetParamsBitmap( )
  {
    $param = null;

      // SWITCH : $quality
    switch( true )
    {
      case( $this->quality == 'low' ):
        $param = '--set bitmap ';
        return $param;
        break;
      case( empty( $this->quality ) ):
      default:
        return $param;
        break;
    }
      // SWITCH : $quality
  }



 /**
  * updateSwfFilesRenderPdfSetParamsDpi( )
  *
  * @return   string     $param
  * @internal #45712
  * @access   private
  * @version  1.0.9
  * @since    1.0.9
  */
  private function updateSwfFilesRenderPdfSetParamsDpi( )
  {
    $conf   = $this->conf;
    $param  = null;

    $coa_name = $conf['userFunc.']['constant_editor.']['configuration.']['dpi'];
    $coa_conf = $conf['userFunc.']['constant_editor.']['configuration.']['dpi.'];
    $dpi      = ( int ) $this->zz_cObjGetSingle( $coa_name, $coa_conf );

    if( ! $dpi )
    {
      $dpi = 144;
    }

    $param = '--set zoom=' . $dpi . ' ';
    return $param;
  }



 /**
  * updateSwfFilesRenderPdfSetMaxSize( $pages )
  *
  * @param    array    $pages : Array with informationen for each PDF page
  * @internal #45170
  * @access   private
  * @version  1.0.4
  * @since    1.0.4
  */
  private function updateSwfFilesRenderPdfSetMaxSize( $pages )
  {
    $infos    = array( );
    $counter  = 0;

      // LOOP : each PDF page
    foreach( ( array ) $pages as $page )
    {
        // LOOP : elements
      $elements = explode( ' ', $page );
      foreach( $elements as $element )
      {
        list( $key, $value ) = explode( '=', $element );
        $infos[$counter][$key] = $value;
      }
        // LOOP : elements

        // SWITCH : width and height
      switch( true )
      {
        case( ( int) $infos[$counter]['width'] > $this->pdfMaxWidth ):
          $this->pdfMaxWidth  = ( int ) $infos[$counter]['width'];
          break;
        case( ( int) $infos[$counter]['height'] > $this->pdfMaxHeight ):
          $this->pdfMaxHeight = ( int ) $infos[$counter]['height'];
          break;
        default:
            // Do nothing
          break;
      }
        // SWITCH : width and height

      $counter = $counter + 1;
    }
      // LOOP : each PDF page

      // DRS  : PDF info
    if( $this->b_drs_error )
    {
      switch( true )
      {
        case( $this->pdfMaxWidth <= 0 ):
        case( $this->pdfMaxHeight <= 0 ):
          $prompt = 'Size of PDF page is null!';
          t3lib_div::devlog( '[WARN/SWF+XML] ' . $prompt, $this->extKey, 2 );
          break;
      }
    }
      // DRS  : PDF info
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

    unset( $filesCounter );
    return $arrReturn;
  }



 /**
  * updateXml( ):
  *
  * @param    array        TypoScript configuration
  * @return    mixed        HTML output.
  * @access   private
  * @version  0.0.2
  * @since    0.0.2
  */
  private function updateXml( )
  {
    $xmlFileIsDeprecated = false;

      // Render SWF files if they are deprecated or if there isn't any SWF file
    $xmlFileIsDeprecated = $this->updateXmlFileIsDeprecated( );
    if( $xmlFileIsDeprecated )
    {
      $this->updateXmlFileRenderIt( );
    }
      // Render SWF files if they are deprecated or if there isn't any SWF file
  }



 /**
  * updateXmlFileIsDeprecated( ):
  *
  * @param    array        TypoScript configuration
  * @return    mixed        HTML output.
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
  * @return    void
  * @access   private
  * @version  0.0.3
  * @since    0.0.2
  */
  private function updateXmlFileRenderIt( )
  {
    $table        = $this->table;
    $fieldFiles   = 'tx_flipit_xml_file';

      // Get content parameters
    $contentParams = $this->updateXmlFileRenderItParams( );

      // Get pages
    $pages    = implode( "'/>" . PHP_EOL . "  <page src='", ( array ) $this->files['tx_flipit_swf_files'] );
    $pages    = "  <page src='" . $pages . "'/>";
      // Get pages

      // pages: replace absolute path with relative path
    $uploads  = t3lib_div::getIndpEnv( 'TYPO3_DOCUMENT_ROOT' ) . '/';
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
      $this->fieldLabelForTstamp  => $this->tstamp,
      $fieldFiles                 => $xmlFile
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
    $this->cObj->data[$this->fieldLabelForTstamp] = $this->tstamp;
    $this->cObj->data[$fieldFiles]                = $xmlFile;

    return;
  }



 /**
  * updateXmlFileRenderIt( ):
  *
  * @return    string        $contentParams  : Contente parameters
  * @access   private
  * @version  1.0.8
  * @since    0.0.2
  */
  private function updateXmlFileRenderItParams( )
  {
    $conf = $this->conf;

    $contentParams    = null;
    $arrContentParams = array( );

      // Content parameters from TypoScript
    $confXml = $conf['userFunc.']['constant_editor.']['xml.'];

      // FOREACH :  content param from TypoScript
    foreach( array_keys ( ( array ) $confXml ) as $param )
    {
        // CONTINUE : param has an dot
      if( rtrim( $param, '.' ) != $param )
      {
        continue;
      }
        // CONTINUE : param has an dot

      $cObj_name  = $conf['userFunc.']['constant_editor.']['xml.'][$param];
      $cObj_conf  = $conf['userFunc.']['constant_editor.']['xml.'][$param . '.'];
      $value      = $this->zz_cObjGetSingle( $cObj_name, $cObj_conf );
      //$value      = $this->cObj->cObjGetSingle($cObj_name, $cObj_conf);

      $arrContentParams[$param] = $param . " = '" . $value . "'";

    }
      // FOREACH :  content param from TypoScript

      // #45170, 130205, dwildt, +
      // Override document size
    switch( true )
    {
      case( $this->pdfMaxWidth > 0 ):
      case( $this->pdfMaxHeight > 0 ):
        $arrContentParams['width']  = "width='" . $this->pdfMaxWidth . "'";
        $arrContentParams['height'] = "height='" . $this->pdfMaxHeight . "'";
          // DRS
        if( $this->b_drs_updateSwfXml )
        {
          $prompt = 'Size of SWF files is not the default and is overriden to ' . $this->pdfMaxWidth . 'x' . $this->pdfMaxHeight . ' points.';
          t3lib_div::devlog( '[INFO/SWF+XML] ' . $prompt, $this->extKey, 0 );
        }
          // DRS
        break;
    }
      // Override document size

      // #45712, 130221, dwildt, -
//      // #45471, 130214, dwildt, +
//      // Double document size
//    switch( true )
//    {
//      case( $this->quality == 'low' ):
//        list( $param, $width )  = explode('=', $arrContentParams['width'] );
//        $width          = trim( $width, "'" );
//        list( $param, $height ) = explode('=', $arrContentParams['height'] );
//        $height         = trim( $height, "'" );
//        unset( $param );
//
//          // DRS
//        if( $this->b_drs_updateSwfXml )
//        {
//          $prompt = 'Quality is low. Document width will set from ' . $width . ' pts to ' . ( $width * 2 ) . ' pts.';
//          t3lib_div::devlog( '[INFO/SWF+XML] ' . $prompt, $this->extKey, 0 );
//          $prompt = 'Quality is low. Document height will set from ' . $height . ' pts to ' . ( $height * 2 ) . ' pts.';
//          t3lib_div::devlog( '[INFO/SWF+XML] ' . $prompt, $this->extKey, 0 );
//        }
//          // DRS
//
//        $arrContentParams['width']  = "width='" . ( $width * 2 ) . "'";
//        $arrContentParams['height'] = "height='" . ( $height * 2 ) . "'";
//        break;
//    }
//      // Double document size
      // #45712, 130221, dwildt, -

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
  * @return    string          $xmlFile  : xml file
  * @access   private
  * @version  0.0.3
  * @since    0.0.3
  */
  private function updateXmlFileRenderItWriteFile( $xmlContent )
  {
      // #46991, 130912, dwildt, 2-
      // xml output file
    //$xmlFile =  $this->table . '_' . $this->cObj->data['uid'] . '.xml';

      // #46991, 130912, dwildt, 3+
      // xml output file
    $tstamp  = time( );
    $xmlFile =  $this->table . '_' . $this->cObj->data['uid'] . '_' . $tstamp . '.xml';

      // xml output path
    $field   = 'tx_flipit_xml_file';
    $xmlPath = $this->zz_getPath( $field );

      // xml file with path
    $xmlFileWiPath =  $xmlPath . '/' . $xmlFile;

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
 * drs_debugTrail( ): Returns class, method and line of the call of this method.
 *                    The calling method is a debug method - if it is called by another
 *                    method, please set the level in the calling method to 2.
 *
 * @param    integer        $level: ...
 * @return    array        $arr_return : with elements class, method, line and prompt
 * @internal  #45174
 * @version 1.0.4
 * @since   1.0.4
 */
  public function drs_debugTrail( $level = 1 )
  {
    $arr_return = null;

      // Get the debug trail
    $debugTrail_str = t3lib_utility_Debug::debugTrail( );

      // Get debug trail elements
    $debugTrail_arr = explode( '//', $debugTrail_str );

      // Get class, method
    $classMethodLine        = $debugTrail_arr[ count( $debugTrail_arr) - ( $level + 2 )];
    list( $classMethod )    = explode ( '#', $classMethodLine );
    list($class, $method )  = explode( '->', $classMethod );
      // Get class, method

      // Get line
    $classMethodLine      = $debugTrail_arr[ count( $debugTrail_arr) - ( $level + 1 )];
    list( $dummy, $line ) = explode ( '#', $classMethodLine );
    unset( $dummy );
      // Get line

      // RETURN content
    $arr_return['class']  = trim( $class );
    $arr_return['method'] = trim( $method );
    $arr_return['line']   = trim( $line );
    $arr_return['prompt'] = $arr_return['class'] . '::' . $arr_return['method'] . ' (' . $arr_return['line'] . ')';

    return $arr_return;
      // RETURN content
  }



 /**
  * init( ):
  *
  * @return    mixed        HTML output.
  * @access private
  * @version 1.0.0
  * @since 0.0.1
  */
  private function  init( )
  {
      //  #44896, 130129, dwildt, 1-
//    $conf = $this->conf;

      // Init extension configuration array
    $this->arr_extConf = unserialize( $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey] );

      // Init the DRS
    $this->initDrs( );

      // Init table
    $this->initTable( );

      // Init field labels
    $this->initFieldLabels( );

      // Init table
    $this->initCObj( );

      // RETURN :
    $arr_return = $this->initRequiredFields( );
    if( $arr_return['return'] )
    {
      return $arr_return;
    }
      // RETURN :

      // RETURN :
    $arr_return = $this->initLayout( );
    if( $arr_return['return'] )
    {
      return $arr_return;
    }
      // RETURN :

      // #45471, 130214, dwildt, 1+
    $this->initQuality( );

      // Require class userfunc
    $this->initClasses( );

      //  #44896, 130129, dwildt, 2-
//      // Init global vars
//    $this->table = $conf['userFunc.']['constant_editor.']['database.']['table'];
      // Global tstamp for updates. It must be older than the tstamp of generated files
    $this->tstamp = time( );


      // DRS
    if( $this->b_drs_flipit )
    {
      $prompt =  'current record is ' . $this->table . ' ( uid = ' . $this->cObj->data['uid'] . ' )';
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
 * @return    void
 * @access private
 */
  private function initClasses( )
  {
      // Include class userfunc
    // #47370, 140916, dwildt, 3-
//    $typo3_document_root  = t3lib_div::getIndpEnv( 'TYPO3_DOCUMENT_ROOT' );
//    $pathToUserfunc       = $typo3_document_root . '/typo3conf/ext/flipit/lib/userfunc/class.tx_flipit_userfunc.php';
//    require_once( $pathToUserfunc );
    // #47370, 140916, dwildt, 1+
    require_once (t3lib_extMgm::extPath( 'flipit' ) . 'lib/userfunc/class.tx_flipit_userfunc.php');
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
  * initCObj( ):
  *
  * @return    mixed        HTML output.
  * @access   private
  * @version  1.0.1
  * @since    1.0.1
  */
  private function initCObj( )
  {

// #44858
//$pos = strpos( '87.177.65.251', t3lib_div :: getIndpEnv( 'REMOTE_ADDR' ) );
//if ( ! ( $pos === false ) )
//{
//  echo '<pre>';
//  var_dump( __METHOD__, __LINE__, $this->table );
//  var_dump( __METHOD__, __LINE__, $GLOBALS['TSFE']->cObj->data );
//  var_dump( __METHOD__, __LINE__, $GLOBALS['TSFE']->currentRecord );
//  echo '</pre>';
//}
    switch( $this->table )
    {
//      case( 'tt_content' ):
//          // Do nothing
//        break;
      default:
        $this->cObjDataSet( );
        break;
    }
  }

/**
 * initDrs( ): Init the DRS - Development Reportinmg System
 *
 * @return    void
 * @access private
 */
  private function initDrs( )
  {

      // Enable the DRS by TypoScript
    switch( $this->arr_extConf['debuggingDrs'] )
    {
      case( 'Disabled' ):
      case( null ):
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

    $this->b_drs_error        = true;
    $this->b_drs_warn         = true;
    $this->b_drs_info         = true;
    $this->b_drs_ok           = true;
    $this->b_drs_exec         = true;
    $this->b_drs_flipit       = true;
    $this->b_drs_init         = true;
    $this->b_drs_jquery       = true;
    $this->b_drs_pdf          = true;
    $this->b_drs_sql          = true;
    $this->b_drs_updateSwfXml = true;
    $this->b_drs_todo         = true;
    $prompt = 'The DRS - Development Reporting System is enabled: ' . $this->arr_extConf['debuggingDrs'];
    t3lib_div::devlog( '[INFO/DRS] ' . $prompt, $this->extKey, 0 );
  }



 /**
  * initFieldLables( ):
  *
  * @return
  * @access   private
  * @version  0.0.3
  * @since    0.0.3
  */
  private function initFieldLabels( )
  {
    $conf = $this->conf;

      // #i0007
    switch( $this->table )
    {
      case( 'tt_content' ):
        $this->fieldLabelForMedia   = 'media';
        $this->fieldLabelForTitle   = 'header';
        break;
      default:
        $this->fieldLabelForMedia   = $conf['userFunc.']['constant_editor.']['database.']['fields.']['media'];
        $this->fieldLabelForTitle   = $conf['userFunc.']['constant_editor.']['database.']['fields.']['title'];
        break;
    }
    $this->fieldLabelForTstamp  = $GLOBALS['TCA'][$this->table]['ctrl']['tstamp'];
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
      // Get files from media
    $field    = $this->fieldLabelForMedia;
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
  * @return    mixed        HTML output.
  * @access   private
  * @version  0.0.1
  * @since    0.0.1
  */
  private function initIfFirstFileOnly( )
  {
    $arr_return = array( );
    $arr_return['return'] = false;

      // Get first file from media
    $field            = $this->fieldLabelForMedia;
    $firstkey         = key( ( array ) $this->files[$field] );
    $firstFileWiPath  = $this->files[$field][$firstkey];
//var_dump( __METHOD__, __LINE__, $this->files, $GLOBALS['TSFE']->register );

      // RETURN : There isn't any file
    if( empty( $firstFileWiPath ) )
    {
      if( $this->b_drs_init )
      {
        $prompt = 'There isn\'t any file. Flip it! won\'t run. This is OK.';
        t3lib_div::devlog( '[INFO/INIT] ' . $prompt, $this->extKey, 0 );
      }
      $arr_return['return']   = true;
      $arr_return['content']  = null;
      return $arr_return;
    }
      // RETURN : There isn't any file

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
          t3lib_div::devlog( '[OK/INIT] ' . $prompt, $this->extKey, -1 );
        }
        $arr_return['return'] = false;
        break;
      case( $firstFile != $currentFile ):
      default:
        if( $this->b_drs_init )
        {
          $prompt = 'The current file ' . $currentFile . ' isn\'t the first file ' . $firstFile . '. ' .
                    'Flip it! won\'t run. This is OK.';
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
  * initLayout( ):
  *
  * @return    mixed        HTML output.
  * @access   private
  * @version  0.0.3
  * @since    0.0.3
  */
  private function initLayout( )
  {
    $conf = $this->conf;

    $arr_return = array( );
    $arr_return['content']  = null;
    $arr_return['return']   = false;

    $coa_name = $conf['userFunc.']['drs.']['layout'];
    $coa_conf = $conf['userFunc.']['drs.']['layout.'];
    $layout   = $this->zz_cObjGetSingle( $coa_name, $coa_conf );

    switch( $layout )
    {
      case( 'layout_00' ):
        if( $this->b_drs_init )
        {
          $prompt = 'Current layout: ' . $layout;
          t3lib_div::devlog( '[INFO/INIT] ' . $prompt, $this->extKey, 0 );
          $prompt = 'Current layout is the tt_content.uploads.20 default. Flip it! won\'t processed.';
          t3lib_div::devlog( '[INFO/INIT] ' . $prompt, $this->extKey, 0 );
        }
        $arr_return['return'] = true;
        break;
      case( 'layout_01' ):
      case( 'layout_02' ):
      case( 'layout_03' ):
      case( 'layout_04' ):
      default:
        if( $this->b_drs_init )
        {
          $prompt = 'Current layout: ' . $layout;
          t3lib_div::devlog( '[INFO/INIT] ' . $prompt, $this->extKey, 0 );
        }
        $arr_return['return'] = false;
        break;
    }

    return $arr_return;

  }



 /**
  * initQuality( ):
  *
  * @return   void
  * @access   private
  * @internal #45471
  * @version  1.0.8
  * @since    1.0.8
  */
  private function initQuality( )
  {
    $conf = $this->conf;

    $coa_name = $conf['userFunc.']['constant_editor.']['configuration.']['quality'];
    $coa_conf = $conf['userFunc.']['constant_editor.']['configuration.']['quality.'];
    $this->quality  = $this->zz_cObjGetSingle( $coa_name, $coa_conf );

    switch( $this->quality )
    {
      case( null ):
      case( 'high' ):
        $this->quality = 'high';
        if( $this->b_drs_init )
        {
          $prompt = 'Quality: ' . $this->quality;
          t3lib_div::devlog( '[INFO/INIT] ' . $prompt, $this->extKey, 0 );
        }
        return;
        break;
      case( 'low' ):
        if( $this->b_drs_init )
        {
          $prompt = 'Quality: ' . $this->quality;
          t3lib_div::devlog( '[INFO/INIT] ' . $prompt, $this->extKey, 0 );
        }
        return;
        break;
      case( 'error' ):
      default:
        if( $this->b_drs_init )
        {
          $prompt = 'Undefined: ' . "['userFunc.']['constant_editor.']['configuration.']['quality']" .
                    ' is ' . $this->quality;
          t3lib_div::devlog( '[ERROR/INIT] ' . $prompt, $this->extKey, 3 );
          $prompt = 'Quality is set to high.';
          t3lib_div::devlog( '[WARN/INIT] ' . $prompt, $this->extKey, 2 );
        }
        $this->quality = 'high';
        return;
        break;
    }

    return;
  }

 /**
  * initRequiredFields( ):
  *
  * @return    mixed        HTML output.
  * @access   private
  * @version  1.0.2
  * @since    1.0.0
  */
  private function initRequiredFields( )
  {

// #44858
//$pos = strpos( '87.177.65.251', t3lib_div :: getIndpEnv( 'REMOTE_ADDR' ) );
//if ( ! ( $pos === false ) )
//{
//  echo '<pre>';
//  var_dump( __METHOD__, __LINE__, $this->table );
//  var_dump( __METHOD__, __LINE__, $GLOBALS['TSFE']->cObj->data );
//  var_dump( __METHOD__, __LINE__, $GLOBALS['TSFE']->currentRecord );
//  echo '</pre>';
//}
    $arr_return = array( );
    $arr_return['content']  = null;
    $arr_return['return']   = false;

      // SWITCH : add required fields for title and media
    switch( true )
    {
      case( ! empty ( $GLOBALS['TSFE']->tx_browser_pi1->cObj->data ) ):
          // Add to the global $arrRequiredFields the title field
        if( $this->fieldLabelForTitle )
        {
          $this->arrRequiredFields[] = $this->fieldLabelForTitle;
        }
          // Add to the global $arrRequiredFields the media field
        if( $this->fieldLabelForMedia )
        {
          $this->arrRequiredFields[] = $this->fieldLabelForMedia;
        }
        break;
      default:
          // Do nothing: $this->cObj->data is set by the TYPO3 core
        break;
    }
      // SWITCH : add required fields for title and media

    $this->arrRequiredFields = array_unique( $this->arrRequiredFields );
      // Add to the global $arrRequiredFields the media field

      // FOREACH : required field
    foreach( $this->arrRequiredFields as $requiredField )
    {
      if( array_key_exists( $requiredField, $this->cObj->data ) )
      {
        continue;
      }
      $prompt = '<div style="background:white;border:1em solid red;padding:1em;text-align:center;">
        <h1>
          ERROR
        </h1>
        <h2>
          Field is missing
        </h2>
        <p>
          $this->cObj->data doesn\'t contain the field "' . $requiredField. '"<br />
          This is an unexpected result.<br />
          Sorry for the trouble.
        </p>
        <p>
          Required fields:<br />
          <pre>
            ' . var_export( $this->arrRequiredFields, true ) . '
          </pre>
        </p>
        <p>
          ' . __METHOD__ . ' (line ' . __LINE__ . ')
        </p>
        <p>
          TYPO3 extension Flip it!
        </p>
        </div>
        ';
      $arr_return['content']  = $prompt;
      $arr_return['return']   = true;
      return $arr_return;
    }
      // FOREACH : required field

      // RETURN : no DRS
    if( ! $this->b_drs_flipit )
    {
      return $arr_return;
    }
      // RETURN : no DRS

      // DRS
    switch( $this->table )
    {
      case( 'tt_content' ):
        $prompt = 'FLIP it! configuration is taken from plugin with id ' . $this->cObj->data['uid'];
        t3lib_div::devlog( '[INFO/DRS] ' . $prompt, $this->extKey, 0 );
        break;
      default:
        $prompt = 'FLIP it! configuration is taken from ' . $this->table . '.uid ' . $this->cObj->data['uid'];
        t3lib_div::devlog( '[INFO/DRS] ' . $prompt, $this->extKey, 0 );
        break;
    }
      // DRS

    return $arr_return;
  }

// /**
//  * initRequiredFieldsByTsfe( ) : checks if cObj->data in TSFE contains the element
//  *                               table.tx_flipit_enabled.
//  *                               If yes
//  *                               * set global $table to the given table
//  *                               * return true
//  *
//  * @return   boolean
//  * @access   private
//  * @version  1.0.0
//  * @since    1.0.0
//  */
//  private function initRequiredFieldsByTsfe( )
//  {
//      // FOREACH  : cObj->data in TSFE
//    foreach( array_keys( $GLOBALS['TSFE']->cObj->data ) as $tableField )
//    {
//      list( $table, $field ) = explode( '.', $tableField );
//      if( $field != 'tx_flipit_enabled' )
//      {
//        continue;
//      }
//      $this->initTable( $table );
//      return true;
//    }
//      // FOREACH  : cObj->data in TSFE
//
//    return false;
//  }


 /**
  * initTable( ) :
  *
  * @return   void
  * @access   private
  * @version  1.0.0
  * @since    1.0.0
  */
  private function initTable( )
  {
    if( ! ( $this->table === null ) )
    {
      return;
    }

    switch( true )
    {
      case( ! empty ( $GLOBALS['TSFE']->tx_browser_pi1->currentRecord ) ):
        list( $this->table ) = explode( ':', $GLOBALS['TSFE']->tx_browser_pi1->currentRecord );
        break;
      default:
        list( $this->table ) = explode( ':', $GLOBALS['TSFE']->currentRecord );
        break;
    }

    if( $this->b_drs_init )
    {
      $prompt = 'table is set to ' . $this->table;
      t3lib_div::devlog( '[INFO/INIT] ' . $prompt, $this->extKey, 0 );
    }

  }




 /**
  * javascriptFancyboxScript( ):
  *
  * @param    string        Content input. Not used, ignore.
  * @param    array        TypoScript configuration
  * @return    mixed        HTML output.
  * @access public
  * @version 0.0.3
  * @since 0.0.3
  */
  public function javascriptFancyboxScript( $content, $conf )
  {
    unset( $content );

      // Current TypoScript configuration
    $this->conf = $conf;

    $spaceLeft  = $conf['userFunc.']['paramsSpaceLeft'];
    $javascript = $conf['userFunc.']['javascript'];
    $params     = array( );
    $strParams  = null;
    $variables  = array( );

      // params
    foreach( array_keys ( ( array ) $conf['userFunc.']['params.'] ) as $param )
    {
        // CONTINUE : param has an dot
      if( rtrim( $param, '.' ) != $param )
      {
        continue;
      }
        // CONTINUE : param has an dot

      $cObj_name  = $conf['userFunc.']['params.'][$param];
      $cObj_conf  = $conf['userFunc.']['params.'][$param . '.'];
      $value      = $this->zz_cObjGetSingle( $cObj_name, $cObj_conf );
      switch( true )
      {
          // Don't process default values
          // See
          //  * http://fancybox.net/api
          //  * constant editor
        case( $param == 'autoDimensions'      && $value == 'true' ):
        case( $param == 'autoScale'           && $value == 'true' ):
        case( $param == 'centerOnScroll'      && $value == 'false' ):
        case( $param == 'cyclic'              && $value == 'false' ):
        case( $param == 'enableEscapeButton'  && $value == 'true' ):
        case( $param == 'height'              && $value == '340px' ):
        case( $param == 'hideOnContentClick'  && $value == 'false' ):
        case( $param == 'hideOnOverlayClick'  && $value == 'true' ):
        case( $param == 'margin'              && $value == 20 ):
        case( $param == 'modal'               && $value == 'false' ):
        case( $param == 'opacity'             && $value == 'false' ):
        case( $param == 'overlayColor'        && $value == "'#666'" ):
        case( $param == 'overlayShow'         && $value == 'true' ):
        case( $param == 'overlayOpacity'      && $value == '0.3' ):
        case( $param == 'padding'             && $value == 10 ):
        case( $param == 'scrolling'           && $value == "'auto'" ):
        case( $param == 'showCloseButton'     && $value == 'true' ):
        case( $param == 'showNavArrows'       && $value == 'true' ):
        case( $param == 'speedIn'             && $value == 300 ):
        case( $param == 'speedOut'            && $value == 300 ):
        case( $param == 'titleShow'           && $value == 'true' ):
        case( $param == 'transitionIn'        && $value == "'fade'" ):
        case( $param == 'transitionOut'       && $value == "'fade'" ):
        case( $param == 'width'               && $value == '560px' ):
          if( $this->b_drs_jquery )
          {
            $prompt = 'Fancybox parameter ' . $param . ' = ' . $value . '. This is the default. Parameter won\'t processed.';
            t3lib_div::devlog( '[INFO/JQUERY] ' . $prompt, $this->extKey, 0 );
          }
          continue 2;
          break;
            // Don't process empty values
        case( $value === null ):
        case( $value == "''" ):
          if( $this->b_drs_jquery )
          {
            $prompt = 'Fancybox parameter ' . $param . ' is empty. Parameter won\'t processed.';
            t3lib_div::devlog( '[INFO/JQUERY] ' . $prompt, $this->extKey, 0 );
          }
          continue 2;
          break;
        default:
            // Follow the workflow
          break;
      }
      $params[] = "'" . $param . "' : " . $value;
    }
    $strParams = implode( ',' . PHP_EOL . str_repeat( ' ', $spaceLeft ), ( array ) $params );
      // params

      // variables
    foreach( array_keys ( ( array ) $conf['userFunc.']['variables.'] ) as $variable )
    {
        // CONTINUE : param has an dot
      if( rtrim( $variable, '.' ) != $variable )
      {
        continue;
      }
        // CONTINUE : param has an dot

      $cObj_name  = $conf['userFunc.']['variables.'][$variable];
      $cObj_conf  = $conf['userFunc.']['variables.'][$variable . '.'];
      $variables['%' . $variable . '%'] = $this->zz_cObjGetSingle( $cObj_name, $cObj_conf );
    }
      // variables

    $javascript = str_replace( '%params%', $strParams, $javascript );
    $javascript = str_replace( array_keys( $variables ), $variables, $javascript );

    return $javascript;
  }



 /**
  * jquery( ):
  *
  * @return    void
  * @access   private
  * @version  0.0.3
  * @since    0.0.3
  */
  private function jquery( )
  {
    $this->jquerySource( );
    $this->jqueryFancybox( );
  }



 /**
  * jquerySource( ):
  *
  * @return    void
  * @access   private
  * @version  0.0.3
  * @since    0.0.3
  */
  private function jquerySource( )
  {
    $conf = $this->conf;

    $coa_name = $conf['userFunc.']['constant_editor.']['jquery.']['source'];
    $coa_conf = $conf['userFunc.']['constant_editor.']['jquery.']['source.'];
    $source = $this->zz_cObjGetSingle( $coa_name, $coa_conf );

    switch( $source )
    {
      case( 'enabled' ):
          // Follow the workflow: include library and css
        break;
      case( 'disabled' ):
        if( $this->b_drs_jquery )
        {
          $prompt = 'jQuery library should not included.';
          t3lib_div::devlog( '[INFO/JQUERY] ' . $prompt, $this->extKey, 0 );
        }
        return;
        break;
      default:
        $prompt = '
          <p>
            Undefined value: $source = ' . $source . '<br />
            Please fix the bug!<br />
            TYPO3 extension Flip it!<br />
            Method: ' . __METHOD__ . ' <br />
            Line: ' . __LINE__ . '
          </p>
';
        die( $prompt );
        break;
    }

    $coa_name = $conf['userFunc.']['constant_editor.']['jquery.']['sourcePosition'];
    $coa_conf = $conf['userFunc.']['constant_editor.']['jquery.']['sourcePosition.'];
    $sourcePosition = $this->zz_cObjGetSingle( $coa_name, $coa_conf );

    $coa_name = $conf['userFunc.']['sourceJs'];
    $coa_conf = $conf['userFunc.']['sourceJs.'];
    $sourceLibrary = $this->zz_cObjGetSingle( $coa_name, $coa_conf );

    switch( $sourcePosition )
    {
      case( 'top' ):
        if( $this->b_drs_jquery )
        {
          $prompt = 'jQuery is included at the top of the page (HTML head).';
          t3lib_div::devlog( '[INFO/JQUERY] ' . $prompt, $this->extKey, 0 );
        }
        $GLOBALS['TSFE']->additionalHeaderData['flipit.source.lib'] = $sourceLibrary;
        break;
      case( 'bottom' ):
        if( $this->b_drs_jquery )
        {
          $prompt = 'jQuery is included at the bottom of the page.';
          t3lib_div::devlog( '[INFO/JQUERY] ' . $prompt, $this->extKey, 0 );
        }
        $GLOBALS['TSFE']->additionalFooterData['flipit.source.lib'] = $sourceLibrary;
        break;
      default:
        $prompt = '
          <p>
            Undefined value: sourcePosition = ' . sourcePosition . '<br />
            Please fix the bug!<br />
            TYPO3 extension Flip it!<br />
            Method: ' . __METHOD__ . ' <br />
            Line: ' . __LINE__ . '
          </p>
';
        die( $prompt );
        break;
    }

    return;
  }



 /**
  * jqueryFancybox( ):
  *
  * @return    void
  * @access   private
  * @version  0.0.3
  * @since    0.0.3
  */
  private function jqueryFancybox( )
  {
    $conf = $this->conf;

    $coa_name = $conf['userFunc.']['constant_editor.']['configuration.']['enableFancybox'];
    $coa_conf = $conf['userFunc.']['constant_editor.']['configuration.']['enableFancybox.'];
    $fancybox = $this->zz_cObjGetSingle( $coa_name, $coa_conf );

    switch( $fancybox )
    {
      case( 'enabled' ):
        if( $this->b_drs_jquery )
        {
          $prompt = 'Fancybox is enabled.';
          t3lib_div::devlog( '[INFO/JQUERY] ' . $prompt, $this->extKey, 0 );
        }
        $this->jqueryFancyboxInclude( );
        break;
      case( 'disabled' ):
        if( $this->b_drs_jquery )
        {
          $prompt = 'Fancybox is disabled.';
          t3lib_div::devlog( '[INFO/JQUERY] ' . $prompt, $this->extKey, 0 );
        }
        break;
      default:
        $prompt = '
          <p>
            Undefined value: $fancybox = ' . $fancybox . '<br />
            Please fix the bug!<br />
            TYPO3 extension Flip it!<br />
            Method: ' . __METHOD__ . ' <br />
            Line: ' . __LINE__ . '
          </p>
';
        die( $prompt );
        break;
    }

    return;
  }



 /**
  * jqueryFancyboxInclude( ):
  *
  * @return    void
  * @access   private
  * @version  0.0.3
  * @since    0.0.3
  */
  private function jqueryFancyboxInclude( )
  {
    $conf = $this->conf;

    $coa_name = $conf['userFunc.']['constant_editor.']['jquery.']['fancybox'];
    $coa_conf = $conf['userFunc.']['constant_editor.']['jquery.']['fancybox.'];
    $fancybox = $this->zz_cObjGetSingle( $coa_name, $coa_conf );

    switch( $fancybox )
    {
      case( 'enabled' ):
          // Follow the workflow: include library and css
        break;
      case( 'disabled' ):
        if( $this->b_drs_jquery )
        {
          $prompt = 'jQuery fancybox library and CSS should not included.';
          t3lib_div::devlog( '[INFO/JQUERY] ' . $prompt, $this->extKey, 0 );
        }
        return;
        break;
      default:
        $prompt = '
          <p>
            Undefined value: $fancybox = ' . $fancybox . '<br />
            Please fix the bug!<br />
            TYPO3 extension Flip it!<br />
            Method: ' . __METHOD__ . ' <br />
            Line: ' . __LINE__ . '
          </p>
';
        die( $prompt );
        break;
    }

    $coa_name = $conf['userFunc.']['fancyboxCss'];
    $coa_conf = $conf['userFunc.']['fancyboxCss.'];
    $fancyboxCss = $this->zz_cObjGetSingle( $coa_name, $coa_conf );
    if( $this->b_drs_jquery )
    {
      $prompt = 'Fancybox CSS is included at the top of the page (HTML head).';
      t3lib_div::devlog( '[INFO/JQUERY] ' . $prompt, $this->extKey, 0 );
    }
    $GLOBALS['TSFE']->additionalHeaderData['flipit.fancybox.css'] = $fancyboxCss;


    $coa_name = $conf['userFunc.']['constant_editor.']['jquery.']['fancyboxPosition'];
    $coa_conf = $conf['userFunc.']['constant_editor.']['jquery.']['fancyboxPosition.'];
    $fancyboxPosition = $this->zz_cObjGetSingle( $coa_name, $coa_conf );

    $coa_name = $conf['userFunc.']['fancyboxJs'];
    $coa_conf = $conf['userFunc.']['fancyboxJs.'];
    $fancyboxLibrary = $this->zz_cObjGetSingle( $coa_name, $coa_conf );

    switch( $fancyboxPosition )
    {
      case( 'top' ):
        if( $this->b_drs_jquery )
        {
          $prompt = 'Fancybox is included at the top of the page (HTML head).';
          t3lib_div::devlog( '[INFO/JQUERY] ' . $prompt, $this->extKey, 0 );
        }
        $GLOBALS['TSFE']->additionalHeaderData['flipit.fancybox.lib'] = $fancyboxLibrary;
        break;
      case( 'bottom' ):
        if( $this->b_drs_jquery )
        {
          $prompt = 'Fancybox is included at the bottom of the page.';
          t3lib_div::devlog( '[INFO/JQUERY] ' . $prompt, $this->extKey, 0 );
        }
        $GLOBALS['TSFE']->additionalFooterData['flipit.fancybox.lib'] = $fancyboxLibrary;
        break;
      default:
        $prompt = '
          <p>
            Undefined value: fancyboxPosition = ' . fancyboxPosition . '<br />
            Please fix the bug!<br />
            TYPO3 extension Flip it!<br />
            Method: ' . __METHOD__ . ' <br />
            Line: ' . __LINE__ . '
          </p>
';
        die( $prompt );
        break;
    }

    return;
  }



 /**
  * zz_cObjGetSingle( ):
  *
  * @return    string        $value  : ....
  * @access   private
  * @version  0.0.3
  * @since    0.0.3
  */
  private function zz_cObjGetSingle( $cObj_name, $cObj_conf )
  {
    switch( true )
    {
      case( is_array( $cObj_conf ) ):
        $value = $this->cObj->cObjGetSingle( $cObj_name, $cObj_conf );
        break;
      case( ! ( is_array( $cObj_conf ) ) ):
      default:
        $value = $cObj_name;
        break;
    }

    return $value;
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
    if( $this->b_drs_exec )
    {
        // #45174, 130205, dwildt
      $debugTrailLevel  = 1;
      $debugTrail       = $this->drs_debugTrail( $debugTrailLevel );
      $debugTrailPrompt = $debugTrail['prompt'];

      $prompt = $exec;
      t3lib_div::devlog( '[INFO/PHP] ' . $debugTrailPrompt . ' -> exec: ' . $prompt, $this->extKey, 0 );
    }
      // DRS

      // Execute!
    exec( $exec, $lines );

      // DRS
    if( $this->b_drs_exec )
    {
      $prompt = var_export( $lines, true );
      t3lib_div::devlog( '[INFO/PHP] lines: ' . $prompt, $this->extKey, 0 );
    }
      // DRS

      // RETURN : lines
    return $lines;
  }


 /**
  * zzFieldWoTable( ):
  *
  * @return
  * @access   private
  * @version  1.0.0
  * @since    1.0.0
  */
  private function zzFieldWoTable( $tableField )
  {
    list( $table, $field ) = explode( '.', $tableField );

    if( $field )
    {
      return $field;
    }
    else
    {
      return $table;
    }

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
    $field = $this->zzFieldWoTable( $field );

      // Get path
    $this->zz_TCAload( $this->table );
    $uploadFolder         = $GLOBALS['TCA'][$this->table]['columns'][$field]['config']['uploadfolder'];
    $typo3_document_root  = t3lib_div::getIndpEnv( 'TYPO3_DOCUMENT_ROOT' );
    $path                 = $typo3_document_root . '/' . $uploadFolder;

      // DRS
    if ( $this->b_drs_error )
    {
      if ( ! $uploadFolder )
      {
        $prompt = '$GLOBALS[TCA][' . $this->table . '][columns][' . $field . '][config][uploadfolder] is empty! ';
        t3lib_div::devlog( '[ERROR/INIT] ' . $prompt, $this->extKey, 3 );
      }
    }
      // DRS

    return $path;
  }



 /**
  * zz_TCAload( ): Load the TCA, if we don't have an table.columns array
  *
  * @return    void
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
          $prompt = 'latest ' . $field . ': ' . date ( 'Y-m-d H:i:s.', $tstampLatest );
          t3lib_div::devlog( '[INFO/FLIPIT] ' . $prompt, $this->extKey, 0 );
        }
        return $tstampLatest;
        break;
      case( false ):
      default:
        if( $this->b_drs_flipit )
        {
          $prompt = 'first ' . $field . ': ' . date ( 'Y-m-d H:i:s.', $tstampFirst );
          t3lib_div::devlog( '[INFO/FLIPIT] ' . $prompt, $this->extKey, 0 );
        }
        return $tstampFirst;
        break;
    }
  }



 /**
  * zz_tstampMedia( ):
  *
  * @return    void
  * @access     private
  * @version  0.0.2
  * @since    0.0.2
  */
  private function zz_tstampMedia( )
  {
    if( ! ( $this->tstampMedia === null ) )
    {
      return;
    }

      // Get latest timestamp of files in given field
    $latest = true;
    $this->tstampMedia = $this->zz_tstampLatest( $this->fieldLabelForMedia, $latest );
  }



 /**
  * zz_tstampRecord( ):
  *
  * @return    void
  * @access     private
  * @version  0.0.2
  * @since    0.0.2
  */
  private function zz_tstampRecord( )
  {
    if( ! ( $this->tstampRecord === null ) )
    {
      return;
    }

      // Get timestamp of current record
    $this->tstampRecord = $this->cObj->data[$this->fieldLabelForTstamp];

    if( $this->b_drs_flipit )
    {
      $prompt = $this->fieldLabelForTstamp . ': ' . date ( 'Y-m-d H:i:s.', $this->tstampRecord );
      t3lib_div::devlog( '[INFO/FLIPIT] ' . $prompt, $this->extKey, 0 );
    }

  }



 /**
  * zz_tstampSwf( ):
  *
  * @return    void
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
  * @return    void
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