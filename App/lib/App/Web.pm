package App::Web;

use strict;
use warnings;
use utf8;
use Kossy;

use utf8;
use Teng;
use Teng::Schema::Loader;
use Data::Dumper;

my $dsn = 'dbi:mysql:dena';
my $user = 'admin';
my $pass = 'hy0528hy';

my $dbh = DBI->connect($dsn, $user, $pass, {
    'mysql_enable_urf8' => 1,
});

my $teng = Teng::Schema::Loader->load(
    'dbh' => $dbh,
    'namespace' => 'App::DB',
);

sub save_post {
    my $content = $_[0];

    $content = 'empty' if ! defined $content;

    my $result = $teng->insert('memo' => {
        'content' => $content,
        'created' => \'NOW()',
    });

    return $result;
}

sub get_posts {
    my $rows = $teng->search('memo', {});
    my $posts = $rows->all;

    return $posts;
}


filter 'set_title' => sub {
    my $app = shift;
    sub {
        my ( $self, $c )  = @_;
        $c->stash->{site_name} = __PACKAGE__;
        $app->($self,$c);
    }
};

get '/' => [qw/set_title/] => sub {
    my ( $self, $c )  = @_;

    my $posts = get_posts();

    $c->render('index.tx', { posts => $posts });
};

get '/json' => sub {
    my ( $self, $c )  = @_;
    my $result = $c->req->validator([
        'q' => {
            default => 'Hello',
            rule => [
                [['CHOICE',qw/Hello Bye/],'Hello or Bye']
            ],
        }
    ]);
    $c->render_json({ greeting => $result->valid->get('q') });
};

post '/' => sub {
    my ( $self, $c ) = @_;

    my $data = $c->req->parameters;
    my $content = $data->{"content"};

    my $id = save_post($content);

    return $c->redirect('/');
};

1;

