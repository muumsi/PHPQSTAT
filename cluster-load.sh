#!/bin/bash
set -xv
# Exporting Environment Variables
#########################################
source /var/www/html/PHPQstat/phpqstat.conf
#########################################

function cpusused() { 
#    if [ "$BQS" = 'SGE' ]; then
#        cpusused=$(qstat -u *, -q $1 | gawk '{if ($5 !~ /qw/){sum=sum+$9}}END{print sum}')
#    fi
#    if [ "$BQS" = 'Slurm' ]; then
        cpusused=$(/usr/bin/ssh $USERLOGINHOSTNAME squeue -p $1 -t R -o "%C" | gawk '{if ($1 !~ /CPUS/){sum=sum+$1}}END{print sum}')
#    fi
}

function cpusqw() { 
#    if [ "$BQS" = 'SGE' ]; then
#        cpusqw=$(qstat -u *, | gawk '{if ($5 ~ /qw/){sum=sum+$NF}}END{if (sum >0){ print sum}else{print 0}}')
#    fi
#    if [ "$BQS" = 'Slurm' ]; then
        cpusqw=$(/usr/bin/ssh $USERLOGINHOSTNAME squeue -t PD -h -o "%C" | gawk '{sum=sum+$1}END{print sum}') 
#    fi
    #echo $cpusqw
}

if ! [ -d $RRD_ROOT ]; then mkdir -p $RRD_ROOT; fi
# DB Creation
#################
#echo $QUEUES
for q in $QUEUES; do
creabbdd=""
   if ! [ -f $RRD_ROOT/qacct_${q}.rrd ] ; then 
       creabbdd="${creabbdd}DS:${q}-used:GAUGE:1000000:0:999995000 "
       rrdtool create $RRD_ROOT/qacct_${q}.rrd -s 180 $creabbdd RRA:AVERAGE:0.5:1:175200
   fi
done
# Queue Waiting
creabbdd="DS:slots-qw:GAUGE:1000000:0:999995000 "
if ! [ -f $RRD_ROOT/qacct_qw.rrd ] ; then
       rrdtool create $RRD_ROOT/qacct_qw.rrd -s 180 $creabbdd RRA:AVERAGE:0.5:1:175200
fi

# Cores available
creabbdd="DS:CoresAvailable:GAUGE:1000000:0:999995000 "
if ! [ -f $RRD_ROOT/qacct_avail.rrd ] ; then
       rrdtool create $RRD_ROOT/qacct_avail.rrd -s 180 $creabbdd RRA:AVERAGE:0.5:1:175200
fi

# DB update
######################
i=0 
for q in $QUEUES; do
qname="${q}${QEXT}"
data="N"
    cpusused $qname
    cpuslimit=${CLIMIT[${i}]}
    if [ -z $cputime ] ; then cputime=0; fi
    if [ -z $cpusused ] ; then cpusused=0; fi
    data="$data:$cpusused"
    rrdupdate $RRD_ROOT/qacct_${q}.rrd $data
    #echo "rrdupdate $RRD_ROOT/qacct_${q}.rrd $data"
    i=$((i+1))
done

# Queue Waiting
data="N"
cpusqw
data="$data:$cpusqw"
rrdupdate $RRD_ROOT/qacct_qw.rrd $data
#echo "rrdupdate $RRD_ROOT/qacct_qw.rrd $data"

# Cores Available
data="N"

#cpusavail
data="$data:$TCORES"
rrdupdate $RRD_ROOT/qacct_avail.rrd $data
#echo "rrdupdate $RRD_ROOT/qacct_avail.rrd $data"

# Print chart
######################
DATE=$(date '+%a %b %-d %H\:%M\:%S %Z %Y')

unset datagrups
unset plus
unset qs
i=0 
for q in $QUEUES; do
 pp="${q}-used"
 datagrups="$datagrups DEF:${q}-used=$RRD_ROOT/qacct_${q}.rrd:${q}-used:AVERAGE "
 datagrups="$datagrups CDEF:${q}=${pp} "
 datagrups="$datagrups AREA:${q}#${COLOR[${i}]}:${q} "
 if (($i>0)); then 
   plus="${plus}+,"
   qs="$qs${q}-used,"
   pp="${qs}${plus}+"
 fi
 i=$((i+1))
done

# Queue Waiting
#datagrups="$datagrups DEF:slots-qw=$RRD_ROOT/qacct_qw.rrd:slots-qw:AVERAGE LINE1:slots-qw#${COLOR[${i}]}:slots-qw"

# Cores Available
#datagrups="$datagrups DEF:CoresAvailable=$RRD_ROOT/qacct_avail.rrd:CoresAvailable:AVERAGE LINE1:CoresAvailable#000000:CoresAvailable"

rrdtool graph $WEB_ROOT/img/hour.png -a PNG -s -1hour -t "HPC Usage (hourly)" -h 200 -w 600 -v "Load" COMMENT:" \\l" $datagrups COMMENT:" Last update\: $DATE" > /dev/null

rrdtool graph $WEB_ROOT/img/day.png -a PNG -s -1day -t "HPC Usage (daily)" -h 200 -w 600 -v "Load" COMMENT:" \\l" $datagrups COMMENT:" Last update\: $DATE" > /dev/null

rrdtool graph $WEB_ROOT/img/week.png -a PNG -s -1week -t "HCP Accounting (Weekly)" -h 200 -w 600 -v "Load" COMMENT:" \\l" $datagrups COMMENT:" Last update\: $DATE" > /dev/null

rrdtool graph $WEB_ROOT/img/month.png -a PNG --start end-4w --end 00:00 -t "HPC Usage (Monthly)" -h 200 -w 600 -v "Load" COMMENT:" \\l" $datagrups COMMENT:" Last update\: $DATE" > /dev/null

rrdtool graph $WEB_ROOT/img/year.png -a PNG --start end-52w --end 00:00 -t "HPC Usage (Yearly)" -h 200 -w 600 -v "Load" COMMENT:" \\l" $datagrups  COMMENT:" Last update\: $DATE" > /dev/null

