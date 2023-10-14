FROM debian:11

RUN \
	set -x &&\
	apt-get -y update &&\
	apt-get -y upgrade &&\
	apt-get -y install git apache2 php

RUN \
	set -x &&\
	useradd -G www-data -m user &&\
	mkdir -p /var/www/db/utad0/data /var/www/db/utad0/appl &&\
	chown user:www-data /var/www/db/utad0/data /var/www/db/utad0/appl &&\
	chmod g+rws /var/www/db/utad0/data /var/www/db/utad0/appl

RUN \
	set -x &&\
	su -c 'git clone https://github.com/paijp/unet-btron.git /var/www/db/utad0/appl/68747470733a2f2f6769746875622e636f6d2f7061696a702f756e65742d6274726f6e2e676974' user &&\
	su -c 'git clone https://github.com/paijp/utad.git /var/www/db/utad0/data/68747470733a2f2f6769746875622e636f6d2f7061696a702f757461642e676974' user &&\
	ln -s /var/www/db/utad0/appl/68747470733a2f2f6769746875622e636f6d2f7061696a702f756e65742d6274726f6e2e676974/base/index.php /var/www/html/ &&\
	ln -s /var/www/db/utad0/appl/68747470733a2f2f6769746875622e636f6d2f7061696a702f756e65742d6274726f6e2e676974/base/env.php /var/www/html/ &&\
	chmod -R g+rws /var/www/db/utad0/data

RUN \
	set -x &&\
	rm /var/www/html/index.html

EXPOSE 80

CMD /etc/init.d/apache2 start &&tail -f /var/log/apache2/access.log
