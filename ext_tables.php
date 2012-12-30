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
  // Configure third party tables
  // TCA tables



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
  case($beLanguage == 'de'):
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
?>