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

$confArr  = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['org']);

  // Language for labels of static templates and page tsConfig
$beLanguage = $confArr['beLanguage'];
switch($beLanguage) {
  case($beLanguage == 'German'):
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
    t3lib_extMgm::addStaticFile($_EXTKEY,'static/', 'Flip it!: Basis (immer einbinden!)');
    switch( true )
    {
      case( $typo3Version < 4007000 ):
        t3lib_extMgm::addStaticFile($_EXTKEY,'static/typo3/4.6/',     'Flip it!: Basis fuer TYPO3 < 4.7 (einbinden!)');
        break;
      default:
        t3lib_extMgm::addStaticFile($_EXTKEY,'static/typo3/4.6/',     'Flip it!: Basis fuer TYPO3 < 4.7 (NICHT einbinden!)');
        break;
    }
    break;
  default:
      // English
    t3lib_extMgm::addStaticFile($_EXTKEY,'static/', 'Flip it!: Basis (obligate!)');
    switch( true )
    {
      case( $typo3Version < 4007000 ):
        t3lib_extMgm::addStaticFile($_EXTKEY,'static/typo3/4.6/',     'Flip it!: Basis for TYPO3 < 4.7 (obligate!)');
        break;
      default:
        t3lib_extMgm::addStaticFile($_EXTKEY,'static/typo3/4.6/',     'Flip it!: Basis for TYPO3 < 4.7 (don\'t use it!)');
        break;
    }
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



  ////////////////////////////////////////////////////////////////////////////
  //
  // TCA for tt_content

t3lib_div::loadTCA( 'tt_content' );

  // Add fields to interface
$showRecordFieldList = $TCA['tt_content']['interface']['showRecordFieldList'];
$showRecordFieldList = $showRecordFieldList.',tx_flipit_enabled,tx_flipit_swf_tstamp,tx_flipit_swf_files,tx_flipit_lightbox';
$TCA['tt_content']['interface']['showRecordFieldList'] = $showRecordFieldList;
  // Add fields to interface

  // Add fields to columns
$TCA['tt_content']['columns']['tx_flipit_enabled'] = array (
  'exclude' => 0,
  'label'   => 'LLL:EXT:flipit/locallang_db.xml:tcaLabel_tx_flipit_enabled',
  'config'  => array (
    'type' => 'text',
    'cols' => '30',
    'rows' => '5',
  ),
);
$TCA['tt_content']['columns']['tx_flipit_swf_tstamp'] = array (
  'exclude' => 0,
  'label'   => 'LLL:EXT:flipit/locallang_db.xml:tcaLabel_tx_flipit_swf_tstamp',
  'config'  => array (
    'type' => 'text',
    'cols' => '30',
    'rows' => '5',
  ),
);
$TCA['tt_content']['columns']['tx_flipit_swf_files'] = array (
  'exclude' => 0,
  'label'   => 'LLL:EXT:flipit/locallang_db.xml:tcaLabel_tx_flipit_swf_files',
  'config'  => array (
    'type' => 'text',
    'cols' => '30',
    'rows' => '5',
  ),
);
$TCA['tt_content']['columns']['tx_flipit_lightbox'] = array (
  'exclude' => 0,
  'label'   => 'LLL:EXT:flipit/locallang_db.xml:tcaLabel_tx_flipit_lightbox',
  'config'  => array (
    'type' => 'text',
    'cols' => '30',
    'rows' => '5',
  ),
);
  // Add fields to columns

  // Insert div [flipit] at position $int_div_position
$str_showitem = $TCA['tt_content']['types']['uploads']['showitem'];
$arr_showitem = explode( '--div--;', $str_showitem );
$int_div_position = 1;
foreach( $arr_showitem as $key => $value )
{
  switch( true )
  {
    case($key == $int_div_position):
      $arr_new_showitem[$key] = '' . 
        'LLL:EXT:org/locallang_db.xml:tcaLabel_tt_content_div_tx_flipit, tx_flipit_enabled, tx_flipit_swf_tstamp, tx_flipit_swf_files, tx_flipit_lightbox,';
      break;
    case($key < $int_div_position):
    case($key > $int_div_position):
    default:
      $arr_new_showitem[$key] = $value;
      break;
  }
}
$str_showitem = implode('--div--;', $arr_new_showitem);
$TCA['fe_users']['types']['uploads']['showitem'] = $str_showitem;
  // Insert div [flipit] at position $int_div_position
  // TCA for tt_content

?>