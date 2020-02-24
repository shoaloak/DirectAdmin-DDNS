# Dynamic DNS for Directadmin
Want to attach your home server to a subdomain but don't know how?
Use this simple PHP script with a cronjob to dynamically update your DNS records!

## Setup
Make sure deps are installed such as `php php-curl`.

```
# git clone https://github.com/shoaloak/DirectAdmin-DDNS /opt/dyndns
# whereis php # <php location>, usually /usr/bin/php
# crontab -e
* * * * * <php location> /opt/dyndns/dyndns.php
```

## Sources
* Based on [this forum post](https://www.vimexx.nl/forum/14-tutorials/588-dyndns-mogelijk-via-directadmin-api-bij-vimexx?page=1#post-2323).
* Shamelessly mirroring [httpsocket.php](https://files.directadmin.com/services/all/httpsocket/)
