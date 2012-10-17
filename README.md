git-hg-rp
=========

PHP script to automatically submit time spend working on a software project based on git|hg commits to Rechnung+.
ATM only Mercurial is supported. But I've only started the project today!

Problem
-------

I need to enter time spent working on a software project into a time-tracking system. 
I've chosen [Rechnung+](http://rechnung-plus.de) mainly because I've developed it ;-) and because it suit my needs.
I often forget when did I start working today and have to guess working time. Now I think that there's a way to 
detect this information from the earliest modification time of any file in a project since previous commit.
Having committed changes after working I know the ending time of the work. This gives us the duration of work.
I also want to submit it to Rechnung+ automatically. This PHP script is doing it using Rechnung+ API.
In the future it could be run as a post-commit hook.
I understand this is not very precise but it's better than guessing.

Similar solutions
-----------------

Before I describe the usage in details I want to share the information I've got when researching for similar projects. 
I've started by searching for "[git time-tracking](https://www.google.de/webhp?q=git%20time-tracking). 
When analyzing the result I found the following.

* http://andy.delcambre.com/2008/02/06/git-time-tracking.html
  Post-commit hook is saving current timestamp into a log file, 
  commit-msg hook calculates how long the feature took to implement and adds the time to the commit message.
* http://rcrowley.org/2011/01/13/gitpaid.html
  Running `gpbegin -b client-name` and `gpend -b client-name -m "Shaved the yak."` makes real commits to some extra
  repository. `gpinvoice -b client-name` calculates the total billded time.
* http://mir.aculo.us/2009/10/12/instant-time-tracking-from-git-commit-messages/
  