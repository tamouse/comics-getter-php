#
# Makefile for processing various aspects of application maintenace
#
# @author: Tamara Temple
# @version: $Id$
# @copyright: Tamara Temple Development, 2010-
# @package: comicgetter
#

repofiles = \
Makefile \
addeditsubscription.php \
db_init.inc \
deletesubscription.php \
functions.inc \
getcomic.php \
gocomics.inc \
index.php \
mngcomics.php \
rss.php \
sample-config.inc \
schema.sql \
style.css \
subscriptions.php \
.gitignore \
admin/clean_config.pl \
images/delete.jpg \
images/edit.jpg \
templates/addeditsubscriptionform.tpl \
templates/deletesubscription.tpl \
templates/errors.tpl \
templates/footer.tpl \
templates/header.tpl \
templates/index.tpl \
templates/messages.tpl \
templates/mngcomics.tpl \
templates/nav.tpl \
templates/newcomics.tpl \
templates/rss.tpl \
templates/subscriptions.tpl


updaterepo: admin/last_update
	git commit # will launch an editor to create the commit message
	git push origin master

	
admin/last_update: $(repofiles)
	git add $?
	touch admin/last_update
	git add admin/last_update



sample-config.inc: config.inc
	admin/clean_config.pl config.inc > sample-config.inc