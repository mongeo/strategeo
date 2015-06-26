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
###need to update schema file###
Create database in mysql called "STRATEGEO"

Then use command:
mysql -u username -p STRATEGEO < /var/www/html/strategeo/db/schema.sql

#
# Game states
#

0: Not initialized
1: Initialized
2: Joined (Blue's selection)
3: Joined / Blue finished (Red's turn)
4: In progress
5: Complete