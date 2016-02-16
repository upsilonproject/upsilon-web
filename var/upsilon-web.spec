Name:		upsilon-web
Version:	%{buildid_version}
Release:	%{buildid_timestamp}%{?dist}
Summary:	Upsilon web

Group:		Applications/System
License:	GPLv2
URL:		http://upsilon-project.co.uk
Source0:	upsilon-web-%{buildid_tag}.zip

Requires: httpd php php-pdo php-mysql php-mbstring mariadb-server

%description
Upsilon web

%prep
%setup -q -n upsilon-web-%{buildid_tag}

%build 
mkdir -p %{buildroot}/usr/share/upsilon-web/
cp -r upload/*  %{buildroot}/usr/share/upsilon-web/

cp .buildid %{buildroot}/usr/share/upsilon-web/

mkdir -p %{buildroot}/usr/share/doc/upsilon-web/
cp README.md %{buildroot}/usr/share/doc/upsilon-web/
cp setup/initialData.sql %{buildroot}/usr/share/doc/upsilon-web/
cp setup/schema.sql %{buildroot}/usr/share/doc/upsilon-web/

mkdir -p %{buildroot}/etc/httpd/conf.d
cp setup/upsilon-apache.conf %{buildroot}/etc/httpd/conf.d/upsilon-web.conf

%post 
service httpd restart

%files
%doc /usr/share/doc/upsilon-web/README.md
%doc /usr/share/doc/upsilon-web/initialData.sql
%doc /usr/share/doc/upsilon-web/schema.sql
/usr/share/upsilon-web/*
/usr/share/upsilon-web/.buildid
/etc/httpd/conf.d/upsilon-web.conf

%changelog
* Thu Mar 05 2015 James Read <contact@jwread.com> 1.5.0-1
	Version 1.5.0
