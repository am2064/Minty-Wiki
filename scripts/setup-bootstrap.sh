#!/bin/sh

wget -c http://twitter.github.com/bootstrap/assets/bootstrap.zip
unzip bootstrap.zip
rm bootstrap.zip

echo "body {padding-top: 60px;}" > bootstrap/css/bootstrap-extra.css

mv bootstrap ..

echo "Please change \$css to:\n\t\$css=array(\"bootstrap/css/bootstrap.css\",\"bootstrap/css/bootstrap-responsive.css\",\"bootstrap/css/bootstrap-extra.css\");\nand \$exceptions to: \n\t\$exceptions=array(\"bootstrap\",\"scripts\");";