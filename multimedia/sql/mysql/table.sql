-- --------------------------------------------------------

-- -
-- テーブルの構造`multimedia`
-- -

CREATE TABLE `multimedia` (
  `multimedia_id`           int(11) NOT NULL default '0',
  `album_authority`         tinyint(1) NOT NULL default '0',
  `vote_flag`               tinyint(1) NOT NULL default '0',
  `comment_flag`            tinyint(1) NOT NULL default '0',
  `confirm_flag`            tinyint(1) NOT NULL default '0',
  `room_id`	                int(11) NOT NULL default '0',
  `insert_time`             varchar(14) NOT NULL default '',
  `insert_site_id`          varchar(40) NOT NULL default '',
  `insert_user_id`          varchar(40) NOT NULL default '',
  `insert_user_name`        varchar(255) NOT NULL default '',
  `update_time`             varchar(14) NOT NULL default '',
  `update_site_id`          varchar(40) NOT NULL default '',
  `update_user_id`          varchar(40) NOT NULL default '',
  `update_user_name`        varchar(255) NOT NULL default '',
  PRIMARY KEY  (`multimedia_id`),
  KEY `room_id` (`room_id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- -
-- テーブルの構造 `multimedia_block`
-- -

CREATE TABLE multimedia_block (
  `block_id`           int(11) NOT NULL default '0',
  `multimedia_id`      int(11) NOT NULL default '0',
  `display`            tinyint(1) NOT NULL default '0',
  `autoplay_flag`      tinyint(1) NOT NULL default '0',
  `buffer_time`        int(11) NOT NULL default '0',
  `album_visible_row`  int(11) NOT NULL default '0',
  `new_period`         int(11) default '0',
  `room_id`            int(11) NOT NULL default '0',
  `insert_time`        varchar(14) NOT NULL default '',
  `insert_site_id`     varchar(40) NOT NULL default '',
  `insert_user_id`     varchar(40) NOT NULL default '',
  `insert_user_name`   varchar(255) NOT NULL default '',
  `update_time`        varchar(14) NOT NULL default '',
  `update_site_id`     varchar(40) NOT NULL default '',
  `update_user_id`     varchar(40) NOT NULL default '',
  `update_user_name`   varchar(255) NOT NULL default '',
  PRIMARY KEY  (`block_id`),
  KEY `multimedia_id` (`multimedia_id`),
  KEY `room_id` (`room_id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- -
-- テーブルの構造 `multimedia_album`
-- -

CREATE TABLE multimedia_album (
  `album_id`           int(11) NOT NULL default '0',
  `multimedia_id`      int(11) NOT NULL default '0',
  `album_name`         varchar(255) default NULL,
  `album_jacket`       varchar(255) default NULL,
  `album_description`  text,
  `album_sequence`     int(11) default NULL,
  `image_upload_id`    int(11) NOT NULL default '0',
  `upload_id`          int(11) NOT NULL default '0',
  `width`              int(11) NOT NULL default '0',
  `height`             int(11) NOT NULL default '0',
  `item_count`         int(11) NOT NULL default '0',
  `item_upload_time`   varchar(14) NOT NULL default '',
  `album_vote_count`   int(11) NOT NULL default '0',
  `public_flag`        tinyint(1) NOT NULL default '0',
  `room_id`	           int(11) NOT NULL default '0',
  `insert_time`        varchar(14) NOT NULL default '',
  `insert_site_id`     varchar(40) NOT NULL default '',
  `insert_user_id`     varchar(40) NOT NULL default '',
  `insert_user_name`   varchar(255) NOT NULL default '',
  `update_time`        varchar(14) NOT NULL default '',
  `update_site_id`     varchar(40) NOT NULL default '',
  `update_user_id`     varchar(40) NOT NULL default '',
  `update_user_name`   varchar(255) NOT NULL default '',
  PRIMARY KEY  (`album_id`),
  KEY `multimedia_id` (`multimedia_id`,`album_sequence`),
  KEY `room_id` (`room_id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- -
-- テーブルの構造 `multimedia_item`
-- -

CREATE TABLE multimedia_item (
  `item_id`            int(11) NOT NULL default '0',
  `album_id`           int(11) NOT NULL default '0',
  `multimedia_id`      int(11) NOT NULL default '0',
  `item_name`          varchar(255) default NULL,
  `agree_flag`         tinyint(1) NOT NULL default '0',
  `item_sequence`      int(11) default NULL,
  `upload_id`          int(11) NOT NULL default '0',
  `duration`           int(11) NOT NULL default '0',
  `file_path`          varchar(255) default NULL,
  `item_path`          varchar(255) default NULL,
  `item_play_count`	   int(11) NOT NULL default '0',
  `item_vote_count`	   int(11) NOT NULL default '0',
  `item_description`   text,
  `privacy`            tinyint(1) NOT NULL default '0',
  `room_id`	           int(11) NOT NULL default '0',
  `insert_time`        varchar(14) NOT NULL default '',
  `insert_site_id`     varchar(40) NOT NULL default '',
  `insert_user_id`     varchar(40) NOT NULL default '',
  `insert_user_name`   varchar(255) NOT NULL default '',
  `update_time`        varchar(14) NOT NULL default '',
  `update_site_id`     varchar(40) NOT NULL default '',
  `update_user_id`     varchar(40) NOT NULL default '',
  `update_user_name`   varchar(255) NOT NULL default '',
  PRIMARY KEY  (`item_id`),
  KEY `album_id` (`album_id`,`item_sequence`),
  KEY `multimedia_id` (`multimedia_id`,`room_id`),
  KEY `upload_id` (`upload_id`),
  KEY `room_id` (`room_id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- -
-- テーブルの構造 `multimedia_comment`
-- -

CREATE TABLE multimedia_comment (
  `comment_id`         int(11) NOT NULL default '0',
  `item_id`            int(11) NOT NULL default '0',
  `album_id`           int(11) NOT NULL default '0',
  `multimedia_id`      int(11) NOT NULL default '0',
  `comment_value`      text,
  `room_id`	           int(11) NOT NULL default '0',
  `insert_time`        varchar(14) NOT NULL default '',
  `insert_site_id`     varchar(40) NOT NULL default '',
  `insert_user_id`     varchar(40) NOT NULL default '',
  `insert_user_name`   varchar(255) NOT NULL default '',
  `update_time`        varchar(14) NOT NULL default '',
  `update_site_id`     varchar(40) NOT NULL default '',
  `update_user_id`     varchar(40) NOT NULL default '',
  `update_user_name`   varchar(255) NOT NULL default '',
  PRIMARY KEY  (`comment_id`),
  KEY `item_id` (`item_id`),
  KEY `album_id` (`album_id`),
  KEY `multimedia_id` (`multimedia_id`,`room_id`),
  KEY `room_id` (`room_id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- -
-- テーブルの構造 `multimedia_user_item`
-- -

CREATE TABLE `mutlimedia_user_item` (
  `user_id`            varchar(40) NOT NULL,
  `item_id`            int(11) NOT NULL default '0',
  `vote_flag`          tinyint(1) NOT NULL default '0',
  `room_id`            int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`item_id`),
  KEY `item_id` (`item_id`),
  KEY `room_id` (`room_id`)
) ENGINE=MyISAM;

-- -
-- テーブルの構造 `multimedia_item_tag`
-- -
CREATE TABLE `multimedia_item_tag` (
  `item_id`               int(11) unsigned NOT NULL default '0',
  `tag_id`                int(11) unsigned NOT NULL default '0',
  `tag_value`             varchar(255) BINARY NOT NULL default '',
  `sequence`              int(11) NOT NULL default '0',
  `room_id`	              int(11) NOT NULL default '0',
  `insert_time`           varchar(14) NOT NULL default '',
  `insert_site_id`        varchar(40) NOT NULL default '',
  `insert_user_id`        varchar(40) NOT NULL default '',
  `insert_user_name`      varchar(255) NOT NULL default '',
  `update_time`           varchar(14) NOT NULL default '',
  `update_site_id`        varchar(40) NOT NULL default '',
  `update_user_id`        varchar(40) NOT NULL default '',
  `update_user_name`      varchar(255) NOT NULL default '',
  PRIMARY KEY  (`item_id`,`tag_id`,`tag_value`),
  KEY `tag_id` (`tag_id`),
  KEY `room_id` (`room_id`)
) ENGINE=MyISAM;

-- -
-- テーブルの構造 `multimedia_tag`
-- -
CREATE TABLE `multimedia_tag` (
  `tag_id`                int(11) unsigned NOT NULL default '0',
  `tag_value`             varchar(255) BINARY NOT NULL default '',
  `used_number`           int(11) NOT NULL default '0',
  `room_id`	              int(11) NOT NULL default '0',
  `insert_time`           varchar(14) NOT NULL default '',
  `insert_site_id`        varchar(40) NOT NULL default '',
  `insert_user_id`        varchar(40) NOT NULL default '',
  `insert_user_name`      varchar(255) NOT NULL default '',
  `update_time`           varchar(14) NOT NULL default '',
  `update_site_id`        varchar(40) NOT NULL default '',
  `update_user_id`        varchar(40) NOT NULL default '',
  `update_user_name`      varchar(255) NOT NULL default '',
  PRIMARY KEY  (`tag_id`),
  KEY `tag_value_2` (`tag_value`),
  KEY `room_id` (`room_id`)
) ENGINE=MyISAM;