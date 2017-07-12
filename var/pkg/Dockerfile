FROM centos/httpd:latest

RUN yum install -y https://dl.fedoraproject.org/pub/epel/7/x86_64/e/epel-release-7-10.noarch.rpm
ADD http://repos.upsilon-project.co.uk/upsilon-rpm-el7/upsilon-rpm-el7.repo /etc/yum.repos.d/
ADD RPMS/noarch/upsilon-web.rpm /opt/
RUN yum localinstall /opt/upsilon-web.rpm -y

