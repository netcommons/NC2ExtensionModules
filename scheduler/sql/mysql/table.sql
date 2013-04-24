-- --------------------------------------------------------

-- -
-- Table Structure `scheduler`
-- -

CREATE TABLE `scheduler` (
  `scheduler_id`       int(11) unsigned NOT NULL,
  `room_id`            int(11) unsigned NOT NULL,
  `authority`          tinyint unsigned NOT NULL default 0,
  `mail_send`          tinyint(1) NOT NULL default '0',
  `mail_authority`     tinyint(1) NOT NULL default '0',
  `mail_subject`       varchar(255) NOT NULL default '',
  `mail_body`          text,
  `insert_time`        varchar(14) NOT NULL default '',
  `insert_site_id`     varchar(40) NOT NULL default '',
  `insert_user_id`     varchar(40) NOT NULL default '',
  `insert_user_name`   varchar(255) NOT NULL default '',
  `update_time`        varchar(14) NOT NULL default '',
  `update_site_id`     varchar(40) NOT NULL default '',
  `update_user_id`     varchar(40) NOT NULL default '',
  `update_user_name`   varchar(255) NOT NULL default '',
  PRIMARY KEY  (`scheduler_id`),
  KEY `room_id` (`room_id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- -
-- Table Structure `scheduler_block`
-- -

CREATE TABLE `scheduler_block` (
  `block_id`           int(11) unsigned NOT NULL,
  `scheduler_id`       int(11) unsigned NOT NULL,
  `display`            tinyint(1) NOT NULL default '0',
  `visible_row`        int(11) NOT NULL default '0',
  `new_period`         int(11) NOT NULL default '0',
  `schedule_id`        int(11) unsigned NOT NULL,
  `room_id`            int(11) NOT NULL default 0,
  `insert_time`        varchar(14) NOT NULL default '',
  `insert_site_id`     varchar(40) NOT NULL default '',
  `insert_user_id`     varchar(40) NOT NULL default '',
  `insert_user_name`   varchar(255) NOT NULL default '',
  `update_time`        varchar(14) NOT NULL default '',
  `update_site_id`     varchar(40) NOT NULL default '',
  `update_user_id`     varchar(40) NOT NULL default '',
  `update_user_name`   varchar(255) NOT NULL default '',
   PRIMARY KEY  (`block_id`),
  KEY `scheduler_id` (`scheduler_id`),
  KEY `room_id` (`room_id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- -
-- Table Structure `scheduler_schedule`
-- -

CREATE TABLE `scheduler_schedule` (
  `schedule_id`        int(11) unsigned NOT NULL,
  `scheduler_id`       int(11) unsigned NOT NULL,
  `summary`            varchar(255) NOT NULL default '',
  `icon`               varchar(255) NOT NULL default '',
  `room_id`            int(11) NOT NULL default 0,
  `period`             varchar(14) default '',
  `entry_type`         tinyint(1) NOT NULL default '0',
  `insert_time`        varchar(14) NOT NULL default '',
  `insert_site_id`     varchar(40) NOT NULL default '',
  `insert_user_id`     varchar(40) NOT NULL default '',
  `insert_user_name`   varchar(255) NOT NULL default '',
  `update_time`        varchar(14) NOT NULL default '',
  `update_site_id`     varchar(40) NOT NULL default '',
  `update_user_id`     varchar(40) NOT NULL default '',
  `update_user_name`   varchar(255) NOT NULL default '',
  PRIMARY KEY  (`schedule_id`),
  KEY `scheduler_id` (`scheduler_id`,`room_id`),
  KEY `room_id` (`room_id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- -
-- Table Structure `scheduler_schedule_description`
-- -

CREATE TABLE `scheduler_schedule_description` (
  `schedule_id`        int(11) unsigned NOT NULL,
  `description`        text,
  `room_id`            int(11) NOT NULL default '0',
  PRIMARY KEY  (`schedule_id`),
  KEY `room_id` (`room_id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- -
-- Table Structure `scheduler_date`
-- -

CREATE TABLE `scheduler_date` (
  `date_id`            int(11) unsigned NOT NULL,
  `scheduler_id`       int(11) unsigned NOT NULL,
  `schedule_id`        int(11) unsigned NOT NULL,
  `date_sequence`      int(11) NOT NULL default '0',
  `rank`               int(11) NOT NULL default '0',
  `rank_point`         int(11) NOT NULL default '0',
  `allday_flag`        tinyint(1) unsigned NOT NULL default 0,
  `start_date`         varchar(8) NOT NULL default '',
  `start_time`         varchar(6) NOT NULL default '',
  `start_time_full`    varchar(14) NOT NULL default '',
  `end_date`           varchar(8) NOT NULL default '',
  `end_time`           varchar(6) NOT NULL default '',
  `end_time_full`      varchar(14) NOT NULL default '',
  `timezone_offset`    float(3,1) NOT NULL default '0.0',
  `calendar_id`        int(11) unsigned NOT NULL,
  `room_id`            int(11) NOT NULL default 0,
  `insert_time`        varchar(14) NOT NULL default '',
  `insert_site_id`     varchar(40) NOT NULL default '',
  `insert_user_id`     varchar(40) NOT NULL default '',
  `insert_user_name`   varchar(255) NOT NULL default '',
  `update_time`        varchar(14) NOT NULL default '',
  `update_site_id`     varchar(40) NOT NULL default '',
  `update_user_id`     varchar(40) NOT NULL default '',
  `update_user_name`   varchar(255) NOT NULL default '',
  PRIMARY KEY  (`date_id`),
  KEY `scheduler_id` (`scheduler_id`),
  KEY `schedule_id` (`schedule_id`,`start_time_full`),
  KEY `room_id` (`room_id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- -
-- Table Structure `scheduler_reply`
-- -

CREATE TABLE `scheduler_reply` (
  `reply_id`            int(11) unsigned NOT NULL,
  `scheduler_id`       int(11) unsigned NOT NULL,
  `schedule_id`        int(11) unsigned NOT NULL,
  `date_id`            int(11) unsigned NOT NULL,
  `user_id`            varchar(40) NOT NULL default '',
  `user_name`          varchar(255) NOT NULL default '',
  `reply`              int(1) NOT NULL default 0,
  `reply_comment`      text,
  `room_id`            int(11) NOT NULL default 0,
  `insert_time`        varchar(14) NOT NULL default '',
  `insert_site_id`     varchar(40) NOT NULL default '',
  `insert_user_id`     varchar(40) NOT NULL default '',
  `insert_user_name`   varchar(255) NOT NULL default '',
  `update_time`        varchar(14) NOT NULL default '',
  `update_site_id`     varchar(40) NOT NULL default '',
  `update_user_id`     varchar(40) NOT NULL default '',
  `update_user_name`   varchar(255) NOT NULL default '',
  PRIMARY KEY  (`reply_id`),
  KEY `scheduler_id` (`scheduler_id`,`user_id`),
  KEY `schedule_id` (`schedule_id`),
  KEY `date_id` (`date_id`,`user_id`),
  KEY `room_id` (`room_id`)
) ENGINE=MyISAM;