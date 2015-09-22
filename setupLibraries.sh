echo "Setting up libraries"

SMARTYDIR="src/main/php/includes/libraries/smarty"
rm -rf $SMARTYDIR
mkdir -p $SMARTYDIR
tar xaf lib/smarty.tar.gz --strip 2 -C $SMARTYDIR 'smarty-*/libs/'

rm -rf target/dojo-release-buildarea
mkdir -p target/dojo-release-buildarea
tar xaf lib/dojo-release.tgz --strip 1 -C target/dojo-release-buildarea 'dojo-release-*/'

rm -rf src/main/php/resources/dojo/gridx
mkdir -p src/main/php/resources/dojo/gridx
tar xaf lib/gridx.tar.gz --strip 1 -C src/main/php/resources/dojo/gridx/ 'gridx-*/'

echo "Setting up libraries: Completed"
