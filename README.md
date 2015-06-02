# strategeo
stratego-like game

#
# Location to unpack
#
Unpack somewhere in your document root
ie /var/www/html/

#
# Create a config file
#
Create a config file outside of your document root
 ie /var/www/conf/db_conn

Inside that file write your login credentials
 ie localhost username password STRATEGEO

#
# Set up database
#

Create database in mysql called "STRATEGEO"

Then use command:
mysql -u username -p STRATEGEO < /var/www/html/strategeo/db/schema.sql

