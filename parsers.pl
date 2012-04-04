#!/usr/bin/perl -w
#
# parsers - comic page parsers
#
# Author: Tamara Temple <tamara@tamaratemple.com>
# Created: 2012/04/04
# Time-stamp: <2012-04-04 14:09:59 tamara>
# Copyright (c) 2012 Tamara Temple Web Development
# License: GPLv3
#

use strict;

my $debug = 1;

use LWP::UserAgent;
use HTML::Parser;
use Data::Dumper::Names;

while (my $comicUrl = shift @ARGV) {
    my $images;
    $_ = $comicUrl;
  SWITCH: {
      /\b(go)?comics\.com\b/i  && do {($images) = parseGoComics($comicUrl); last SWITCH;};
      /\btwolumps\.net\b/i  && do {($images) = parseTwoLumps($comicUrl); last SWITCH;};
      # default:
      warn "Unknown Comic Source: $comicUrl\n";
    }
    if ($images) {
	warn "main: \@\$images: ", Dumper(@$images) if $debug;
	print join("\n",@$images) . "\n";
    } else {
	die "No images returned\n";
    }
}

sub get_page {
    my $url = shift;
    warn "get_page: \$url=$url" if $debug;
    my $ua = LWP::UserAgent->new(
	agent => "Mozilla",
	timeout => 30,
	);
    $ua->show_progress(1) if $debug;
    my $response = $ua->get($url);
    unless ($response->is_success) {
	warn $url . ' returned ' . $response->status_line;
	return 0;
    }
    return $response->decoded_content;
}

sub parseGoComics {
    my $url = shift;
    warn "parseGoComics: \$url=$url" if $debug;
    my $content = get_page($url);
    return unless $content;
    my @images;
    my $p = HTML::Parser->new(
	api_version => 3,
	start_h => [ sub {
	    my($self, $tag, $attr) = @_;
	    return undef unless $tag eq "img";
	    return undef unless exists $attr->{src};
	    return undef unless exists $attr->{class} && $attr->{class} eq "strip";
	    push @images, [$attr->{src}];
		     },
		     'self, tagname, attr'],
	);
    $p->parse($content);
    warn "parseGoComics: ". Dumper(@images) if $debug;
    return @images;
}

