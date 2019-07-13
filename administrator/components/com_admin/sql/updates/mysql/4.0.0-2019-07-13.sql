INSERT INTO `#__extensions` (`package_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES
(0, 'mod_loginsupport', 'module', 'mod_loginsupport', '', 1, 1, 1, 1, '', '', 0, '0000-00-00 00:00:00', 0, 0),
(0, 'mod_frontend', 'module', 'mod_frontend', '', 1, 1, 1, 0, '', '', 0, '0000-00-00 00:00:00', 0, 0),
(0, 'mod_messages', 'module', 'mod_messages', '', 1, 1, 1, 0, '', '', 0, '0000-00-00 00:00:00', 0, 0),
(0, 'mod_post_installation_messages', 'module', 'mod_post_installation_messages', '', 1, 1, 1, 0, '', '', 0, '0000-00-00 00:00:00', 0, 0),
(0, 'mod_user', 'module', 'mod_user', '', 1, 1, 1, 0, '', '', 0, '0000-00-00 00:00:00', 0, 0),
(0, 'mod_submenu', 'module', 'mod_submenu', '', 1, 1, 1, 0, '', '{}', 0, '0000-00-00 00:00:00', 0, 0);

DELETE FROM `#__extensions` WHERE `element` = 'mod_status' AND `client_id` = 1;


INSERT INTO `#__modules` (`title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES
('Login Support', '', '', 1, 'sidebar', 0, NULL, NULL, NULL, 1, 'mod_loginsupport', 1, 1, '{"forum_url":"https://forum.joomla.org/","documentation_url":"https://docs.joomla.org/","news_url":"https://www.joomla.org/","automatic_title":1,"prepare_content":1,"layout":"_:default","moduleclass_sfx":"","cache":1,"cache_time":900,"module_tag":"div","bootstrap_size":"0","header_tag":"h3","header_class":"","style":"0"}', 1, '*');
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (LAST_INSERT_ID(), 0);

INSERT INTO `#__modules` (`title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES
('System Submenu', '', NULL, 1, 'cpanel-system', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_submenu', 1, 0, '{"menutype":"*","preset":"system","layout":"_:default","moduleclass_sfx":"","module_tag":"div","bootstrap_size":"12","header_tag":"h3","header_class":"","style":"System-none"}', 1, '*');
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (LAST_INSERT_ID(), 0);

INSERT INTO `#__modules` (`title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES
('Content Submenu', '', NULL, 1, 'cpanel-content', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_submenu', 1, 0, '{"menutype":"*","preset":"content","layout":"_:default","moduleclass_sfx":"","module_tag":"div","bootstrap_size":"3","header_tag":"h3","header_class":"","style":"System-none"}', 1, '*');
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (LAST_INSERT_ID(), 0);

INSERT INTO `#__modules` (`title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES
('Menus Submenu', '', NULL, 1, 'cpanel-menus', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_submenu', 1, 0, '{"menutype":"*","preset":"menus","layout":"_:default","moduleclass_sfx":"","module_tag":"div","bootstrap_size":"6","header_tag":"h3","header_class":"","style":"System-none"}', 1, '*');
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (LAST_INSERT_ID(), 0);

INSERT INTO `#__modules` (`title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES
('Components Submenu', '', NULL, 1, 'cpanel-components', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_submenu', 1, 0, '{"menutype":"*","preset":"components","layout":"_:default","moduleclass_sfx":"","module_tag":"div","bootstrap_size":"12","header_tag":"h3","header_class":"","style":"System-none"}', 1, '*');
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (LAST_INSERT_ID(), 0);

INSERT INTO `#__modules` (`title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES
('Users Submenu', '', NULL, 1, 'cpanel-users', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_submenu', 1, 0, '{"menutype":"*","preset":"users","layout":"_:default","moduleclass_sfx":"","module_tag":"div","bootstrap_size":"6","header_tag":"h3","header_class":"","style":"System-none"}', 1, '*');
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (LAST_INSERT_ID(), 0);

INSERT INTO `#__modules` (`title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES
('Popular Articles', '', '', 3, 'cpanel-content', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_popular', 3, 1, '{"count":"5","catid":"","user_id":"0","layout":"_:default","moduleclass_sfx":"","cache":"0", "bootstrap_size": "6"}', 1, '*');
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (LAST_INSERT_ID(), 0);

INSERT INTO `#__modules` (`title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES
('Recently Added Articles', '', '', 4, 'cpanel-content', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_latest', 3, 1, '{"count":"5","ordering":"c_dsc","catid":"","user_id":"0","layout":"_:default","moduleclass_sfx":"","cache":"0", "bootstrap_size": "6"}', 1, '*');
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (LAST_INSERT_ID(), 0);

INSERT INTO `#__modules` (`title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES
('Logged-in Users', '', '', 2, 'cpanel-users', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_logged', 3, 1, '{"count":"5","name":"1","layout":"_:default","moduleclass_sfx":"","cache":"0", "bootstrap_size": "6"}', 1, '*');
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (LAST_INSERT_ID(), 0);

INSERT INTO `#__modules` (`title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES
('Frontend Link', '', '', 5, 'status', 0, NULL, NULL, NULL, 1, 'mod_frontend', 1, 1, '', 1, '*');
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (LAST_INSERT_ID(), 0);

INSERT INTO `#__modules` (`title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES
('Messages', '', '', 4, 'status', 0, NULL, NULL, NULL, 1, 'mod_messages', 3, 1, '', 1, '*');
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (LAST_INSERT_ID(), 0);

INSERT INTO `#__modules` (`title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES
('Post Install Messages', '', '', 3, 'status', 0, NULL, NULL, NULL, 1, 'mod_post_installation_messages', 3, 1, '', 1, '*');
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (LAST_INSERT_ID(), 0);

INSERT INTO `#__modules` (`title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES
('User Status', '', '', 6, 'status', 0, NULL, NULL, NULL, 1, 'mod_user', 3, 1, '', 1, '*');
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (LAST_INSERT_ID(), 0);

INSERT INTO `#__modules` (`title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES
('Site', '', NULL, 1, 'icon', 0, NULL, NULL, NULL, 1, 'mod_quickicon', 1, 1, '{"context":"site_quickicons","header_icon":"fa fa-desktop","show_users":"1","show_articles":"1","show_categories":"1","show_media":"1","show_menuItems":"1","show_modules":"1","show_plugins":"1","show_templates":"1","layout":"_:default","moduleclass_sfx":"","cache":1,"cache_time":900,"style":"0","module_tag":"div","bootstrap_size":"6","header_tag":"h3","header_class":""}', 1, '*');
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (LAST_INSERT_ID(), 0);

INSERT INTO `#__modules` (`title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES
('System', '', NULL, 2, 'icon', 0, NULL, NULL, NULL, 1, 'mod_quickicon', 1, 1, '{"context":"system_quickicon","header_icon":"fa fa-wrench","show_global":"1","show_checkin":"1","show_cache":"1","layout":"_:default","moduleclass_sfx":"","cache":1,"cache_time":900,"style":"0","module_tag":"div","bootstrap_size":"6","header_tag":"h3","header_class":""}', 1, '*');
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (LAST_INSERT_ID(), 0);

INSERT INTO `#__modules` (`title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES
('3rd Party', '', NULL, 4, 'icon', 0, NULL, NULL, NULL, 1, 'mod_quickicon', 1, 1, '{"context":"mod_quickicon","header_icon":"fa fa-boxes","load_plugins":"1","layout":"_:default","moduleclass_sfx":"","cache":1,"cache_time":900,"style":"0","module_tag":"div","bootstrap_size":"6","header_tag":"h3","header_class":""}', 1, '*');
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (LAST_INSERT_ID(), 0);

UPDATE `#__modules` SET `title` = 'Update Checks',`ordering` = 3,`position` = 'icon',`showtitle` = 1,`params` = '{"context":"update_quickicon","header_icon":"fa fa-sync","show_global":"0","show_checkin":"0","show_cache":"0","show_users":"0","show_articles":"0","show_categories":"0","show_media":"0","show_menuItems":"0","show_modules":"0","show_plugins":"0","show_templates":"0","layout":"_:default","moduleclass_sfx":"","cache":1,"cache_time":900,"style":"0","module_tag":"div","bootstrap_size":"12","header_tag":"h3","header_class":""}' WHERE `#__modules`.`id` = 9;

INSERT INTO `#__modules` (`title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES
('Help Submenu', '', NULL, 1, 'cpanel-help', 0, NULL, NULL, NULL, 1, 'mod_submenu', 1, 0, '{\"menutype\":\"*\",\"preset\":\"help\",\"layout\":\"_:default\",\"moduleclass_sfx\":\"\",\"style\":\"System-none\",\"module_tag\":\"div\",\"bootstrap_size\":\"0\",\"header_tag\":\"h3\",\"header_class\":\"\"}', 1, '*');
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (LAST_INSERT_ID(), 0);

UPDATE `#__modules` SET `ordering` = 2,`position` = 'status' WHERE `#__modules`.`id` = 79;
