Asterisk Queue Status
=====================


Original version here:

https://sysadminman.net/blog/2012/asterisk-elastix-queue-and-agent-wallboard-3604

and

https://sysadminman.net/blog/2013/asterisk-freepbx-queue-and-agent-wallboard-4933

Translated to russian and adapted for Asterisk 11.25.0

## Prereqruisites

* Uses PHPAGI to query Asterisk, so this needs installing first – http://phpagi.sourceforge.net/
* Written against the output of Asterisk 11.25.0 and tested with it
* Needs a crontab entry to reset the queue statistics at midnight every night.

Cron example:

    /usr/sbin/asterisk -rx 'queue reset stats'


![](https://github.com/aleks-o/asterisk_queue_status/raw/master/img/ast1.png)

