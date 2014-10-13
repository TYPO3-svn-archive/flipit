<?php

if( ! defined( 'TYPO3_MODE' ) )
{
  die( 'Access denied.' );
}



  ////////////////////////////////////////////////////////////////////////////
  //
  // INDEX

  // Set TYPO3 version
  // Configuration by the extension manager
  //    Localization support
  // Enables the Include Static Templates
  // Add pagetree icons
  // Methods for backend workflows
  // TCA for tt_content



  ////////////////////////////////////////////////////////////////////////////
  //
  // Set TYPO3 version

  // Set TYPO3 version as integer (sample: 4.7.7 -> 4007007)
list( $main, $sub, $bugfix ) = explode( '.', TYPO3_version );
$version = ( ( int ) $main ) * 1000000;
$version = $version + ( ( int ) $sub ) * 1000;
$version = $version + ( ( int ) $bugfix ) * 1;
$typo3Version = $version;
  // Set TYPO3 version as integer (sample: 4.7.7 -> 4007007)

if( $typo3Version < 3000000 )
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
  // Set TYPO3 version



  ////////////////////////////////////////////////////////////////////////////
  //
  // Configuration by the extension manager

$confArr  = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['flipit']);

  // Language for labels of static templates and page tsConfig
$beLanguage = $confArr['beLanguage'];
switch( $beLanguage )
{
  case( 'German'):
    $beLanguage = 'de';
    break;
  default:
    $beLanguage = 'default';
}
  // Language for labels of static templates and page tsConfig
  // Configuration by the extension manager



  ////////////////////////////////////////////////////////////////////////////
  //
  // Enables the Include Static Templates

  // Case $beLanguage
switch( true )
{
  case( $beLanguage == 'de' ):
      // German
    t3lib_extMgm::addStaticFile($_EXTKEY,'static/',               'Flip it! [1] Basis');
    switch( true )
    {
      case( $typo3Version < 4007000 ):
        t3lib_extMgm::addStaticFile($_EXTKEY,'static/typo3/4.6/', 'Flip it! [1.1] + Basis fuer TYPO3 < 4.7 (einbinden!)');
        break;
      default:
        t3lib_extMgm::addStaticFile($_EXTKEY,'static/typo3/4.6/', 'Flip it! [1.1] + Basis fuer TYPO3 < 4.7 (NICHT einbinden!)');
        break;
    }
    t3lib_extMgm::addStaticFile($_EXTKEY,'static/woFancybox/',    'Flip it! [2] + Fancybox abgeschaltet');
    break;
  default:
      // English
    t3lib_extMgm::addStaticFile($_EXTKEY,'static/',               'Flip it! [1] Basis (obligate!)');
    switch( true )
    {
      case( $typo3Version < 4007000 ):
        t3lib_extMgm::addStaticFile($_EXTKEY,'static/typo3/4.6/', 'Flip it! [1.1] + Basis for TYPO3 < 4.7 (obligate!)');
        break;
      default:
        t3lib_extMgm::addStaticFile($_EXTKEY,'static/typo3/4.6/', 'Flip it! [1.1] + Basis for TYPO3 < 4.7 (don\'t use it!)');
        break;
    }
    t3lib_extMgm::addStaticFile($_EXTKEY,'static/woFancybox/',    'Flip it! [2] + Fancybox disabled');
    break;
}
  // Case $beLanguage
  // Enables the Include Static Templates



  ////////////////////////////////////////////////////////////////////////////
  //
  // Add pagetree icons

  // Case $beLanguage
switch( true )
{
  case( $beLanguage == 'de' ):
      // German
    $TCA['pages']['columns']['module']['config']['items'][] =
       array( 'Flip it!', 'flipit', t3lib_extMgm::extRelPath( $_EXTKEY ).'ext_icon.gif' );
    break;
  default:
      // English
    $TCA['pages']['columns']['module']['config']['items'][] =
       array( 'Flip it!', 'flipit', t3lib_extMgm::extRelPath( $_EXTKEY ).'ext_icon.gif' );
}
  // Case $beLanguage

t3lib_SpriteManager::addTcaTypeIcon('pages', 'contains-flipit', '../typo3conf/ext/flipit/ext_icon.gif');
  // Add pagetree icons



  ///////////////////////////////////////////////////////////
  //
  // Methods for backend workflows

  // #i0004, 130130, dwildt, 1+
require_once(t3lib_extMgm::extPath($_EXTKEY).'lib/flexform/class.tx_flipit_flexform.php');
require_once(t3lib_extMgm::extPath($_EXTKEY).'lib/userfunc/class.tx_flipit_userfunc.php');
  // Methods for backend workflows



  ////////////////////////////////////////////////////////////////////////////
  //
  // TCA for tt_content

t3lib_div::loadTCA( 'tt_content' );

  // Add fields to interface
