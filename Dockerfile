FROM registry.redhat.io/ubi8/php-74

USER 0

RUN yum install -y php-bcmath php-mysqlnd php-pdo php-mbstring php-xml

ADD var/upsilon-apache.conf /etc/httpd/conf.d/
ADD var/upsilon-alias-apache.conf /etc/httpd/conf.d/
RUN rm /etc/httpd/conf.d/welcome.conf
RUN echo "Alias / /usr/share/upsilon-web/" > /etc/httpd/conf.d/upsilon-web-alias.conf

USER 1001

ADD src/main/php/ /usr/share/upsilon-web/
ADD composer.json /usr/share/upsilon-web/

RUN cd /usr/share/upsilon-web && \
	TEMPFILE=$(mktemp) && \
    curl -o "$TEMPFILE" "https://getcomposer.org/installer" && \
    php <"$TEMPFILE" && \
    ./composer.phar install --no-interaction --no-ansi --optimize-autoloader

RUN mv /usr/share/upsilon-web/src/main/php/includes/libraries /usr/share/upsilon-web/includes/


CMD /usr/libexec/s2i/run
