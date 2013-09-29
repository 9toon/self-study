package Models::Model;
# モデル基底クラス

use strict;
use warnings;
use YAML();
use parent 'Teng';
use Teng::Schema::Loader;
use File::Spec;
use File::Basename qw(dirname);

# 設定ファイルの読み込み
my $root_dir = File::Spec->rel2abs(File::Spec->catdir(dirname(__FILE__), '../../'));
my $config_file = "$root_dir/config/database.yml";
my ($dsn, $user, $passwd) = load_config();

# 接続
my $dbh = DBI->connect($dsn, $user, $passwd, {
        'mysql_enable_utf8' => 1,
    });

my $db = Teng::Schema::Loader->load(
    'dbh'       => $dbh,
    'namespace' => 'Models',
);

# コンストラクタ
sub new {
    my $class = shift;
    return bless $db, $class;
}

# 設定ファイルの読み込み
sub load_config {
    open(IN, $config_file) or die("cannot open file.");
    read(IN, my $input, (-s $config_file));
    close(IN);
    my $ydoc = YAML::Load($input);
    my $user = $ydoc->{database}->{user};
    my $passwd = $ydoc->{database}->{password};
    my $dsn = $ydoc->{database}->{dsn};
    return ($dsn, $user, $passwd);
}

1;

