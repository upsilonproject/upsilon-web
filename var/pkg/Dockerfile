FROM centos/httpd-24-centos7:latest

USER root

#RUN yum install -y https://dl.fedoraproject.org/pub/epel/7/x86_64/e/epel-release-7-10.noarch.rpm
RUN yum install -y epel-release
RUN rm /etc/httpd/conf.d/welcome.conf
#RUN chmod o+rx /var/log/httpd24 && chmod o+r /var/log/http24/*
ADD http://repos.upsilon-project.co.uk/upsilon-rpm-el7/upsilon-rpm-el7.repo /etc/yum.repos.d/
ADD RPMS/noarch/upsilon-web.rpm /opt/
RUN yum localinstall /opt/upsilon-web.rpm -y
RUN echo "Alias / /usr/share/upsilon-web/" > /etc/httpd/conf.d/upsilon-web-alias.conf

USER 1001
