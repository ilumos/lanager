LANager
=======

LANager is a web application designed to make [LAN Parties](https://en.wikipedia.org/wiki/Lan_party)
more enjoyable for attendees and organisers alike.

## Goals
*currently these appear to be the most valuable goals, but this list is likely to evolve*

### For Attendees
* Make it easy to find out what's going on and join in
	* [Games in progress](http://i.imgur.com/xbN2R.png) with direct join links
	* Tournaments & events
	* [Shouts](http://i.imgur.com/tixUA.png)
	* Files e.g. game mods and patches
* Enhance the social aspect of LAN parties
	* [Attendee profiles](http://i.imgur.com/P5gaT.png) with Steam add/message links
	* [List of attendees](http://i.imgur.com/IBlHK.png) with people's seating location etc
* Find out important venue/event information
	* Eating & sleeping
	* Event rules
* Give attendees a voice during the event
	* Request music
	* Suggest games & tournaments

### For Organisers
* Make it easy to get information out to attendees
* Encourage socialising and participation
* Get instant feedback from attendees


## Requirements
* Windows / Linux / OSX
* Web server (Apache, ngix and others)
* PHP 5.3
* MySQL

*WAMP, LAMP and MAMP are a quick way to satisfy the above, just check that the version you download includes PHP 5.3* 

## Installation
*This section is a work in progress*

* Download and unzip
* [Configure your server for Laravel](http://www.laravel.com/docs/install)
* Set up a database and a user
* [Set up and run database migrations](http://www.laravel.com/docs/database/migrations)
* [Get an API key from Steam](steamcommunity.com/dev/apikey)


## Feedback & Contributions
* Found a bug? Got a great feature idea? Post it to [issue tracker](https://github.com/ilumos/lanager/issues)!
* Want to contribute?
	* [Fork the project](https://github.com/ilumos/lanager/fork) and add the features you want to see
	* Work on new features / bug fixes in the [issue tracker](https://github.com/ilumos/lanager/issues)
	* Or if you're really hardcore, request commit access 
