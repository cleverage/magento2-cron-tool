Magento2 - Cron tools
=====================

This module is mainly for debugging purpose, that's why it's recommended to install in only in "dev". It should work 
with any version of Magento2.

Installation
------------

```bash
# Setup composer dependencies
composer config repositories.cleverage-magento2-cron-tool vcs https://github.com/cleverage/magento2-cron-tool
composer require --dev cleverage/magento2-cron-tool:dev-master

# Register the module
./bin/magento setup:upgrade
```

Usage
-----

```bash
./bin/magento cleverage:cron:list
```

Lists the registered cron jobs and display their schedule

```bash
./bin/magento cleverage:cron:execute [job_code]
```

Execute directly a registered cron job, without waiting for its schedule
