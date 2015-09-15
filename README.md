# Statuschecker
Website Template for easy to use Statuschecker

Cron-Job Command: /usr/bin/wget -q -O temp.txt http://your-domain.tld/path/performcheck.php?key=insertyourkeyhere


How does all this work?
- A Cron-Job runs performcheck.php(with your personal key as GET argument) at least every minute(lowest possible period in Cron)
- The performcheck.php stores some infos in cache files
- The index.php just access the cache files and wont send out more tcp traffic(no additional pings)


Live Demo Website(may have more or less features): http://project-sato.net/statuschecker/
