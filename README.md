PHPMongoTweet on OpenShift Express
==================================

This git repository helps you get up and running quickly w/ a PHP sample application
that uses MongoDB on OpenShift Express

Running on OpenShift
--------------------

Create an account at http://openshift.redhat.com/

Create a php-5.3 application

    rhc-create-app -a phpmongotweet -t php-5.3

Add MongoDB support to your application

    rhc-ctl-app -a phpmongotweet -e add-mongodb-2.0

Add this upstream phpmongotweet repo

    cd phpmongotweet
    git remote add upstream -m master git://github.com/openshift/phpmongotweet-example.git
    git pull -s recursive -X theirs upstream master


Then push the repo upstream

    git push

That's it, you can now checkout your application at:
    http://phpmongotweet-$yourlogin.rhcloud.com


Repo layout
-----------

php/ - Externally exposed php code goes here
libs/ - Additional libraries
misc/ - For not-externally exposed php code
../data - For persistent data
deplist.txt - list of pears to install
.openshift/action_hooks/* - Scripts that execute with every push

