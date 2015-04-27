moodle-block_destiny
=============

Displays checked out books in Folett Software's Destiny library system on Moodle.
Tested with Moodle 2.8.3

Server Setup
-------------

## Ubuntu 14.04
On Ubuntu 14.04 it seemed to work straight out of the box.


## Ubuntu 12.10
To install stuff needed to connect to MSSQL from PHP:
```
sudo apt-get install php5-odbc php5-sybase tdsodbc
```

Then:
```
vi /etc/freetds/freetds.conf
```

And add:

```
[global]
tds version = 8.0
client charset = UTF-8
```

(Source: http://www.robertprice.co.uk/robblog/2013/01/using-sql-server-ntext-columns-in-php/)


TODO
-------------
* Ability to add multiple servers and select which one is used (testing server and live server)
