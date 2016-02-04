echo "Setting up libraries"

SMARTYDIR="src/main/php/includes/libraries/smarty"
rm -rf $SMARTYDIR
mkdir -p $SMARTYDIR
tar xaf lib/smarty.tar.gz --strip 2 -C $SMARTYDIR 'smarty-*/libs/'

rm -rf target/dojo-release-buildarea
mkdir -p target/dojo-release-buildarea
tar xaf lib/dojo-release.tgz --strip 1 -C target/dojo-release-buildarea 'dojo-release-*/'

composer install

echo "Setting up libraries: Completed"
