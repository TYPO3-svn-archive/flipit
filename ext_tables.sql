# INDEX
# -----
# tt_content



#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content (
  tx_flipit_enabled tinyint(3) unsigned DEFAULT '0' NOT NULL,
  tx_flipit_swf_files text,
  tx_flipit_lightbox tinyint(3) unsigned DEFAULT '0' NOT NULL
);