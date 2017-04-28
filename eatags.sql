/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table eat.action_add
CREATE TABLE IF NOT EXISTS `action_add` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `evernote_user_id` int(11) DEFAULT NULL,
  `notebook_guid` varchar(45) DEFAULT NULL,
  `note_guid` varchar(40) DEFAULT NULL,
  `type` varchar(6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;


-- Dumping structure for table eat.action_flickr
CREATE TABLE IF NOT EXISTS `action_flickr` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `evernote_user_id` int(11) DEFAULT NULL,
  `flickr_fullname` varchar(200) DEFAULT NULL,
  `flickr_oauth_token` varchar(512) DEFAULT NULL,
  `flickr_oauth_token_secret` varchar(512) DEFAULT NULL,
  `flickr_user_nsid` int(11) DEFAULT NULL,
  `flickr_username` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


-- Dumping structure for table eat.action_gmail
CREATE TABLE IF NOT EXISTS `action_gmail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_google_auth_id` int(11) NOT NULL,
  `lang_code` varchar(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;


-- Dumping structure for table eat.action_latex
CREATE TABLE IF NOT EXISTS `action_latex` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `evernote_user_id` int(11) DEFAULT NULL,
  `delete_check` tinyint(1) DEFAULT '0',
  `latex_key` varchar(5) DEFAULT NULL,
  `image_inline` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;


-- Dumping structure for table eat.action_twitter
CREATE TABLE IF NOT EXISTS `action_twitter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `evernote_user_id` int(11) DEFAULT NULL,
  `twitter_oauth_token` varchar(512) COLLATE utf8_bin DEFAULT NULL,
  `twitter_oauth_token_secret` varchar(512) COLLATE utf8_bin DEFAULT NULL,
  `twitter_user_id` int(11) DEFAULT NULL,
  `twitter_screen_name` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


-- Dumping structure for table eat.action_user_newsletter_emails
CREATE TABLE IF NOT EXISTS `action_user_newsletter_emails` (
  `evernote_user_id` int(11) NOT NULL,
  `email` varchar(320) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- Dumping structure for table eat.action_wordpress
CREATE TABLE IF NOT EXISTS `action_wordpress` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `evernote_user_id` int(11) NOT NULL,
  `wp_blog_url` varchar(512) COLLATE utf8_bin NOT NULL,
  `wp_username` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `wp_pass` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


-- Dumping structure for table eat.action_wordpress_note_post_link
CREATE TABLE IF NOT EXISTS `action_wordpress_note_post_link` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `blog_id` int(11) unsigned DEFAULT NULL,
  `note_guid` varchar(40) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `blog_id` (`blog_id`),
  CONSTRAINT `action_wordpress_note_post_link_ibfk_1` FOREIGN KEY (`blog_id`) REFERENCES `action_wordpress` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;


-- Dumping structure for table eat.act_latex
CREATE TABLE IF NOT EXISTS `act_latex` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `evernote_user_id` int(11) NOT NULL,
  `formula_id` int(11) NOT NULL,
  `formula_text` varchar(512) DEFAULT NULL,
  `note_guid` varchar(40) DEFAULT NULL,
  `resource_md5_body` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


-- Dumping structure for table eat.ci_sessions
CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `session_id` varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '0',
  `ip_address` varchar(16) COLLATE utf8_bin NOT NULL DEFAULT '0',
  `user_agent` varchar(150) COLLATE utf8_bin NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


-- Dumping structure for table eat.eat_webhook_notifications
CREATE TABLE IF NOT EXISTS `eat_webhook_notifications` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `evernote_user_id` int(11) DEFAULT NULL,
  `evernote_note_guid` varchar(48) DEFAULT NULL,
  `reason` varchar(15) DEFAULT NULL,
  `notified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `id_tag` int(11) DEFAULT NULL,
  `started` timestamp NULL DEFAULT NULL,
  `finished` timestamp NULL DEFAULT NULL,
  `was_eaten` bit(1) DEFAULT b'1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


-- Dumping structure for table eat.gmail_draft_label_keys
CREATE TABLE IF NOT EXISTS `gmail_draft_label_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang_code` varchar(5) DEFAULT NULL,
  `lang_name` varchar(45) DEFAULT NULL,
  `lang_literal` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- Dumping data for table eat.gmail_draft_label_keys: 10 rows
/*!40000 ALTER TABLE `gmail_draft_label_keys` DISABLE KEYS */;
INSERT INTO `gmail_draft_label_keys` (`id`, `lang_code`, `lang_name`, `lang_literal`) VALUES
	(1, 'ca-ES', 'Català', 'Esborranys'),
	(2, 'es-ES', 'Español', 'Borradores'),
	(3, 'en-US', 'English', 'Drafts'),
	(4, 'fr-FR', 'Français', 'Brouillons'),
	(5, 'de-DE', 'Deutsch', 'Entw&APw-rfe'),
	(6, 'it-IT', 'Italiano', 'Bozze'),
	(7, 'pt-BR', 'Português', 'Rascunhos'),
	(8, 'ru-RU', 'Русский', '&BCcENQRABD0EPgQyBDgEOgQ4-'),
	(9, 'zh-CN', '中文（简体）', '&g0l6Pw-'),
	(10, 'ja-JP', '日本語', '&Tgtm+DBN-');
/*!40000 ALTER TABLE `gmail_draft_label_keys` ENABLE KEYS */;


-- Dumping structure for table eat.language
CREATE TABLE IF NOT EXISTS `language` (
  `id_language` int(11) NOT NULL AUTO_INCREMENT,
  `keyname` varchar(60) NOT NULL,
  `iso` varchar(5) NOT NULL,
  `is_active` varchar(1) NOT NULL,
  `is_default` varchar(1) DEFAULT '0',
  PRIMARY KEY (`id_language`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- Dumping data for table eat.language: ~3 rows (approximately)
/*!40000 ALTER TABLE `language` DISABLE KEYS */;
INSERT INTO `language` (`id_language`, `keyname`, `iso`, `is_active`, `is_default`) VALUES
	(1, 'Español', 'es', '1', '0'),
	(2, 'Inglés', 'en', '1', '1'),
	(3, 'Català', 'ca', '0', '0');
/*!40000 ALTER TABLE `language` ENABLE KEYS */;


-- Dumping structure for table eat.latex_editor_analytics
CREATE TABLE IF NOT EXISTS `latex_editor_analytics` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `evernote_user_id` int(11) DEFAULT NULL,
  `times_editor_was_opened` int(11) DEFAULT '0',
  `times_formula_was_updated_to_evernote` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


-- Dumping structure for table eat.login_attempts
CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(40) COLLATE utf8_bin NOT NULL,
  `login` varchar(50) COLLATE utf8_bin NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Dumping structure for table eat.mailings
CREATE TABLE IF NOT EXISTS `mailings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title_key` varchar(50) NOT NULL,
  `desc_key` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- Dumping data for table eat.mailings: 2 rows
/*!40000 ALTER TABLE `mailings` DISABLE KEYS */;
INSERT INTO `mailings` (`id`, `title_key`, `desc_key`) VALUES
	(1, 'MLEATags', 'mailing_list_description'),
	(2, 'MLLatex', '');
/*!40000 ALTER TABLE `mailings` ENABLE KEYS */;


-- Dumping structure for table eat.note_fields
CREATE TABLE IF NOT EXISTS `note_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- Dumping data for table eat.note_fields: ~2 rows (approximately)
/*!40000 ALTER TABLE `note_fields` DISABLE KEYS */;
INSERT INTO `note_fields` (`id`, `name`) VALUES
	(1, 'content'),
	(2, 'resources');
/*!40000 ALTER TABLE `note_fields` ENABLE KEYS */;


-- Dumping structure for table eat.rate_limit_queue
CREATE TABLE IF NOT EXISTS `rate_limit_queue` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `evernote_user_id` int(11) DEFAULT NULL,
  `evernote_note_guid` varchar(48) DEFAULT NULL,
  `reason` varchar(10) DEFAULT NULL,
  `revoked_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `will_be_free_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


-- Dumping structure for table eat.tags
CREATE TABLE IF NOT EXISTS `tags` (
  `id_tag` int(11) NOT NULL AUTO_INCREMENT,
  `id_tag_feature` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `success_name` varchar(50) NOT NULL,
  `fail_name` varchar(50) NOT NULL,
  `match_mode` varchar(20) NOT NULL,
  `priority` tinyint(4) NOT NULL,
  `require_resources` varchar(1) NOT NULL,
  `model` varchar(50) NOT NULL,
  `is_active` varchar(1) NOT NULL,
  PRIMARY KEY (`id_tag`),
  KEY `id_tag_feature` (`id_tag_feature`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- Dumping data for table eat.tags: ~12 rows (approximately)
/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
INSERT INTO `tags` (`id_tag`, `id_tag_feature`, `name`, `description`, `success_name`, `fail_name`, `match_mode`, `priority`, `require_resources`, `model`, `is_active`) VALUES
	(1, 1, 'eat.wordpress.post', 'features_wp_post_description', 'eaten: [wordpress.post]', 'not_eaten: [wordpress post]', 'exact', 10, '1', 'eat/eat_wordpress', '1'),
	(2, 1, 'eat.wordpress.draft', 'features_wp_draft_description', 'eaten: [wordpress.draft]', 'not_eaten: [wordpress.draft]', 'exact', 10, '1', 'eat/eat_wordpress', '1'),
	(3, 2, 'eat.tweet', 'features_tweet_description', 'eaten: [tweet]', 'not_eaten: [tweet]', 'exact', 10, '0', 'eat/eat_twitter', '1'),
	(4, 3, 'eat.flickr', 'features_flickr_description', 'eaten: [flickr]', 'not_eaten: [flickr]', 'exact', 10, '1', 'eat/eat_flickr', '1'),
	(5, 4, 'eat.toc', 'features_toc_description', 'eaten: [toc]', 'not_eaten: [toc]', 'exact', 70, '0', 'eat/eat_toc', '1'),
	(6, 5, 'eat.toc.notebook', 'features_notebook_toc_description', 'eaten: [toc.notebook]', 'not_eaten: [toc.notebook]', 'exact', 70, '0', 'eat/eat_notebook_toc', '1'),
	(7, 6, 'eat.latex', 'features_latex_description', 'eaten: [latex]', 'not_eaten: [latex]', 'exact', 70, '1', 'eat/eat_latex', '1'),
	(8, 7, 'eat.add.header', 'features_add_header_description', 'eaten: [add.header]', 'not_eaten: [add.header]', 'exact', 70, '0', 'eat/eat_add', '1'),
	(9, 7, 'eat.add.footer', 'features_add_footer_description', 'eaten: [add.footer]', 'not_eaten: [add.footer]', 'exact', 70, '0', 'eat/eat_add', '1'),
	(10, 7, 'eat.add.surround', 'features_add_surround_description', 'eaten: [add.surround]', 'not_eaten: [add.surround]', 'exact', 70, '0', 'eat/eat_add', '1'),
	(11, 8, 'eat.gmail.draft', 'features_gmail_draft_description', 'eaten: [gmail.draft]', 'not_eaten: [gmail.draft]', 'exact', 10, '0', 'eat/eat_gmail', '1'),
	(12, 9, 'eat.toc.tag', 'features_toc_tag_description', 'eaten: [toc.tag]', 'not_eaten: [toc.tag]', 'exact', 70, '0', 'eat/eat_notebook_toc', '1');
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;


-- Dumping structure for view eat.tags_executed_by_user
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `tags_executed_by_user` (
	`username` VARCHAR(50) NULL COLLATE 'utf8_bin',
	`userid` INT(11) NULL,
	`evernote_user_id` INT(11) NULL,
	`tag_name` VARCHAR(50) NULL COLLATE 'latin1_swedish_ci',
	`id_tag` INT(11) NULL,
	`started` TIMESTAMP NULL,
	`finished` TIMESTAMP NULL,
	`was_eaten` BIT(1) NULL
) ENGINE=MyISAM;


-- Dumping structure for view eat.tags_failed_today
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `tags_failed_today` (
	`username` VARCHAR(50) NULL COLLATE 'utf8_bin',
	`userid` INT(11) NULL,
	`evernote_user_id` INT(11) NULL,
	`tag_name` VARCHAR(50) NULL COLLATE 'latin1_swedish_ci',
	`id_tag` INT(11) NULL,
	`started` TIMESTAMP NULL,
	`finished` TIMESTAMP NULL,
	`was_eaten` BIT(1) NULL
) ENGINE=MyISAM;


-- Dumping structure for table eat.tag_features
CREATE TABLE IF NOT EXISTS `tag_features` (
  `id_tag_feature` int(11) NOT NULL AUTO_INCREMENT,
  `keyname` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` mediumtext NOT NULL,
  `config_required` varchar(20) NOT NULL,
  `user_activated` varchar(1) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_tag_feature`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- Dumping data for table eat.tag_features: ~9 rows (approximately)
/*!40000 ALTER TABLE `tag_features` DISABLE KEYS */;
INSERT INTO `tag_features` (`id_tag_feature`, `keyname`, `name`, `description`, `config_required`, `user_activated`, `is_active`, `_order`) VALUES
	(1, 'wordpress', 'Wordpress', 'features_wp_long_desc', 'action_wordpress', '0', 1, 50),
	(2, 'twitter', 'Twitter', 'features_tweet_long_desc', 'action_twitter', '0', 1, 60),
	(3, 'flickr', 'Flickr', 'features_flickr_long_desc', 'action_flickr', '0', 1, 90),
	(4, 'toc', 'Toc', 'features_toc_long_desc', '', '1', 1, 20),
	(5, 'toc.notebook', 'Notebook ToC', 'features_notebook_toc_long_desc', '', '1', 1, 30),
	(6, 'latex', 'Latex', 'features_latex_long_desc', 'action_latex', '1', 1, 10),
	(7, 'add', 'Add', 'features_add_long_desc', 'action_add', '1', 1, 70),
	(8, 'gmail', 'Gmail', 'features_gmail_long_desc', 'action_gmail', '0', 1, 80),
	(9, 'toc.tag', 'Tag ToC', 'features_toc_tag_long_desc', '', '1', 1, 40);
/*!40000 ALTER TABLE `tag_features` ENABLE KEYS */;


-- Dumping structure for table eat.tag_options
CREATE TABLE IF NOT EXISTS `tag_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_tag` int(11) NOT NULL,
  `key` varchar(20) CHARACTER SET latin1 NOT NULL,
  `value` varchar(50) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_tag` (`id_tag`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- Dumping data for table eat.tag_options: ~8 rows (approximately)
/*!40000 ALTER TABLE `tag_options` DISABLE KEYS */;
INSERT INTO `tag_options` (`id`, `id_tag`, `key`, `value`) VALUES
	(1, 1, 'mode', 'POST'),
	(2, 2, 'mode', 'DRAFT'),
	(3, 8, 'mode', 'HEADER'),
	(4, 8, 'mode', 'FOOTER'),
	(5, 10, 'mode', 'SURROUND'),
	(6, 11, 'mode', 'DRAFT'),
	(7, 6, 'mode', 'NOTEBOOK'),
	(8, 12, 'mode', 'TAG');
/*!40000 ALTER TABLE `tag_options` ENABLE KEYS */;


-- Dumping structure for table eat.tag_update_note_fields
CREATE TABLE IF NOT EXISTS `tag_update_note_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_tag` int(11) NOT NULL,
  `id_field` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_tag` (`id_tag`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- Dumping data for table eat.tag_update_note_fields: ~4 rows (approximately)
/*!40000 ALTER TABLE `tag_update_note_fields` DISABLE KEYS */;
INSERT INTO `tag_update_note_fields` (`id`, `id_tag`, `id_field`) VALUES
	(1, 5, 1),
	(2, 6, 1),
	(3, 7, 1),
	(4, 7, 2);
/*!40000 ALTER TABLE `tag_update_note_fields` ENABLE KEYS */;


-- Dumping structure for table eat.testing_logging
CREATE TABLE IF NOT EXISTS `testing_logging` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `message` varchar(512) DEFAULT '',
  `method` varchar(255) DEFAULT NULL,
  `line` int(11) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `when` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Dumping structure for table eat.testing_logging_levels
CREATE TABLE IF NOT EXISTS `testing_logging_levels` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `level` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- Dumping data for table eat.testing_logging_levels: ~3 rows (approximately)
/*!40000 ALTER TABLE `testing_logging_levels` DISABLE KEYS */;
INSERT INTO `testing_logging_levels` (`id`, `level`) VALUES
	(1, 'ERROR'),
	(2, 'WARNING'),
	(3, 'INFO');
/*!40000 ALTER TABLE `testing_logging_levels` ENABLE KEYS */;


-- Dumping structure for table eat.testing_logging_types
CREATE TABLE IF NOT EXISTS `testing_logging_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- Dumping data for table eat.testing_logging_types: ~3 rows (approximately)
/*!40000 ALTER TABLE `testing_logging_types` DISABLE KEYS */;
INSERT INTO `testing_logging_types` (`id`, `type_name`) VALUES
	(1, 'EVERNOTE'),
	(2, 'TAG'),
	(3, 'UNKNOWN');
/*!40000 ALTER TABLE `testing_logging_types` ENABLE KEYS */;


-- Dumping structure for table eat.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8_bin NOT NULL,
  `password` varchar(255) COLLATE utf8_bin NOT NULL,
  `email` varchar(100) COLLATE utf8_bin NOT NULL,
  `activated` tinyint(1) NOT NULL DEFAULT '1',
  `banned` tinyint(1) NOT NULL DEFAULT '0',
  `ban_reason` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `new_password_key` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `new_password_requested` datetime DEFAULT NULL,
  `new_email` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `new_email_key` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `last_ip` varchar(40) COLLATE utf8_bin NOT NULL,
  `last_login` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `evernote_access_token` varchar(512) COLLATE utf8_bin DEFAULT NULL,
  `evernote_user_id` int(11) DEFAULT NULL,
  `evernote_note_store_url` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `evernote_web_api_url_prefix` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `evernote_token_expires` int(11) DEFAULT NULL,
  `is_test_user` int(11) NOT NULL DEFAULT '0' COMMENT '0 is FALSE, otherwise TRUE',
  `test_url` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `language` varchar(5) COLLATE utf8_bin NOT NULL DEFAULT 'en-US',
  `evernote_token_has_expired` tinyint(1) NOT NULL DEFAULT '0',
  `user_has_tags_updated` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


-- Dumping structure for view eat.users_active_evernote
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `users_active_evernote` (
	`id` INT(11) NOT NULL,
	`username` VARCHAR(50) NOT NULL COLLATE 'utf8_bin',
	`evernote_user_id` INT(11) NULL,
	`evernote_access_token` VARCHAR(512) NULL COLLATE 'utf8_bin',
	`user_has_tags_updated` TINYINT(1) NOT NULL
) ENGINE=MyISAM;


-- Dumping structure for table eat.users_historic
CREATE TABLE IF NOT EXISTS `users_historic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8_bin NOT NULL,
  `email` varchar(100) COLLATE utf8_bin NOT NULL,
  `activated` tinyint(1) NOT NULL DEFAULT '1',
  `banned` tinyint(1) NOT NULL DEFAULT '0',
  `ban_reason` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `new_password_key` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `new_password_requested` datetime DEFAULT NULL,
  `new_email` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `new_email_key` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `last_ip` varchar(40) COLLATE utf8_bin NOT NULL,
  `last_login` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `evernote_access_token` varchar(512) COLLATE utf8_bin DEFAULT NULL,
  `evernote_user_id` int(11) DEFAULT NULL,
  `evernote_note_store_url` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `evernote_web_api_url_prefix` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `evernote_token_expires` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


-- Dumping structure for view eat.user_active_features
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `user_active_features` (
	`evernote_user_id` INT(11) NULL,
	`keyname` VARCHAR(12) NOT NULL COLLATE 'utf8_general_ci'
) ENGINE=MyISAM;


-- Dumping structure for table eat.user_active_mailings
CREATE TABLE IF NOT EXISTS `user_active_mailings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `mailing_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_user_active_mailings_1_idx` (`user_id`),
  KEY `fk_user_active_mailings_2_idx` (`mailing_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


-- Dumping structure for table eat.user_autologin
CREATE TABLE IF NOT EXISTS `user_autologin` (
  `key_id` char(32) COLLATE utf8_bin NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `user_agent` varchar(150) COLLATE utf8_bin NOT NULL,
  `last_ip` varchar(40) COLLATE utf8_bin NOT NULL,
  `last_login` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`key_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


-- Dumping structure for table eat.user_google_auth
CREATE TABLE IF NOT EXISTS `user_google_auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `evernote_user_id` int(11) NOT NULL,
  `gmail_token` varchar(100) CHARACTER SET latin1 NOT NULL,
  `token_expiration` int(11) NOT NULL,
  `token_refresh` varchar(100) NOT NULL,
  `email` varchar(100) CHARACTER SET latin1 NOT NULL,
  `uid` varchar(100) CHARACTER SET latin1 NOT NULL,
  `type` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


-- Dumping structure for table eat.user_profiles
CREATE TABLE IF NOT EXISTS `user_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `country` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `website` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


-- Dumping structure for view eat.user_with_wordpress
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `user_with_wordpress` (
	`username` VARCHAR(50) NOT NULL COLLATE 'utf8_bin',
	`email` VARCHAR(100) NOT NULL COLLATE 'utf8_bin',
	`userid` INT(11) NOT NULL,
	`evernote_user_id` INT(11) NULL
) ENGINE=MyISAM;


-- Dumping structure for trigger eat.users_historic_trigger
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `users_historic_trigger` BEFORE DELETE ON `users` FOR EACH ROW BEGIN

INSERT INTO users_historic

SET

id = OLD.id,

username = OLD.username,

email = OLD.email,

activated = OLD.activated,

banned = OLD.banned,

ban_reason = OLD.ban_reason,

new_password_key = OLD.new_password_key,

new_password_requested = OLD.new_password_requested,

new_email = OLD.new_email,

new_email_key = OLD.new_email_key,

last_ip = OLD.last_ip,

last_login = OLD.last_login,

created = OLD.created,

deleted = NOW(),

evernote_access_token = OLD.evernote_access_token,

evernote_user_id = OLD.evernote_user_id,

evernote_note_store_url = OLD.evernote_note_store_url,

evernote_web_api_url_prefix = OLD.evernote_web_api_url_prefix,

evernote_token_expires = OLD.evernote_token_expires;

END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;


-- Dumping structure for view eat.tags_executed_by_user
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `tags_executed_by_user`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `tags_executed_by_user` AS select `users`.`username` AS `username`,`users`.`id` AS `userid`,`users`.`evernote_user_id` AS `evernote_user_id`,`tags`.`name` AS `tag_name`,`tags`.`id_tag` AS `id_tag`,`ewn`.`started` AS `started`,`ewn`.`finished` AS `finished`,`ewn`.`was_eaten` AS `was_eaten` from ((`eat_webhook_notifications` `ewn` left join `tags` on((`ewn`.`id_tag` = `tags`.`id_tag`))) left join `users` on((`ewn`.`evernote_user_id` = `users`.`evernote_user_id`))) where ((`ewn`.`id` > 525) and (`users`.`username` not in ('rubdottocom_dev','boskiman')));


-- Dumping structure for view eat.tags_failed_today
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `tags_failed_today`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `tags_failed_today` AS select `tags_executed_by_user`.`username` AS `username`,`tags_executed_by_user`.`userid` AS `userid`,`tags_executed_by_user`.`evernote_user_id` AS `evernote_user_id`,`tags_executed_by_user`.`tag_name` AS `tag_name`,`tags_executed_by_user`.`id_tag` AS `id_tag`,`tags_executed_by_user`.`started` AS `started`,`tags_executed_by_user`.`finished` AS `finished`,`tags_executed_by_user`.`was_eaten` AS `was_eaten` from `tags_executed_by_user` where ((`tags_executed_by_user`.`was_eaten` = 0) and (`tags_executed_by_user`.`finished` >= curdate()));


-- Dumping structure for view eat.users_active_evernote
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `users_active_evernote`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `users_active_evernote` AS select `u`.`id` AS `id`,`u`.`username` AS `username`,`u`.`evernote_user_id` AS `evernote_user_id`,`u`.`evernote_access_token` AS `evernote_access_token`,`u`.`user_has_tags_updated` AS `user_has_tags_updated` from `users` `u` where ((`u`.`evernote_token_has_expired` = 0) and (`u`.`evernote_token_expires` >= unix_timestamp(now())));


-- Dumping structure for view eat.user_active_features
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `user_active_features`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `user_active_features` AS select `u`.`evernote_user_id` AS `evernote_user_id`,'flickr' AS `keyname` from (`users` `u` join `action_flickr` `a` on((`a`.`evernote_user_id` = `u`.`evernote_user_id`))) where (`u`.`evernote_user_id` is not null) union all select `u`.`evernote_user_id` AS `evernote_user_id`,'twitter' AS `keyname` from (`users` `u` join `action_twitter` `a` on((`a`.`evernote_user_id` = `u`.`evernote_user_id`))) where (`u`.`evernote_user_id` is not null) union all select `u`.`evernote_user_id` AS `evernote_user_id`,'wordpress' AS `keyname` from (`users` `u` join `action_wordpress` `a` on((`a`.`evernote_user_id` = `u`.`evernote_user_id`))) where (`u`.`evernote_user_id` is not null) union all select `u`.`evernote_user_id` AS `evernote_user_id`,'toc' AS `keyname` from `users` `u` where (`u`.`evernote_user_id` is not null) union all select `u`.`evernote_user_id` AS `evernote_user_id`,'toc.notebook' AS `keyname` from `users` `u` where (`u`.`evernote_user_id` is not null) union all select `u`.`evernote_user_id` AS `evernote_user_id`,'latex' AS `keyname` from `users` `u` where (`u`.`evernote_user_id` is not null) union all select `u`.`evernote_user_id` AS `evernote_user_id`,'add' AS `keyname` from `users` `u` where (`u`.`evernote_user_id` is not null) union all select `u`.`evernote_user_id` AS `evernote_user_id`,'toc.tag' AS `keyname` from `users` `u` where (`u`.`evernote_user_id` is not null) union all select `u`.`evernote_user_id` AS `evernote_user_id`,'gmail' AS `keyname` from (`users` `u` join `user_google_auth` `uga` on(((`uga`.`evernote_user_id` = `u`.`evernote_user_id`) and (`uga`.`type` = 'gmail_draft')))) where (`u`.`evernote_user_id` is not null);


-- Dumping structure for view eat.user_with_wordpress
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `user_with_wordpress`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `user_with_wordpress` AS select `users`.`username` AS `username`,`users`.`email` AS `email`,`users`.`id` AS `userid`,`users`.`evernote_user_id` AS `evernote_user_id` from (`users` join `action_wordpress` `wp` on((`users`.`evernote_user_id` = `wp`.`evernote_user_id`))) where (`users`.`evernote_user_id` is not null);
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
