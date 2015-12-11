# php_updater

This tool help you to update your PHP installation for Windows.

Windows only ?
--------------

Yes, for Mac Os X You can use [Homebrew](http://brew.sh) or other distribution. For Linux, many distribution include PHP. Especially for Debian, the [DotDeb](https://www.dotdeb.org) project provide newer version.

Chocolatey provide PHP
----------------------

Chocolatey is another way for install PHP on your Windows server. But the location is not customizable.


## Base tree folder for Windows Server

This tree is not mandatory. All configuration is based on this tree.

```
drive (d:)
└── sites
    ├── log
    │   ├── php56
    │   └── php70
    ├── tmp
    ├── tmp_soap
    ├── tools
    │   ├── ansicon
    │   ├── apache24
    │   ├── git19
    │   ├── php56
    │   ├── php56_backup
    │   ├── php70
    │   ├── php_tool
    │   └── svn19
    └── www_root
```

## Requirements

Git 1.9 or newer installed and available without full qualified path.
The prerequire for PHP installed. By example [VC11](http://www.microsoft.com/en-us/download/details.aspx?id=30679) for PHP 5.6 or [VC14](http://www.microsoft.com/en-us/download/details.aspx?id=48145) for PHP 7.


## Install the Tool

* Clone this repos into `sites/tools/php_updater`
* Open [Ansicon](http://adoxa.altervista.org/ansicon/) and go to install folder.
* Execute `make_tree.bat`
* [Download PHP](http://windows.php.net/download/) 5.6.13 for Windows or newer and unzip into `sites/tools/php_updater/php` folder.
* Configure this `sites/tools/php_updater/php/php.ini` the TimeZone

```
[Date]
; Defines the default timezone used by the date functions
; http://php.net/date.timezone
date.timezone = Europe/Paris
```

* Set the extension directory
```
; On windows:
extension_dir = "ext"
```

* Enable this extensions :
	* php_bz2
	* php_curl
	* php_fileinfo
	* php_gmp
	* php_intl
	* php_mbstring
	* php_openssl

```
[...]
; Windows Extensions
; Note that ODBC support is built in, so no dll is needed for it.
; Note that many DLL files are located in the extensions/ (PHP 4) ext/ (PHP 5)
; extension folders as well as the separate PECL DLL download (PHP 5).
; Be sure to appropriately set the extension_dir directive.
;
extension=php_bz2.dll
extension=php_curl.dll
extension=php_fileinfo.dll
;extension=php_gd2.dll
;extension=php_gettext.dll
extension=php_gmp.dll
extension=php_intl.dll
;extension=php_imap.dll
;extension=php_interbase.dll
;extension=php_ldap.dll
extension=php_mbstring.dll
;extension=php_exif.dll      ; Must be after mbstring as it depends on it
;extension=php_mysql.dll
;extension=php_mysqli.dll
;extension=php_oci8_12c.dll  ; Use with Oracle Database 12c Instant Client
extension=php_openssl.dll
[...]
```

## Configure

Open *ansicon* and go to `sites/tools/php_updater`. Launch `self_install.bat`

This script download the `composer.phar`, install the vendor dependencies and run the configuration command.

```
Please enter the target folder : d:\sites\tools
Please enter the temporary folder : d:\sites\tmp
Please enter the PHP folder : d:\sites\tools\php56
Please enter the backup folder : d:\sites\tools\php56_backup
PHP branch : php56-nts
Configuration  end. Use 'config:show' command for check configuration.
```

For the php branch, read the file `config/sources.yml`

| PHP version | NTS or TS | Arch | Branch        |
| ----------- | --------- | ---- | ------------- |
| 5.6         | TS        | x86  | php56         |
| 5.6         | NTS       | x86  | php56-nts     |
| 5.6         | TS        | x64  | php56-x64     |
| 5.6         | NTS       | x64  | php56-x64-nts |


| PHP version | NTS or TS | Arch | Branch        |
| ----------- | --------- | ---- | ------------- |
| 7.0         | TS        | x86  | php70         |
| 7.0         | NTS       | x86  | php70-nts     |
| 7.0         | TS        | x64  | php70-x64     |
| 7.0         | NTS       | x64  | php70-x64-nts |

## Use

### Basic use

Open *ansicon* and go to `sites/tools/php_updater`. Launch `update.bat`

This script do :

* Update php_updater from GitHub.
* Update Composer
* Install the eventualy vendor update
* Install the lastest version of PHP branch set

### Advansed use

Open *ansicon* and go to `sites/tools/php_updater`. Launch `PhpUpdate.bat` hor display help and all available command.

This version include :

* Display config
* Init config if not exists
* Update PHP version
* Display Current installed PHP version.

## Q/A

*You want clean the configuration ?* Remove the `config/config.yml` file.

*Why install PHP in this tool ?* A PHP script cannot access in write mode in `php.exe` if in running.

*Who to maintain the integrated PHP version ?* It is not necessary to maintain the integrated PHP up to date because it is not used in production by the server. Only the update tools used.

## Contribute

Your contribution can help the community. Fork this repository, make change, and create a new pull request.

You ar not developer and you want help. Make a new issue.

Thank in advance for your help.
