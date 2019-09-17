# security-test

Install CentOS 6.5 Minimal

yum install httpd httpd-devel libxml2-devel openssl-devel curl-devel libjpeg-devel zlib-devel libpng-devel freetype-devel mysql mysql-devel
yum install wget yum-utils
yum groupinstall "development tools"
cd /usr/local/src
wget http://museum.php.net/php5/php-5.1.1.tar.gz
tar -xvf php-5.1.1.tar.gz
cd php-5.1.1
./configure   --prefix=/usr/local/php --with-apxs2=/usr/sbin/apxs --enable-mbstring --with-curl --with-openssl --with-xmlrpc --enable-soap --enable-zip --with-gd --with-jpeg-dir --with-png-dir --with-libdir=lib64 --with-mysqli --with-freetype-dir --with-ldap --enable-intl --with-zlib

Install NodeJS

yum install -y gcc-c++ make
curl -sL https://rpm.nodesource.com/setup_12.x | bash -