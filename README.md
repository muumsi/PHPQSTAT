# PHPQSTAT
1. Requrements:
 - HTTP server with PHP support (Apache, NGINX, …)
 - PHP -  https://www.php.net/ 
 - PEAR, the PHP Ext. and Appl. Repo. - http://pear.php.net/package/PEAR (SSH1, SSH2, SFTP, SCP)
   (folders CRYPT, FILE, MATH and NET should be copied in PHPQstat folder)
 - RRDTOOL - https://oss.oetiker.ch/rrdtool/  
2. File phpqstat_inc.php should be edited and proper values to variables should be set (hostname, title and filesystem).
3. File phpqstat.conf should be edited to setup proper names for Slurm rrd acounting.
4. File diskusage.sh and cluster-load.sh should be edited and tested before scheduled with crontab for periodical checks.
