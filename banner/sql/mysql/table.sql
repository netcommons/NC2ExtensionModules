-- --------------------------------------------------------

-- 
-- テーブルの構造 `banner`
-- 

CREATE TABLE `banner` (
  `banner_id`                  int(11) NOT NULL default '0',
  `banner_name`                varchar(255) NOT NULL default '',
  `banner_type`                tinyint(1) NOT NULL default '0',
  `category_id`                int(11) NOT NULL default '0',
  `banner_sequence`            int(11) NOT NULL default '0',
  `all_click_count`            int(11) NOT NULL default '0',
  `link_url`                   text,
  `target`                     varchar(255) NOT NULL default '',
  `image_url`                  text,
  `upload_id`                  int(11) NOT NULL default '0',
  `image_path`                 text,
  `image_width`                int(11) NOT NULL default '0',
  `image_height`               int(11) NOT NULL default '0',
  `size_flag`                  tinyint(1) NOT NULL default '0',
  `display_width`              int(11) NOT NULL default '0',
  `display_height`             int(11) NOT NULL default '0',
  `source_code`                text,
  `room_id`                    int(11) NOT NULL default '0',
  `insert_time`                varchar(14) NOT NULL default '',
  `insert_site_id`             varchar(40) NOT NULL default '',
  `insert_user_id`             varchar(40) NOT NULL default '',
  `insert_user_name`           varchar(255) NOT NULL default '',
  `update_time`                varchar(14) NOT NULL default '',
  `update_site_id`             varchar(40) NOT NULL default '',
  `update_user_id`             varchar(40) NOT NULL default '',
  `update_user_name`           varchar(255) NOT NULL default '',
  PRIMARY KEY  (`banner_id`),
  KEY `banner_sequence` (`banner_sequence`),
  KEY `room_id` (`room_id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- テーブルの構造 `banner_category`
-- 

CREATE TABLE `banner_category` (
  `category_id`                int(11) NOT NULL default '0',
  `category_name`              varchar(255) default NULL,
  `category_sequence`           int(11) default NULL,
  `room_id`                    int(11) NOT NULL default '0',
  `insert_time`                varchar(14) NOT NULL default '',
  `insert_site_id`             varchar(40) NOT NULL default '',
  `insert_user_id`             varchar(40) NOT NULL default '',
  `insert_user_name`           varchar(255) NOT NULL default '',
  `update_time`                varchar(14) NOT NULL default '',
  `update_site_id`             varchar(40) NOT NULL default '',
  `update_user_id`             varchar(40) NOT NULL default '',
  `update_user_name`           varchar(255) NOT NULL default '',
  PRIMARY KEY  (`category_id`),
  KEY `category_sequence` (`category_sequence`),
  KEY `room_id` (`room_id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- テーブルの構造 `banner_display`
-- 

CREATE TABLE `banner_display` (
  `block_id`                   int(11) NOT NULL default '0',
  `display_type`               tinyint(1) NOT NULL default '0',
  `room_id`                    int(11) NOT NULL default '0',
  `insert_time`                varchar(14) NOT NULL default '',
  `insert_site_id`             varchar(40) NOT NULL default '',
  `insert_user_id`             varchar(40) NOT NULL default '',
  `insert_user_name`           varchar(255) NOT NULL default '',
  `update_time`                varchar(14) NOT NULL default '',
  `update_site_id`             varchar(40) NOT NULL default '',
  `update_user_id`             varchar(40) NOT NULL default '',
  `update_user_name`           varchar(255) NOT NULL default '',
  PRIMARY KEY  (`block_id`),
  KEY `room_id` (`room_id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- テーブルの構造 `banner_block`
-- 

CREATE TABLE `banner_block` (
  `block_id`                   int(11) NOT NULL default '0',
  `banner_id`                  int(11) NOT NULL default '0',
  `display`                    tinyint(1) NOT NULL default '0',
  `block_click_count`          int(11) NOT NULL default '0',
  `room_id`                    int(11) NOT NULL default '0',
  `insert_time`                varchar(14) NOT NULL default '',
  `insert_site_id`             varchar(40) NOT NULL default '',
  `insert_user_id`             varchar(40) NOT NULL default '',
  `insert_user_name`           varchar(255) NOT NULL default '',
  `update_time`                varchar(14) NOT NULL default '',
  `update_site_id`             varchar(40) NOT NULL default '',
  `update_user_id`             varchar(40) NOT NULL default '',
  `update_user_name`           varchar(255) NOT NULL default '',
  PRIMARY KEY  (`block_id`, `banner_id`),
  KEY `room_id` (`room_id`)
) ENGINE=MyISAM;