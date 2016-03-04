#! /bin/bash

function install_extension() {
	wget https://github.com/wikimedia/mediawiki-extensions-$1/archive/master.tar.gz
	tar -zxf master.tar.gz
	rm master.tar.gz
	mv mediawiki-extensions-$1-master wiki/extensions/$1

	cd wiki/extensions/$1

	if [ -f 'composer.json' ];
	then
		composer install --no-interaction --prefer-source
	fi

	cd ../../..
}

set -x

# install elasticsearch
curl -O https://download.elasticsearch.org/elasticsearch/elasticsearch/elasticsearch-1.7.1.deb && sudo dpkg -i --force-confnew elasticsearch-1.7.1.deb
sudo /usr/share/elasticsearch/bin/plugin --install org.wikimedia.search/extra/1.7.1
sudo service elasticsearch restart

sleep 10

originalDirectory=$(pwd)

composer self-update

cd ..

# checkout mediawiki
wget https://github.com/wikimedia/mediawiki/archive/master.tar.gz
tar -zxf master.tar.gz
rm master.tar.gz
mv mediawiki-master wiki

cd wiki

if [ $DBTYPE == "mysql" ]
  then
    mysql -e 'CREATE DATABASE its_a_mw;'
fi

composer install
php maintenance/install.php --dbtype $DBTYPE --dbuser root --dbname its_a_mw --dbpath $(pwd) --pass nyan TravisWiki admin

cd ..

install_extension 'Elastica'
install_extension 'CirrusSearch'
install_extension 'Wikibase'

cd wiki/extensions

cp -r $originalDirectory WikibaseElastic

cd WikibaseElastic
composer install --no-interaction --prefer-source

cd ../..

echo 'error_reporting(E_ALL| E_STRICT);' >> LocalSettings.php
echo 'ini_set("display_errors", 1);' >> LocalSettings.php
echo '$wgShowExceptionDetails = true;' >> LocalSettings.php
echo '$wgDevelopmentWarnings = true;' >> LocalSettings.php
echo '$wgLanguageCode = "en";' >> LocalSettings.php

echo 'require_once __DIR__ . "/extensions/Elastica/Elastica.php";' >> LocalSettings.php
echo 'require_once __DIR__ . "/extensions/CirrusSearch/CirrusSearch.php";' >> LocalSettings.php
echo 'require_once __DIR__ . "/extensions/Wikibase/repo/Wikibase.php";' >> LocalSettings.php
echo 'require_once __DIR__ . "/extensions/Wikibase/repo/ExampleSettings.php";' >> LocalSettings.php
echo 'require_once __DIR__ . "/extensions/Wikibase/client/WikibaseClient.php";' >> LocalSettings.php
echo 'require_once __DIR__ . "/extensions/WikibaseElastic/WikibaseElastic.php";' >> LocalSettings.php
echo '$wgWBClientSettings["siteGlobalID"] = "enwiki";' >> LocalSettings.php

php maintenance/update.php --quick
