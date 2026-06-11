```
==================_=_====
 __ __ ___ __  __| | |__
 \ V  V / '_ \/ _` | / /
  \_/\_/| .__/\__,_|_\_\
========|_|==============
    Wordpress On Disk
=========================


========|_|==============
   Progressing Status
=========================

[ March 29, 2026 ]
+ A simple theme with simple UI which can be runned with 5,000,000 normal posts. It is
tested in Bioogr Linux (a simple and small Linux distro which are developed from Ubuntu
and is run offline from 64GB USB stick on 4GB RAM laptop). It is able to run with more
posts but I have not enough disk space to do that. This version is called version
0.1.1 . In this version, I code based on improvisation, so the codebase does not follow
Wordpress Coding Standards, this will be fixed in next version.

[ April 3, 2026 ]
+ This version is called version 0.1.2. In this version, version 0.1.1 is modified to
apply Wordpress Coding Standards.

[ April 6, 2026 ]
+ This version is called version 0.1.3. In this version, unit test tool is replaced by
Testor ( https://github.com/pgk-zp/testor ); search feature is added to help user
searching & browsing random posts; 50,000,000 posts are generated; 1,000 post indexes
are generated (will be continued to generate in next days until hard disk (1TB) is full).
  o Plan on next days:
    __ Add token protected for tools of creating posts, indexing posts, unit testing
    __ Add custom post type in order to allow Wordpress creating & modifying posts via
       Admin interface.
    __ Upload to host and generate 1 billion posts. If time remains, 1 billion posts will
       be indexed and available to search.

[ April 7, 2026 ]
+ This version is called version 0.1.4. In this version, token protected for tools of creating
posts, indexing posts, unit testing is added. Custom post type ('postisk') is added in order to
allow Wordpress creating & modifying posts via Admin interface.
  o Plan on next days:
    __ Continue to generate indexes until hard disk (1TB) is full.
    __ Upload to host and generate 1 billion posts. If time remains, 1 billion posts will
       be indexed and available to search.

[ April 7, 2026 ]
+ This version is called version 0.1.5. In this version, minor bugs in tools of creating posts,
indexing posts which is related to protected token are fixed. Screenshots are also added.
  o Plan on next days:
    __ Continue to generate indexes until hard disk (1TB) is full.
    __ Upload to host and generate 1 billion posts. If time remains, 1 billion posts will
       be indexed and available to search.

[ April 9, 2026 ]
+ This version is called version 0.1.6. In this version, highlighting feature is added to search
in both admin and public. Listing all posts when query is empty is also added. Some minor bugs in
tools of creating posts, indexing posts is fixed. Indexing feature is refactored to improve speed.
  o Plan on next days:
    __ Continue to generate indexes until hard disk (1TB) is full.
      oo Indexing is still too slow (5,000 posts per 10 hours)
    __ Upload to host and generate 1 billion posts. If time remains, 1 billion posts will
       be indexed and available to search.
      oo This task is cancelled because I can not gain Debit card to buy hosting (Information of debit
      card is only available by using Mobile app, I don't want to use app (just because I don't want
      to do some stupid actions as followed instructions when using app to verify actions (face recognition)
      which is similar VNeIDs app, so I cancel new debit card) ).
    __ Benchmark WPDK on 1TB hard disk, optimize WPDK to gain maximum rate of storing & indexes (smallest as
      possible), add virtual file system feature to manage multiple disk for contributing to data folder contents.
    __ Steps by steps purchasing larger USB sticks, generate and index 1 billion posts with multiple USB sticks on
    laptop.

[ April 9, 2026 ]
+ "Wordpress On Disk" project will be done by closed source. "Wordpress On Disk" will be renamed to "Postisk" which is:
  o A CMS written in Hack language & run on HHVM.
  o Provide cloud service for CMS needs.
  o Provide post managing with dynamic properties (something like Salesforce)
  o Provide API access for developers to build web app based on "Postisk".
  o Source code of "Postisk" will be stored offline.

```
