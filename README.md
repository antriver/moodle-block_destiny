# Moodle Destiny Library Integration
Show checked out books in Folett Software's Destiny library system on Moodle.

## Screenshots
![Screenshot of checked out items](https://www.classroomtechtools.com/assets/img/moodle-plugin-screenshots/block_destiny/1.png)

## Setup

### Ubuntu 14.04
On Ubuntu 14.04 it should work out of the box.

### Ubuntu 12.10
Install the packages needed to connect to MSSQL from PHP:
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


## To Do
* Ability to add multiple servers and select which one is used (testing server and live server)
