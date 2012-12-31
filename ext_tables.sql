# INDEX
# -----
# tt_content



#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content (
  tx_flipit_enabled tinytext unsigned DEFAULT '0' NOT NULL,
  tx_flipit_swf_files text,
  tx_flipit_lightbox tinytext unsigned DEFAULT '0' NOT NULL
);