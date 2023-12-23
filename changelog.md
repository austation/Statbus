
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project loosely adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

### Upcoming/Future
Check out the [Statbus Design Document](https://hackmd.io/@nfreader/SJRqy-rU3)

---

#### [1.18.0] - 2023-12-20

##### Added
- The query for showing tickets by ckey has been updated to now include ANY ticket a given ckey was involved in, even if they didn't start it or finish it.
    - This mirrors the functionality available on mothbus (<3 mothbus)
- The list of ckeys involved in a given ticket is now displayed in the ticket sidebar
- Dynamic threat information now shows the advisory level that corresponds to the threat
- A duplicate book finder for the library

---

#### [1.17.6] - 2023-12-13

##### Added
- Player playtime on notes

---

#### [1.17.5] - 2023-09-26

#### Added 
- Timeline view to rounds! 
    - Shows major events sourced from the database and log files, in the order in which they occurred
    - Toggle event types on and off
- Message on logout
- Visiting a player URL that doesn't correspond to the `ckey` format will redirect you to the `ckey` formatted URL.
- Ability to hide certain features based on the date a change was made to the codebase.
    - For example, rounds before Jan 15th 2018 will no longer poll for round_end_data.json

#### Fixed 
- A major bug with bans by role on the homepage was causing frequent crashes. It has been fixed.
- Rounds without a station name won't display said missing name
- Errors fetching round end data are more graceful

---


#### [1.17.4] - 2023-09-11

#### Added

#### Fixed
- The determination for the latest admin has been improved
- Playtime graphs weren't showing on player pages
- Added a cache to the database 
- Numerous styling and legibility tweaks

---


#### [1.17.3] - 2023-09-07

#### Added
- Links to parsed and raw logs for round popover cards
- A fun new statistic about ahelps by server on the homepage
- Links to round listings for players

#### Fixed
- The link to the content warning wasn't correct
- If the game server database is down, detect that earlier and give a more informative error message

---

#### [1.17.2] - 2023-09-02

#### Added 
- New player listing to TGDB for admins

## Fixed 
- Admins with stacked ranks now have their permissions properly applied

---

#### [1.17.1] - 2023-08-30

#### Added 
- Library enhancements
- Round listing page

---

#### [1.17.0] - 2023-08-26

#### Added 
- A link to the current user's player page on the homepage
- Each page now has a tool to view the JSON version of itself
- Some fun, random piece of data on the homepage, refreshes every time the page loads. So far we have:
    - The newest admin
    - Top ten jobs by ban count
    - A randomly played internet sound
- Navigating to `<round>/logs` will automatically redirect you to the parsed-logs for that round
- The library!
    - Administrators can manage books (delete/undelete) on Statbus
    - They also get a way to quickly list all books by an author
- Test and production deployments
    - New versions of Statbus are now published to `test.atlantaned.space` before they reach `statbus.space`. 

#### Fixed
- Overhauled how `played_url` is parsed, and now it's much better!
- Poly is now asynchronous! Is this a good thing or a bad thing? You decide!

---

#### [1.16.0] - 2023-08-26

##### Added 
- Expiration information to bans
- Ditto for notes
- Global search bar
- TGDB Enhancements
    - Recent activity listing on homepage
    - Known alts for player pages
    - Live feed of tickets from the servers
    - List of active watchlist entries
- More round information
    - Deaths
    - Now shows player job at time of death
    - Round end result data (# of people escaped, station integrity, etc)
- Commendations now have a "save a screenshot" tool.

##### Fixed
- Visiting a link for a ticket that's not yours will prompt admins to view the ticket in TGDB
- Poly blocking the authentication menu


---

#### [1.15.0] - 2023-08-19

##### Added 
- View notes by author to TGDB!
- Admin memos on TGDB
- Global list of bans on TGDB
- Global list of notes & messages on TGDB

##### Fixed
- Numerous small design tweaks and improvements 

---

#### [1.14.0] - 2023-08-17

##### Added 
- An initial round listing on the home page
- Popovers for round badges with links to the round page etc
- Better error and exception handling

---

#### [1.13.0] - 2023-08-17

##### Added 
- Feedback messages for successful authentications

##### Fixed
- Redirecting after authentication. Now it will take you to an actual page, not a missing favicon or something
- Missing styles for alerts

##### Improved
- Formatting for notes and tickets.

---

#### [1.12.0] - 2023-08-16

##### Added
- Admin roster now links to the admin's individual player page!
- You can now view all stats from a given round

---

#### [1.11.0] - 2023-08-15

##### Added
- Download a certified copy of the results of your antagonist round! 

---

#### [1.10.0] - 2023-08-14

##### Added
- Basic round statistics (testmerged PRs and antagonists) to round information pages

---

#### [1.9.0] - 2023-08-11

##### Added
- Admin rank logs so you can see _exactly_ when someone got `+FUN`
- This information also shows up on public player information pages!

---

#### [1.8.0] - 2023-08-09

##### Added
- Players can now view their Notes & Messages! 
- Admins can see this in TGDB as well! 

#### Fixed
- Overhauled the queries used to list tickets
- Refactored a lot of the frontend templates

---

#### [1.7.0] - 2023-08-08

##### Added
- Admins can now set the link to their feedback threads!

---

#### [1.6.0] - 2023-08-07

##### Added
- TGDB player pages
- TGDB bans by ckey

---

#### [1.5.0] - 2023-08-07

##### Added
- Player pages at `statbus.space/player/<ckey>`
    - Also view their achievements!

---

#### [1.4.1] - 2023-08-07

##### Fixed
- Stacked admin ranks now render as the first rank specified, while still showing the full rank.
- Added missing ranks to the rendering list
- Urgent ahelp tickets are now indicated as such

---

#### [1.4.0] - 2023-08-05

##### Added
- A continuation of 1.3.0, now admins can see tickets, and tickets by round, from TGDB!

---

#### [1.3.0] - 2023-08-04

##### Added
- A ticket viewer, so you can see tickets you were involved in! 
    - And a way to embed them with BBcode!

---

#### [1.2.0] - 2023-08-04

##### Added
- Round information pages

---

#### [1.1.0] - 2023-08-03

##### Added
- Admin roster + activity information

---

#### [1.0.1] - 2023-08-02


##### Added
- This changelog!
- A privacy policy

---

#### [1.0.0] - 2023-08-02

##### Added
- Statbus
