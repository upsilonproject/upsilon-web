Name:		upsilon-web
Version:	1.5.0
Release:	1%{?dist}
Summary:	Upsilon web

Group:		Applications/System
License:	GPLv2
URL:		http://upsilon-project.co.uk
Source0:	upsilon-web-%{version}.zip

%description
Upsilon web

%prep
%setup -q


%build
mkdir -p %{buildroot}/usr/share/upsilon-web/
cp -r upload/*  %{buildroot}/usr/share/upsilon-web/

mkdir -p %{buildroot}/usr/share/doc/upsilon-web/
cp README.md %{buildroot}/usr/share/doc/upsilon-web/
cp setup/initialData.sql %{buildroot}/usr/share/doc/upsilon-web/

mkdir -p %{buildroot}/etc/httpd/conf.d
cp setup/upsilon-apache.conf %{buildroot}/etc/httpd/conf.d/upsilon.conf

%post 
service httpd restart

%files
%doc /usr/share/doc/upsilon-web/README.md
%doc /usr/share/doc/upsilon-web/initialData.sql
/usr/share/upsilon-web/*
/etc/httpd/conf.d/upsilon.conf

%changelog
* Thu Mar 05 2015 James Read <contact@jwread.com> 1.5.0-1
	Version 1.5.0
