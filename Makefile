#
# Makefile for processing various aspects of application maintenace
#
# @author: Tamara Temple
# @version: $Id$
# @copyright: Tamara Temple Development, 2010-
# @package: comicgetter
#

repofiles = Makefile addsubscription.php db_init.inc functions.inc getcomic.php gocomics.inc index.php sample-config.inc \
	schema.sql style.css subscriptions.php \
	editsubscription.php deletesubscription.php mngcomics.php \
	.gitignore admin/clean_config.pl images/delete.jpg images/edit.jpg \
	templates/addeditsubscriptionform.tpl \
	templates/errors.tpl templates/footer.tpl templates/header.tpl templates/index.tpl templates/messages.tpl templates/nav.tpl templates/newcomics.tpl \
	templates/subscriptions.tpl templates/deletesubscription.tpl templates/mngcomics.tpl


updaterepo: admin/last_update
	git add $(repofiles)
	git commit # will launch an editor to create the commit message
	git push origin master
	touch admin/last_update
	
admin/last_update: $(repofiles)
	git add admin/last_update



sample-config.inc: config.inc
	admin/clean_config.pl config.inc > sample-config.inc