#!/bin/bash
#set -xv
# Exporting Environment Variables
#########################################
du -m /ceph/grid/home/ --max-depth=1 > /var/www/html/PHPQstat/diskusage.txt 
#rm -f /var/www/html/PHPQstat/diskusage.txt &&
#mv /var/www/html/PHPQstat/diskusage1.txt /var/www/html/PHPQstat/diskusage.txt
#########################################
