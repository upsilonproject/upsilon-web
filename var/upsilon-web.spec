%include SPECS/.buildid.rpmmacro

Name:		upsilon-web
Version:	%{version_formatted_short}
Release:	%{timestamp}.%{?dist}
Summary:	Upsilon web
BuildArch: 	noarch

Group:		Applications/System
License:	GPLv2
URL:		http://upsilon-project.co.uk
Source0:	upsilon-web.zip

%if "%{dist}" == "el7scl"
Requires: httpd24-httpd php php-pdo php-mysql php-mbstring php-bcmath mariadb-server php-Smarty
%else
Requires: httpd php php-pdo php-mysql php-mbstring php-bcmath mariadb-server php-Smarty
%endif

%description
Upsilon web

%prep
%setup -q -n upsilon-web-%{tag}

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
cp setup/upsilon-alias-apache.conf %{buildroot}/etc/httpd/conf.d/upsilon-web-alias.conf

mkdir -p %{buildroot}/etc/upsilon-web/

%post 
service httpd restart
rm -rf /var/lib/php/session/*

%files
%doc /usr/share/doc/upsilon-web/README.md
%doc /usr/share/doc/upsilon-web/initialData.sql
%doc /usr/share/doc/upsilon-web/schema.sql
/usr/share/upsilon-web/*
/usr/share/upsilon-web/.buildid
%config(noreplace) /etc/httpd/conf.d/upsilon-web.conf
%config(noreplace) /etc/httpd/conf.d/upsilon-web-alias.conf
/etc/upsilon-web/

%changelog
* Thu Mar 05 2015 James Read <contact@jwread.com> 1.5.0-1
	Version 1.5.0
