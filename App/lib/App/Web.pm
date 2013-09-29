package App::Web;

use strict;
use warnings;

use Kossy;
use utf8;
use Data::Dumper;
use Models::Model;
use JSON;


# モデル
my $model = Models::Model->new;

sub save_post {
    my $content = $_[0];

    $content = 'empty' if ! defined $content;

    my $result = $model->insert('tasks' => {
        'content' => $content,
        'created' => \'NOW()',
    });

    return $result;
}

sub get_posts {
    my $rows = $model->search('tasks', {
        'done_flg' => 0,
        'del_flg' => 0
        });
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

post '/delete' => sub {
    my ( $self, $c ) = @_;
    my $data = $c->req->parameters;
    my $taskId = $data->{id};
    
    my $result = {
        'status' => 0,
    };

    my $ret;
    $ret = $model->update(
        'tasks' => {
            'modified' => \'NOW()',
            'done_flg' => 1
        },
        {
            'id' => $taskId
        });

    if ($ret) {
        $result->{status} = 1;
    }

    my $json = to_json($result, { utf8 => 1});
    
    return $json;

};

post '/update' => sub {
    my ( $self, $c ) = @_;
    my $data = $c->req->parameters;
    my $taskId = $data->{id};
    my $content = $data->{content};
    
    my $result = {
        'status' => 0,
    };

    my $ret;
    $ret = $model->update(
        'tasks' => {
            'content' => $content,
            'modified' => \'NOW()'
        },
        {
            'id' => $taskId
        });

    if ($ret) {
        $result->{status} = 1;
    }

    my $json = to_json($result, { utf8 => 1});
    
    return $json;
    
};


1;

