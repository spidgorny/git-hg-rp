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
For accurate results I have to edit any file in the beginning of the work and not touch it until I commit.
I also want to submit it to Rechnung+ automatically. This PHP script is doing it using Rechnung+ API.
In the future it could be run as a post-commit hook.
I understand this is not very precise but it's better than guessing.

## Initial setup (done once)

1. Download `git-hg-rp` files from github in ZIP or by `git clone` command. Extract ZIP.
2. Create an account at http://rechnung-plus.de/
2. Edit `.hg/hgrc` file in your(!) project and enter your Rechnung+ credentials like this:

    [git-hg-rp]
    rp-login = my@email.com
    rp-pw = myPassword2012

## Usage (do everytime after commit)

    > php hg-git-rp.php
    
This will do everything described above including submitting the time to the project in Rechnung+.

## Example output

This is what I get when I execute the script:

$ php index.php
Array
(
    [recordId] => 9:fb89284da8c9
    [startDate] => 20121020T040804+0200
    [endDate] => 20121020T040940+0200
    [note] => Final
multi
line
upload
    [tags] => Array
        (
            [0] => git-hg-rp
        )

)

Array
(
    [Inserted] =>
    [Updated] => 1
    [Skipped] =>
    [MissingTag] =>
)

POST time: 0.406

## Setting-up Rechnung+ project

Register and login into Rechnung+, create a client and a project. *Make sure you enter your git|hg project name
(folder name) into the "Tag" field in a project you create in Rechnung+ in square brackets*. This way it will put the time into a
correct project.

## Testing

Edit some files. Make sure you touch one file in the beginning of your work and don't change it until you commit.
This will indicate the starting time.

Commit and run `php git-hg-rp.php`. Go back to Rechnung+ and see if you get a new record with the time you spent
working listed. Make sure to explore other features of Rechnung+ like automatic invoicing and statistics.

Similar solutions
-----------------

I want to share the information I've got when researching for similar projects. 
I've started by searching for "[git time-tracking](https://www.google.de/webhp?q=git%20time-tracking)". 
When analyzing the result I found the following.

* http://andy.delcambre.com/2008/02/06/git-time-tracking.html
  Post-commit hook is saving current timestamp into a log file, 
  commit-msg hook calculates how long the feature took to implement and adds the time to the commit message.
* http://rcrowley.org/2011/01/13/gitpaid.html
  Running `gpbegin -b client-name` and `gpend -b client-name -m "Shaved the yak."` makes real commits to some extra
  repository. `gpinvoice -b client-name` calculates the total billded time.
* http://mir.aculo.us/2009/10/12/instant-time-tracking-from-git-commit-messages/
  You have to enter how much time you worked manually into a commit message like this 
  `git commit -m "Remove some extra whitespace f:15". Then it submits data to some other time-tracking server
  automatically. Not detecting time in any way. No go.
* http://stackoverflow.com/questions/11497322/time-tracking-automatically-from-git-logs-ticgit-tickets-and-dated-version-nu
  This explains that it's wrong to count the time between the commits as there could be a coffee-break or
  a night sleep between two commits. That's why I take the earlies modification time of any file since last commit.
* https://github.com/TheHippo/git-timetracking
  It's a coffeescript which analyzed git log file. No more information was given.
* https://github.com/Fandekasp/Trello-Git
  Goal: get your Trello project updated directly from the git repository via tags. Only README file is available.
* http://edulix.wordpress.com/2010/12/05/presenting-git-timetracker/
  It’s quite simple to use: you do a `git timetrack –start`, the clock starts counting. 
  Then you go for a coffee, you use `git timetrack –stop` for that, and then when you come back, 
  you can continue counting the time executing `git timetrack –start`. 
  Then you do a commit, and it gets automatically annotated with the time spent, and the clock stops counting.
* http://coderwall.com/p/iuye0g
  Add tags to your commit messages with the amount of hours you've spent, eg. "Implemented foobar feature t[1.5]."
  Ruby script shows total time spent on a project by analyzing git log. Total time for the whole project - not what I need.
* http://productblogarchive.37signals.com/products/2011/03/time-tracking-via-git-and-basecamp.html
  They also enter time into the commit message manually which then gets imported into Basecamp.

So there's no other script which does what I want. Poke me if you like it as it gives me more motivation to continue
working on it.

Feedback is welcome. Enjoy.