#!/usr/bin/perl -w
#
# parsers - comic page parsers
#
# Author: Tamara Temple <tamara@tamaratemple.com>
# Created: 2012/04/04
# Time-stamp: <2012-04-04 22:42:59 tamara>
# Copyright (c) 2012 Tamara Temple Web Development
# License: GPLv3
#

use strict;

my $debug = 0;

use Config::Simple;
use LWP::UserAgent;
use Fetch::Image;
use HTML::Parser;
use Try::Tiny;
use Data::Dumper::Names;
use File::Basename;
use Date::Format;

my $cfg = Config::Simple->import_from('config.ini',\my %Config);
my $comicsdir = $Config{'default.comicsdir'};
warn "\$comicsdir: $comicsdir\n" if $debug;

my %typehash = (
    'image/jpeg' => 'jpg',
    'image/jpg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif',
    );

while (my $comicUrl = shift @ARGV) {
    my $imageUrl;
    my $ext;
    $_ = $comicUrl;
  SWITCH: {
      /\b(go)?comics\.com\b/i  && do {$imageUrl = parseGoComics($comicUrl); last SWITCH;};
      /\btwolumps\.net\b/i  && do {$imageUrl = parseTwoLumps($comicUrl); last SWITCH;};
      # default:
      warn "Unknown Comic Source: $comicUrl\n";
    }
    if ($imageUrl) {
	my $r = fetchUrl($imageUrl);
	my $content_type = $r->{_headers}->{'content-type'};
	warn "main: \$content_type=$content_type" if $debug;
	if (exists($typehash{$content_type})) {
	    $ext = $typehash{$content_type};
	} else {
	    $ext = 'dat';
	}

	my $newfname = $comicsdir . '/' .
	    basename($comicUrl) .
	    time2str("%Y%m%d%H%M%S",time) .
	    '.' . $ext;
	
	# save contents
	open(FH,">",$newfname) or die "Unable to open $newfname: $!";
	binmode FH;
	print FH $r->content();
	close FH;
	warn Dumper(stat($newfname)) if $debug;


	printf("%s\t%s\n", $imageUrl, $newfname);
    } else {
	die "No images returned\n";
    }
}

sub fetchUrl {
    my $url = shift;
    warn "fetchUrl: \$url=$url" if $debug;
    my $ua = LWP::UserAgent->new(
	agent => "Mozilla",
	timeout => 30,
	);
    $ua->show_progress(1) if $debug;
    my $response = $ua->get($url);
    unless ($response->is_success) {
	warn "fetchUrl: $url" . ' returned ' . $response->status_line;
	return undef;
    }
    return $response;
}

sub parseGoComics {
    my $url = shift;
    warn "parseGoComics: \$url=$url" if $debug;
    my $response = fetchUrl($url);
    return undef unless($response);
    
    my $content=$response->decoded_content;
    my $imageUrl;
    my $p = HTML::Parser->new(
	api_version => 3,
	start_h => [ sub {
	    my($self, $tag, $attr) = @_;
	    return undef unless $tag eq "img";
	    return undef unless exists $attr->{src};
	    return undef unless exists $attr->{class} && $attr->{class} eq "strip" &&
		$attr->{src} =~ m|http://cdn.svcs.c2.uclick.com/|;
	    $imageUrl = $attr->{src};
		     },
		     'self, tagname, attr'],
	);
    $p->parse($content);
    warn "parseGoComics: ". Dumper($imageUrl) if $debug;
    return $imageUrl;
}

