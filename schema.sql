/**
 * Schema for comicgetter application
 *
 * @author: Tamara Temple <tamara@tamaratemple.com>
 * @version: $Id$
 * @copyright: Tamara Temple Development, October 28, 2010
 * @package: comicgetter
 **/

/**
 * Table prefix is set in config.inc (see sample-config.inc)
 **/


DROP TABLE IF EXISTS `cg_subscriptions`;
CREATE TABLE `cg_subscriptions` (
	`id`	INT AUTO_INCREMENT NOT NULL,
	`name`	VARCHAR(50) NOT NULL,
	`uri`	VARCHAR(255) NOT NULL,
	`created`	TIMESTAMP NOT NULL DEFAULT 0,
	`updated`	TIMESTAMP NOT NULL DEFAULT 0 ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	KEY (`name`)
);

DROP TABLE IF EXISTS `cg_comics`;
CREATE TABLE `cg_comics` (
	`id`		INT AUTO_INCREMENT NOT NULL,
	`subscription_id` INT NOT NULL,
	`imgsrc`	VARCHAR(255) NOT NULL,
	`filespec`	VARCHAR(255) NOT NULL,
	`comicdate`	DATE NOT NULL,
	`pulltime`	TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	KEY (`comicdate`)
);

