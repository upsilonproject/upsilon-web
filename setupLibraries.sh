echo "Setting up libraries"

rm -rf target/dojo-release-buildarea
mkdir -p target/dojo-release-buildarea
tar xaf lib/dojo-release.tgz --strip 1 -C target/dojo-release-buildarea 'dojo-release-*/'

composer install

echo "Setting up libraries: Completed"
