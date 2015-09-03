<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "flipit".
 *
 * Auto generated 05-04-2013 03:13
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Flip it!',
	'description' => 'Flip it! enables you to run over pages in PDF documents like in a real magazine. '
  . 'Flip it! offers lovely and smooth page flip transitions. '
  . 'The visitor of your website does not need any PDF plugin but a Flash plugin. '
  . 'Flip it! can convert PDF documents to SWF files automatically. '
  . 'See: http://typo3-flipit.de/',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '6.0.2',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
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
			'typo3' => '4.5.0-6.2.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'suggests' => array(
	),
	'_md5_values_when_last_written' => 'a:64:{s:9:"ChangeLog";s:4:"947e";s:21:"ext_conf_template.txt";s:4:"7e57";s:12:"ext_icon.gif";s:4:"d86a";s:17:"ext_localconf.php";s:4:"4b8c";s:14:"ext_tables.php";s:4:"3685";s:14:"ext_tables.sql";s:4:"a598";s:16:"locallang_db.xml";s:4:"1d3d";s:12:"t3jquery.txt";s:4:"3bbb";s:36:"tx_org_downloads_23_doc_1_part_1.swf";s:4:"ba30";s:36:"tx_org_downloads_23_doc_1_part_2.swf";s:4:"9f39";s:14:"doc/manual.pdf";s:4:"1525";s:14:"doc/manual.sxw";s:4:"c1c2";s:41:"lib/flexform/class.tx_flipit_flexform.php";s:4:"895d";s:41:"lib/icons/die-netzmacher-logo-quadrat.png";s:4:"a7d4";s:45:"lib/typoscript/class.tx_flipit_typoscript.php";s:4:"50cc";s:41:"lib/userfunc/class.tx_flipit_userfunc.php";s:4:"34c7";s:26:"lib/userfunc/locallang.xml";s:4:"b5d0";s:36:"res/icons/flipit_link_icon_38x38.gif";s:4:"dcbc";s:36:"res/icons/flipit_link_icon_41x41.gif";s:4:"ab64";s:37:"res/js/jquery.fancybox-1.3.4/ajax.txt";s:4:"f333";s:39:"res/js/jquery.fancybox-1.3.4/index.html";s:4:"c378";s:48:"res/js/jquery.fancybox-1.3.4/jquery-1.4.3.min.js";s:4:"e495";s:38:"res/js/jquery.fancybox-1.3.4/style.css";s:4:"7b50";s:47:"res/js/jquery.fancybox-1.3.4/fancybox/blank.gif";s:4:"3254";s:53:"res/js/jquery.fancybox-1.3.4/fancybox/fancy_close.png";s:4:"6e28";s:55:"res/js/jquery.fancybox-1.3.4/fancybox/fancy_loading.png";s:4:"b1d5";s:56:"res/js/jquery.fancybox-1.3.4/fancybox/fancy_nav_left.png";s:4:"3f3e";s:57:"res/js/jquery.fancybox-1.3.4/fancybox/fancy_nav_right.png";s:4:"216e";s:56:"res/js/jquery.fancybox-1.3.4/fancybox/fancy_shadow_e.png";s:4:"fd4f";s:56:"res/js/jquery.fancybox-1.3.4/fancybox/fancy_shadow_n.png";s:4:"18cd";s:57:"res/js/jquery.fancybox-1.3.4/fancybox/fancy_shadow_ne.png";s:4:"63ad";s:57:"res/js/jquery.fancybox-1.3.4/fancybox/fancy_shadow_nw.png";s:4:"c820";s:56:"res/js/jquery.fancybox-1.3.4/fancybox/fancy_shadow_s.png";s:4:"9b9e";s:57:"res/js/jquery.fancybox-1.3.4/fancybox/fancy_shadow_se.png";s:4:"a8af";s:57:"res/js/jquery.fancybox-1.3.4/fancybox/fancy_shadow_sw.png";s:4:"f81c";s:56:"res/js/jquery.fancybox-1.3.4/fancybox/fancy_shadow_w.png";s:4:"59b0";s:58:"res/js/jquery.fancybox-1.3.4/fancybox/fancy_title_left.png";s:4:"1582";s:58:"res/js/jquery.fancybox-1.3.4/fancybox/fancy_title_main.png";s:4:"38da";s:58:"res/js/jquery.fancybox-1.3.4/fancybox/fancy_title_over.png";s:4:"b886";s:59:"res/js/jquery.fancybox-1.3.4/fancybox/fancy_title_right.png";s:4:"6cbe";s:52:"res/js/jquery.fancybox-1.3.4/fancybox/fancybox-x.png";s:4:"1686";s:52:"res/js/jquery.fancybox-1.3.4/fancybox/fancybox-y.png";s:4:"36a5";s:50:"res/js/jquery.fancybox-1.3.4/fancybox/fancybox.png";s:4:"11e5";s:63:"res/js/jquery.fancybox-1.3.4/fancybox/jquery.easing-1.3.pack.js";s:4:"def2";s:63:"res/js/jquery.fancybox-1.3.4/fancybox/jquery.fancybox-1.3.4.css";s:4:"4638";s:62:"res/js/jquery.fancybox-1.3.4/fancybox/jquery.fancybox-1.3.4.js";s:4:"e7fc";s:67:"res/js/jquery.fancybox-1.3.4/fancybox/jquery.fancybox-1.3.4.pack.js";s:4:"8bc3";s:69:"res/js/jquery.fancybox-1.3.4/fancybox/jquery.mousewheel-3.0.4.pack.js";s:4:"3b0a";s:36:"res/js/swfobjects_1.4.4/swfobject.js";s:4:"5859";s:43:"res/js/swfobjects_1.4.4/swfobject_source.js";s:4:"d437";s:32:"res/sampledata/sample_flipit.pdf";s:4:"c8cb";s:32:"res/sampledata/sample_flipit.xml";s:4:"7317";s:34:"res/sampledata/sample_flipit_1.swf";s:4:"8a97";s:34:"res/sampledata/sample_flipit_2.swf";s:4:"a670";s:34:"res/sampledata/sample_flipit_3.swf";s:4:"deae";s:34:"res/sampledata/sample_flipit_4.swf";s:4:"1675";s:16:"res/swf/book.swf";s:4:"c8d6";s:26:"res/swf/sources/readme.txt";s:4:"a48c";s:20:"static/constants.txt";s:4:"5f13";s:16:"static/setup.txt";s:4:"e91b";s:30:"static/typo3/4.6/constants.txt";s:4:"d41d";s:26:"static/typo3/4.6/setup.txt";s:4:"64da";s:31:"static/woFancybox/constants.txt";s:4:"4172";s:27:"static/woFancybox/setup.txt";s:4:"d41d";}',
);

?>