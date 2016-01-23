# Introduction #

These are the basic instructions needed to get nvdCahce up and working.

# Requirements #

1) Access to a MySQL database server
2) An Apache server with PHP (only tested on version 5.2.3)


# Initial Installation #

1) Download the release package found here --> (link here)

2) Move the nvdCache.tar.gz file to the root of your web server 'mv nvdCache.tar.gz /var/www/.' <-- (that works for some versions of linux) Your web server root may be different than what is listed.  Please work with your administrator to properly determine this.

3) un-gzip the file 'gzip -d nvdCache.tar.gz'

4) un-tar the file 'tar -xvf nvdCache.tar'

5) Remove the old tar file 'rm nvdCache.tar'


# Database readiness #

1) How ever you do it, ie. command line or phpMyAdmin create a data base with the name **nvdCache**

2) Execute against the database the contents of the nvdCache\_db.sql file against your newly created database.  From the command line you could do this 'mysql -u'db user name here' -p < nvdCache\_db.sql'

3) Modify the contents of the config.ini file to contain the proper information for connection to the database.

# Testing database #

1) Point your web browser to http://<url goes here/nvdCache/  If everything has been done correctly the returned http content should say something like, "200Everything looks honky dory!<number here>"

# First update #

1) The first update is going to pull in all of the nvdData and load it into the database.  This will take some time so go and get a cup of coffee after issuing the following command from the nvdCache directory. 'php updateDataSource.php'

# Putting updates into cron #