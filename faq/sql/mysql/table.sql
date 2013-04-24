-- --------------------------------------------------------

-- -
-- テーブルの構造 `faq`
-- -

CREATE TABLE `faq` (
  `faq_id`             int(11) NOT NULL default '0',
  `room_id`            int(11) NOT NULL default '0',
  `faq_name`           varchar(255) NOT NULL default '',
  `faq_authority`      tinyint(3) NOT NULL default '0',
  `insert_time`        varchar(14) NOT NULL default '',
  `insert_site_id`     varchar(40) NOT NULL default '',
  `insert_user_id`     varchar(40) NOT NULL default '',
  `insert_user_name`   varchar(255) NOT NULL default '',
  `update_time`        varchar(14) NOT NULL default '',
  `update_site_id`     varchar(40) NOT NULL default '',
  `update_user_id`     varchar(40) NOT NULL default '',
  `update_user_name`   varchar(255) NOT NULL default '',
  PRIMARY KEY  (`faq_id`),
  KEY `room_id` (`room_id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- -
-- テーブルの構造 `faq_block`
-- -

CREATE TABLE `faq_block` (
  `block_id`           int(11) NOT NULL default '0',
  `faq_id`             int(11) NOT NULL default '0',
  `display_category`   int(11) NOT NULL default '0',
  `display_row`        int(11) NOT NULL default '0',
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
  KEY `faq_id` (`faq_id`),
  KEY `room_id` (`room_id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- -
-- テーブルの構造 `faq_category`
-- -

CREATE TABLE `faq_category` (
  `category_id`          int(11) NOT NULL default '0',
  `faq_id`               int(11) NOT NULL default '0',
  `display_sequence`     int(11) default '0',
  `category_name`        varchar(255) NOT NULL default '',
  `category_description` text,
  `room_id`              int(11) NOT NULL default '0',
  `insert_time`          varchar(14) NOT NULL default '',
  `insert_site_id`       varchar(40) NOT NULL default '',
  `insert_user_id`       varchar(40) NOT NULL default '',
  `insert_user_name`     varchar(255) NOT NULL default '',
  `update_time`          varchar(14) NOT NULL default '',
  `update_site_id`       varchar(40) NOT NULL default '',
  `update_user_id`       varchar(40) NOT NULL default '',
  `update_user_name`     varchar(255) NOT NULL default '',
  PRIMARY KEY  (`category_id`),
  KEY `faq_id` (`faq_id`,`display_sequence`),
  KEY `room_id` (`room_id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- -
-- テーブルの構造 `faq_question`
-- -

CREATE TABLE `faq_question` (
  `question_id`        int(11) NOT NULL default '0',
  `faq_id`             int(11) NOT NULL default '0',
  `category_id`        int(11) NOT NULL default '0',
  `display_sequence`   int(11) default '0',
  `question_name`      text,
  `question_answer`    text,
  `room_id`            int(11) NOT NULL default '0',
  `insert_time`        varchar(14) NOT NULL default '',
  `insert_site_id`     varchar(40) NOT NULL default '',
  `insert_user_id`     varchar(40) NOT NULL default '',
  `insert_user_name`   varchar(255) NOT NULL default '',
  `update_time`        varchar(14) NOT NULL default '',
  `update_site_id`     varchar(40) NOT NULL default '',
  `update_user_id`     varchar(40) NOT NULL default '',
  `update_user_name`   varchar(255) NOT NULL default '',
  PRIMARY KEY  (`question_id`),
  KEY `faq_id` (`faq_id`,`category_id`),
  KEY `room_id` (`room_id`)
) ENGINE=MyISAM;
