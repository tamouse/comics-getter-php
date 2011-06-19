<?php
if (defined('STDIN')) {
print "CLI";
} else {
print "CGI";
}
print "\n";
?>