$showRecordFieldList = $TCA['tt_content']['interface']['showRecordFieldList'];
$showRecordFieldList = $showRecordFieldList.',tx_flipit_layout,tx_flipit_quality,tx_flipit_pagelist,tx_flipit_updateswfxml,tx_flipit_swf_files,tx_flipit_xml_file,tx_flipit_fancybox,tx_flipit_evaluate,tx_flipit_externalLinks';
$TCA['tt_content']['interface']['showRecordFieldList'] = $showRecordFieldList;
  // Add fields to interface

  // Add fields to columns
$TCA['tt_content']['columns']['tx_flipit_layout'] = array (
  'exclude' => 0,
  'label'   => 'LLL:EXT:flipit/locallang_db.xml:tcaLabel_tx_flipit_layout',
  'config'  => array (
    'type' => 'select',
    'items' => array(
      array(
        'LLL:EXT:flipit/locallang_db.xml:tcaLabel_tx_flipit_layout_item_00',
        'layout_00',
      ),
      array(
        'LLL:EXT:flipit/locallang_db.xml:tcaLabel_tx_flipit_layout_item_01',
        'layout_01',
      ),
      array(
        'LLL:EXT:flipit/locallang_db.xml:tcaLabel_tx_flipit_layout_item_02',
        'layout_02',
      ),
      array(
        'LLL:EXT:flipit/locallang_db.xml:tcaLabel_tx_flipit_layout_item_03',
        'layout_03',
      ),
      array(
        'LLL:EXT:flipit/locallang_db.xml:tcaLabel_tx_flipit_layout_item_ts',
        'ts',
      ),
    ),
    'default' => 'ts',
  ),
);
$TCA['tt_content']['columns']['tx_flipit_quality'] = array (
  'exclude' => 0,
  'label'   => 'LLL:EXT:flipit/locallang_db.xml:tcaLabel_tx_flipit_quality',
  'config'  => array (
    'type' => 'select',
    'items' => array(
      array(
        'LLL:EXT:flipit/locallang_db.xml:tcaLabel_tx_flipit_quality_item_high',
        'high',
      ),
      array(
        'LLL:EXT:flipit/locallang_db.xml:tcaLabel_tx_flipit_quality_item_low',
        'low',
      ),
      array(
        'LLL:EXT:flipit/locallang_db.xml:tcaLabel_tx_flipit_quality_item_ts',
        'ts',
      ),
    ),
    'default' => 'ts',
  ),
);
$TCA['tt_content']['columns']['tx_flipit_pagelist'] = array (
  'exclude' => 0,
  'label'   => 'LLL:EXT:flipit/locallang_db.xml:tcaLabel_tx_flipit_pagelist',
  'config'  => array (
    'type'      => 'input',
    'size'      => '40',
    'max'       => '256',
    'checkbox'  => '',
    'eval'      => 'trim',
  ),
);
$TCA['tt_content']['columns']['tx_flipit_updateswfxml'] = array (
  'exclude' => 0,
  'label'   => 'LLL:EXT:flipit/locallang_db.xml:tcaLabel_tx_flipit_updateswfxml',
  'config'  => array (
    'type' => 'select',
    'items' => array(
      array(
        'LLL:EXT:flipit/locallang_db.xml:tcaLabel_tx_flipit_updateswfxml_item_disabled',
        'disabled',
      ),
      array(
        'LLL:EXT:flipit/locallang_db.xml:tcaLabel_tx_flipit_updateswfxml_item_enabled',
        'enabled',
      ),
      array(
        'LLL:EXT:flipit/locallang_db.xml:tcaLabel_tx_flipit_updateswfxml_item_ts',
        'ts',
      ),
    ),
    'default' => 'ts',
  ),
);
$TCA['tt_content']['columns']['tx_flipit_swf_files'] = array (
  'exclude' => 0,
  'label'   => 'LLL:EXT:flipit/locallang_db.xml:tcaLabel_tx_flipit_swf_files',
  'config' => array(
    'type'          => 'group',
    'internal_type' => 'file',
    'allowed'       => 'swf',
    'max_size'      => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],
    'uploadfolder'  => 'uploads/tx_flipit',
    'show_thumbs'   => '1',
    'size'          => '10',
    'maxitems'      => '999',
    'minitems'      => '0',
  ),
);
$TCA['tt_content']['columns']['tx_flipit_xml_file'] = array (
  'exclude' => 0,
  'label'   => 'LLL:EXT:flipit/locallang_db.xml:tcaLabel_tx_flipit_xml_file',
  'config' => array(
    'type'          => 'group',
    'internal_type' => 'file',
    'allowed'       => 'xml',
    'max_size'      => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],
    'uploadfolder'  => 'uploads/tx_flipit',
    'show_thumbs'   => '1',
    'size'          => '1',
    'maxitems'      => '1',
    'minitems'      => '0',
  ),
);
$TCA['tt_content']['columns']['tx_flipit_fancybox'] = array (
  'exclude' => 0,
  'label'   => 'LLL:EXT:flipit/locallang_db.xml:tcaLabel_tx_flipit_fancybox',
  'config'  => array (
    'type' => 'select',
    'items' => array(
      array(
        'LLL:EXT:flipit/locallang_db.xml:tcaLabel_tx_flipit_fancybox_item_disabled',
        'disabled',
      ),
      array(
        'LLL:EXT:flipit/locallang_db.xml:tcaLabel_tx_flipit_fancybox_item_enabled',
        'enabled',
      ),
      array(
        'LLL:EXT:flipit/locallang_db.xml:tcaLabel_tx_flipit_fancybox_item_ts',
        'ts',
      ),
    ),
    'default' => 'ts',
  ),
);
$TCA['tt_content']['columns']['tx_flipit_evaluate'] = array (
  'exclude' => 0,
  'label'   => 'LLL:EXT:flipit/locallang_db.xml:tcaLabel_tx_flipit_evaluate',
  'config'  => array (
    'type'      => 'user',
    'userFunc'  => 'tx_flipit_flexform->evaluate',
  ),
);
$TCA['tt_content']['columns']['tx_flipit_externalLinks'] = array (
  'exclude' => 0,
  'label'   => 'LLL:EXT:flipit/locallang_db.xml:tcaLabel_tx_flipit_externalLinks',
  'config'  => array (
    'type'      => 'user',
    'userFunc'  => 'tx_flipit_userfunc->promptExternalLinks',
  ),
);
  // Add fields to columns

  // Insert div [flipit] at position $int_div_position
