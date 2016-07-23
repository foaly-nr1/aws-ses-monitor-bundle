[![Latest Stable Version](https://poser.pugx.org/serendipity_hq/aws-ses-monitor-bundle/v/stable.png)](https://packagist.org/packages/serendipity_hq/aws-ses-monitor-bundle)
Travis
[![Total Downloads](https://poser.pugx.org/serendipity_hq/aws-ses-monitor-bundle/downloads.svg)](https://packagist.org/packages/serendipity_hq/aws-ses-monitor-bundle)
[![License](https://poser.pugx.org/serendipity_hq/aws-ses-monitor-bundle/license.svg)](https://packagist.org/packages/serendipity_hq/aws-ses-monitor-bundle)
[![Code Climate](https://codeclimate.com/github/Aerendir/aws-ses-monitor-bundle/badges/gpa.svg)](https://codeclimate.com/github/Aerendir/aws-ses-monitor-bundle)
[![Test Coverage](https://codeclimate.com/github/Aerendir/aws-ses-monitor-bundle/badges/coverage.svg)](https://codeclimate.com/github/Aerendir/aws-ses-monitor-bundle)
[![Issue Count](https://codeclimate.com/github/Aerendir/aws-ses-monitor-bundle/badges/issue_count.svg)](https://codeclimate.com/github/Aerendir/aws-ses-monitor-bundle)
[![StyleCI](https://styleci.io/repos/63937012/shield)](https://styleci.io/repos/63937012)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/4c45c317-28c4-40ef-9a1b-01af44b77327/mini.png)](https://insight.sensiolabs.com/projects/4c45c317-28c4-40ef-9a1b-01af44b77327)
[![Dependency Status](https://www.versioneye.com/user/projects/579355b8ad9529003b1d4f7c/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/579355b8ad9529003b1d4f7c)

Bouncer bundle
==============

Forked from [BouncerBundle](https://github.com/shivas/bouncer-bundle).

Symfony2 bundle to automate AWS SES users using swiftmailer to filter out bouncing email recipients inside project.

AWS SES users know, if you get big amount of Bouncing emails, AWS will send you into probation period.
In some cases, there is no easy way to solve issue. This bundle solves problem transparently filtering recipients lists trough own database built by listening on AWS SNS Bounce topic that it creates and hooks to your identity.

Useful Links:
=============

- [How to handle Bounces and Complaints](http://sesblog.amazon.com/post/TxJE1JNZ6T9JXK/-Handling-span-class-matches-Bounces-span-and-Complaints.pdf)
- [Some sample code with PHP](https://forums.aws.amazon.com/message.jspa?messageID=202798#202798)

Requirements:
=============

1. You use AWS SES to send your emails
2. You have AWS API key
3. You have confirmed email identity (email or whole domain)

Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require shivas/bouncer-bundle "~0.1"
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding the following line in the `app/AppKernel.php`
file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

             new SerendipityHQ\Bundle\AwsSesMonitorBundle\ShivasBouncerBundle(),
        );

        // ...
    }

    // ...
}
```

Step 3: Add configuration
-------------------------

First, add the configuration parameters to `parameters.yml`:

```yaml
parameters:
    ...
    amazon.aws.key: 'your_key'
    amazon.aws.secret: 'your_secret'
    amazon.aws.eu_region: 'eu-west-1' # You can omit this. If omitted, the bundle sets this to us-east-1
    amazon.ses.version: '2010-12-01' # You can omit this. If omitted, the bundle sets this to 2010-12-01
    amazon.sns.version: '2010-03-31' # You can omit this. If omitted, the bundle sets this to 2010-03-31
```

NOTE: Do not forget to add those parameters also to your `parameters.dist.yml`!
 
Then create the `Credentials` service needed by the `AWS\Client`:
 
```yaml
 services:
     ...
     client.amazon.credentials:
         class: Aws\Credentials\Credentials
         arguments: ["%amazon.aws.key%", "%amazon.aws.secret%"]
```

Then, use those parameters

```yaml
# Default configuration for "ShivasBouncerBundle"
aws_ses_monitor:
    db_driver: orm # currently only ORM supported
    model_manager_name: null # if using custom ORM model manager, provide name, otherwise leave as null
    aws_config:
        # Here the NAME (not the service itself!) of the credentials service set in the previous step.
        # If you omit this, the bundle looks for client.aws.credentials service.
        credentials_service_name: 'client.amazon.credentials'
        region: "%amazon.aws.eu_region%" # You can omit this. If omitted, the bundle sets this to us-east-1
        ses_version: "%amazon.ses.version%" # You can omit this. If omitted, the bundle sets this to 2010-12-01
        sns_version: "%amazon.sns.version%" # You can omit this. If omitted, the bundle sets this to 2010-03-31
    bounces_endpoint:
        route_name:           _aws_monitor_bounces_endpoint
        protocol:             HTTP # HTTP or HTTPS
        host:                 localhost.local # hostname of your project when in production
    complaints_endpoint:
        route_name:           _aws_monitor_complaints_endpoint
        protocol:             HTTP # HTTP or HTTPS
        host:                 localhost.local # hostname of your project when in production
    filter:
        enabled:              true # if false, no filtering of bounced recipients will happen
        filter_not_blacklists: false # if false, all temporary bounces will not make that address to be filtered forever
        number_of_bounces_for_blacklist: 5 # The number of bounces required to permanently blacklist the address
        mailer_name:          # array of mailer names where to register filtering plugin
            - default
```

Add routing file for bounce endpoint (feel free to edit prefix)

```yaml
# app/config/routing.yml
bouncer:
    resource: '@ShivasBouncerBundle/Resources/config/routing.yml'
    prefix: /aws/endpoints
```



Step 4: Update your database schema
-----------------------------------

```
$ php app/console doctrine:schema:update --force
```

Step 5: Setup bounces and complaints handling
---------------------------------------------

Now it's time to create our topics for bounces and complaints. As told in the [post](http://sesblog.amazon.com/post/TxJE1JNZ6T9JXK/-Handling-span-class-matches-Bounces-span-and-Complaints.pdf)
Handling Bounces and Complaints on the [Amazon SES Blog](http://sesblog.amazon.com/), topics should follow the following nomenclature:

    ses-bounces-topic

Something like `ses-your_app-bounces-topic` may be better to avoid conflicts with other apps of yours.

So, run in console:
```
app/console awssesmonitor:sns:setup-bounces-topic ses-your_app-bounces-topic
```

and then

```
app/console awssesmonitor:sns:setup-complaints-topic ses-your_app-complaints-topic
```

This will use your AWS Credentials to fetch available identities and will provide you the option to choose what identities to subscribe to.

What will happen:

1. `ses-your_app-bounces-topic` topic will be created
2. All chosen identities will be configured to send Bounce notifications to that topic
3. Your project url will be provided as HTTP or HTTPS (configuration) endpoint for AWS
4. Automatic subscription confirmation will occur on AWS request to confirm (if your endpoint is reachable)

Contribute
----------

Contribute through issues or pull request.
