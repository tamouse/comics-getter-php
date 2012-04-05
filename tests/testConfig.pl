#!/usr/bin/perl -w
#
# testConfig - test reading the configuration file
#
# Author: Tamara Temple <tamara@tamaratemple.com>
# Created: 2012/04/04
# Time-stamp: <2012-04-04 20:56:42 tamara>
# Copyright (c) 2012 Tamara Temple Web Development
# License: GPLv3
#

use strict;
use Config::Simple;
use Data::Dumper::Names;

my $cfg = Config::Simple->import_from('../config.ini',\my %Config);
print "\%Config: ".Dumper(%Config);
print join("\n",sort(keys (%Config))) . "\n";
for (sort(keys (%Config)))  {
    printf("%s: %s\n",$_,$Config{$_});
}


print "comicsdir: " . $Config{'default.comicsdir'} . "\n";

