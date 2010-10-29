#!/sw/bin/perl -n

#
# clean_config.pl - clean the config.inc file and write to stdout
# (to be used in a make update to create a safe sample-config.inc without passwords and local data)
#
# @author: Tamara Temple
# @version: $Id$
# @copyright: Tamara Temple Development, 2010-
# @license: LGPL
# @package: comicgetter
#

# This program is a filter -- it is intended to be run such that an implicit while loop is run on each line of the input file(s)

/DBHOST|DBNAME|DBPASS|DBUSER/ && s/,(['"])[^'"]*(['"])\);/,\1\2);/g;
/DEBUG/ && s/TRUE/FALSE/;
print $_;
