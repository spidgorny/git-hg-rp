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

Before I describe the usage in details I want to share the information I've got when researching for similar projects.