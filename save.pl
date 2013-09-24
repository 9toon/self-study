#! /usr/local/bin/perl

use strict;
use warnings;

use CGI;
use DBI;



my $cgi = new CGI();

my $name = $cgi->param('name');

my $db = DBI->connect('DBI:mysql:study', 'admin', 'hy0528hy');

my $sql = "INSERT INTO study.user(name) VALUES('". $name ."')";

$db->do($sql);


print $cgi->header(-type=>'text/html', -charset=>'utf-8');
print $name;

