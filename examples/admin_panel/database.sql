CREATE TABLE `user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL COMMENT 'email address',
  `status` enum('A','I') NOT NULL COMMENT 'A= Active, I=Inactive',
  `reset_pass_token` varchar(255) DEFAULT NULL COMMENT 'Reset password token',
  `reset_pass_dt` datetime DEFAULT NULL COMMENT 'Date/time of reset password request',
  `recover_question` varchar(255) DEFAULT NULL COMMENT 'Question to recover access',
  `recover_answer_hash` varchar(255) DEFAULT NULL COMMENT 'Crypt of answer for recover_question',
  `blocked` enum('Y','N') DEFAULT NULL COMMENT 'Indicates if account is blocked',
  `blocked_dt` datetime DEFAULT NULL COMMENT 'Datetime when account was blocked',
  `modified` datetime NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `reset_pass_token` (`reset_pass_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Registered Users';