$str_showitem = $TCA['tt_content']['types']['uploads']['showitem'];
$arr_showitem = explode( '--div--;', $str_showitem );
$int_div_position = 1;
$arr_new_showitem = array( );
foreach( $arr_showitem as $key => $value )
{
  switch( true )
  {
    case($key < $int_div_position):
      $arr_new_showitem[$key] = $value;
      break;
    case($key == $int_div_position):
      $arr_new_showitem[$key] = '' .
        'LLL:EXT:flipit/locallang_db.xml:tcaLabel_tt_content_div_tx_flipit, ' .
          'tx_flipit_layout,' .
          '--palette--;LLL:EXT:flipit/locallang_db.xml:palette_tx_flipit_quality;tx_flipit_quality,' .
          '--palette--;LLL:EXT:flipit/locallang_db.xml:palette_tx_flipit_files;tx_flipit_files,' .
          '--palette--;LLL:EXT:flipit/locallang_db.xml:palette_tx_flipit_fancybox;tx_flipit_fancybox,' .
          'tx_flipit_evaluate,' .
          'tx_flipit_externalLinks,';
      $arr_new_showitem[$key + 1] = $value;
      break;
    case($key > $int_div_position):
    default:
      $arr_new_showitem[$key + 1] = $value;
      break;
  }
}
$str_showitem = implode( '--div--;', $arr_new_showitem );
$TCA['tt_content']['types']['uploads']['showitem'] = $str_showitem;
unset( $int_div_position );
  // Insert div [flipit] at position $int_div_position

  // Insert palettes
$TCA['tt_content']['palettes']['tx_flipit_fancybox']['showitem'] =
  'tx_flipit_fancybox;LLL:EXT:flipit/locallang_db.xml:tcaLabel_tx_flipit_fancybox';
$TCA['tt_content']['palettes']['tx_flipit_fancybox']['canNotCollapse'] = 1;

$TCA['tt_content']['palettes']['tx_flipit_files']['showitem'] =
  'tx_flipit_updateswfxml;LLL:EXT:flipit/locallang_db.xml:tcaLabel_tx_flipit_updateswfxml, --linebreak--,' .
  'tx_flipit_xml_file;LLL:EXT:flipit/locallang_db.xml:tcaLabel_tx_flipit_xml_file, --linebreak--,' .
  'tx_flipit_swf_files;LLL:EXT:flipit/locallang_db.xml:tcaLabel_tx_flipit_swf_files';
$TCA['tt_content']['palettes']['tx_flipit_files']['canNotCollapse'] = 1;

$TCA['tt_content']['palettes']['tx_flipit_quality']['showitem'] =
  'tx_flipit_quality;LLL:EXT:flipit/locallang_db.xml:tcaLabel_tx_flipit_quality,' .
  'tx_flipit_pagelist;LLL:EXT:flipit/locallang_db.xml:tcaLabel_tx_flipit_pagelist';
$TCA['tt_content']['palettes']['tx_flipit_quality']['canNotCollapse'] = 1;
  // Insert palettes

  // TCA for tt_content

?>