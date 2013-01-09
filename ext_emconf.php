<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "flipit".
 *
 * Auto generated 09-01-2013 00:50
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Flip it!',
	'description' => 'Flip it! enables you to run over pages in PDF documents and magazines. Flip it! offers lovely and smooth page flip transitions. The visitor of your website does not need any PDF plugin but a Flash plugin. Flip it! converts PDF documents to SWF files automatically. BE AWARE: You have to install one software package on the web server (Linux and  Windows are supported). You must be root.',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '0.0.3',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'alpha',
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Dirk Wildt (Die Netzmacher)',
	'author_email' => 'http://wildt.at.die-netzmacher.de',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'suggests' => array(
	),
	'_md5_values_when_last_written' => 'a:101:{s:9:"ChangeLog";s:4:"215f";s:21:"ext_conf_template.txt";s:4:"da46";s:12:"ext_icon.gif";s:4:"92e3";s:17:"ext_localconf.php";s:4:"4b8c";s:14:"ext_tables.php";s:4:"8459";s:14:"ext_tables.sql";s:4:"1c2b";s:16:"locallang_db.xml";s:4:"90bb";s:17:"lib/locallang.xml";s:4:"709b";s:41:"lib/icons/die-netzmacher-logo-quadrat.png";s:4:"a7d4";s:45:"lib/typoscript/class.tx_flipit_typoscript.php";s:4:"06c1";s:41:"lib/userfunc/class.tx_flipit_userfunc.php";s:4:"6616";s:37:"res/jss/swfobjects_1.4.4/swfobject.js";s:4:"5859";s:44:"res/jss/swfobjects_1.4.4/swfobject_source.js";s:4:"d437";s:16:"res/swf/book.swf";s:4:"c8d6";s:45:"res/swf/sources/FlashPageFlip_FreeVersion.zip";s:4:"4d5a";s:51:"res/swf/sources/pageflip_v213_dynamic_v2_source.zip";s:4:"3f44";s:40:"res/swf/sources/pageflip_v225_source.zip";s:4:"b736";s:26:"res/swf/sources/readme.txt";s:4:"a48c";s:18:"samples/index.html";s:4:"0462";s:24:"samples/typo3_flipit.ico";s:4:"eb51";s:22:"samples/daten/book.swf";s:4:"c8d6";s:24:"samples/daten/index.html";s:4:"8e87";s:21:"samples/daten/log.txt";s:4:"d41d";s:26:"samples/daten/swfobject.js";s:4:"5859";s:28:"samples/daten/data/pages.xml";s:4:"2b78";s:30:"samples/daten/data/pages/1.swf";s:4:"1542";s:30:"samples/daten/data/pages/2.swf";s:4:"7ea2";s:25:"samples/jahr2012/book.swf";s:4:"c8d6";s:27:"samples/jahr2012/index.html";s:4:"b613";s:24:"samples/jahr2012/log.txt";s:4:"d41d";s:29:"samples/jahr2012/swfobject.js";s:4:"5859";s:31:"samples/jahr2012/data/pages.xml";s:4:"55f3";s:33:"samples/jahr2012/data/pages/1.swf";s:4:"a1c0";s:34:"samples/jahr2012/data/pages/10.swf";s:4:"783d";s:34:"samples/jahr2012/data/pages/11.swf";s:4:"b706";s:34:"samples/jahr2012/data/pages/12.swf";s:4:"9e8d";s:33:"samples/jahr2012/data/pages/2.swf";s:4:"36f8";s:33:"samples/jahr2012/data/pages/3.swf";s:4:"9800";s:33:"samples/jahr2012/data/pages/4.swf";s:4:"e500";s:33:"samples/jahr2012/data/pages/5.swf";s:4:"ebf0";s:33:"samples/jahr2012/data/pages/6.swf";s:4:"7e30";s:33:"samples/jahr2012/data/pages/7.swf";s:4:"785a";s:33:"samples/jahr2012/data/pages/8.swf";s:4:"d0c6";s:33:"samples/jahr2012/data/pages/9.swf";s:4:"e283";s:25:"samples/t3manual/book.swf";s:4:"c8d6";s:27:"samples/t3manual/index.html";s:4:"1e72";s:24:"samples/t3manual/log.txt";s:4:"d41d";s:29:"samples/t3manual/swfobject.js";s:4:"5859";s:31:"samples/t3manual/data/pages.xml";s:4:"0aab";s:33:"samples/t3manual/data/pages/1.swf";s:4:"a963";s:34:"samples/t3manual/data/pages/10.swf";s:4:"b5aa";s:34:"samples/t3manual/data/pages/11.swf";s:4:"1d96";s:34:"samples/t3manual/data/pages/12.swf";s:4:"f48c";s:34:"samples/t3manual/data/pages/13.swf";s:4:"c1b4";s:34:"samples/t3manual/data/pages/14.swf";s:4:"d9e1";s:34:"samples/t3manual/data/pages/15.swf";s:4:"8603";s:34:"samples/t3manual/data/pages/16.swf";s:4:"0e6d";s:34:"samples/t3manual/data/pages/17.swf";s:4:"77d4";s:34:"samples/t3manual/data/pages/18.swf";s:4:"13b5";s:34:"samples/t3manual/data/pages/19.swf";s:4:"4806";s:33:"samples/t3manual/data/pages/2.swf";s:4:"ff4d";s:34:"samples/t3manual/data/pages/20.swf";s:4:"00c0";s:34:"samples/t3manual/data/pages/21.swf";s:4:"1de1";s:34:"samples/t3manual/data/pages/22.swf";s:4:"89aa";s:34:"samples/t3manual/data/pages/23.swf";s:4:"0f5a";s:34:"samples/t3manual/data/pages/24.swf";s:4:"8b39";s:34:"samples/t3manual/data/pages/25.swf";s:4:"ff1a";s:34:"samples/t3manual/data/pages/26.swf";s:4:"26b6";s:34:"samples/t3manual/data/pages/27.swf";s:4:"82b8";s:34:"samples/t3manual/data/pages/28.swf";s:4:"7b51";s:34:"samples/t3manual/data/pages/29.swf";s:4:"0933";s:33:"samples/t3manual/data/pages/3.swf";s:4:"d295";s:34:"samples/t3manual/data/pages/30.swf";s:4:"4e20";s:34:"samples/t3manual/data/pages/31.swf";s:4:"2ce3";s:34:"samples/t3manual/data/pages/32.swf";s:4:"8899";s:34:"samples/t3manual/data/pages/33.swf";s:4:"2deb";s:34:"samples/t3manual/data/pages/34.swf";s:4:"c98a";s:34:"samples/t3manual/data/pages/35.swf";s:4:"c6c3";s:34:"samples/t3manual/data/pages/36.swf";s:4:"fbc9";s:34:"samples/t3manual/data/pages/37.swf";s:4:"f7d5";s:34:"samples/t3manual/data/pages/38.swf";s:4:"06fe";s:34:"samples/t3manual/data/pages/39.swf";s:4:"728b";s:33:"samples/t3manual/data/pages/4.swf";s:4:"8639";s:34:"samples/t3manual/data/pages/40.swf";s:4:"091f";s:34:"samples/t3manual/data/pages/41.swf";s:4:"8e97";s:33:"samples/t3manual/data/pages/5.swf";s:4:"e0ca";s:33:"samples/t3manual/data/pages/6.swf";s:4:"abad";s:33:"samples/t3manual/data/pages/7.swf";s:4:"d10b";s:33:"samples/t3manual/data/pages/8.swf";s:4:"033f";s:33:"samples/t3manual/data/pages/9.swf";s:4:"e3dc";s:28:"samples/wohnbauland/book.swf";s:4:"c8d6";s:30:"samples/wohnbauland/index.html";s:4:"d7b6";s:27:"samples/wohnbauland/log.txt";s:4:"d41d";s:32:"samples/wohnbauland/swfobject.js";s:4:"5859";s:34:"samples/wohnbauland/data/pages.xml";s:4:"468d";s:36:"samples/wohnbauland/data/pages/1.swf";s:4:"358b";s:36:"samples/wohnbauland/data/pages/2.swf";s:4:"ec4a";s:36:"samples/wohnbauland/data/pages/3.swf";s:4:"c717";s:36:"samples/wohnbauland/data/pages/4.swf";s:4:"af01";s:20:"static/constants.txt";s:4:"59d0";s:16:"static/setup.txt";s:4:"78ed";}',
);

?>