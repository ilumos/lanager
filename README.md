LANager
=======

LANager is a web application designed to make [LAN Parties](https://en.wikipedia.org/wiki/Lan_party)
more enjoyable for attendees and organisers alike.

## Goals
*currently these appear to be the most valuable goals, but this list is likely to evolve*

### For Attendees
* Make it easy to find out what's going on and join in
	* [Games in progress](http://zeropingheroes.co.uk/wp-content/gallery/lanager/xbn2r.png) with direct join links
	* [Events](http://zeropingheroes.co.uk/wp-content/gallery/lanager/timetable.png) & tournaments
	* [Shouts](http://zeropingheroes.co.uk/wp-content/gallery/lanager/tixua.png)
	* [Files](http://zeropingheroes.co.uk/wp-content/gallery/lanager/files.png) e.g. game mods and patches
* Enhance the social aspect of LAN parties
	* [Attendee profiles](http://zeropingheroes.co.uk/wp-content/gallery/lanager/p5gat.png) with Steam add/message links
	* [List of attendees](http://zeropingheroes.co.uk/wp-content/gallery/lanager/iblhk.png) with people's seating location etc
* Let attendees find out important venue/event information
	* [Info pages](http://zeropingheroes.co.uk/wp-content/gallery/lanager/info.png) easily editable for displaying things like where to sleep etc
* Give attendees a voice during the event
	* [Request music & videos](http://zeropingheroes.co.uk/wp-content/gallery/lanager/playlist.png) for playout on a big screen
	* [Watch & listen](http://zeropingheroes.co.uk/wp-content/gallery/lanager/playlist_screen.png) to requested music & videos

### For Organisers
* Make it easy to get information out to attendees via [Info pages](http://zeropingheroes.co.uk/wp-content/gallery/lanager/info.png) and [Shouts](http://zeropingheroes.co.uk/wp-content/gallery/lanager/tixua.png)
* Encourage socialising and participation
* Get instant feedback from attendees


## Requirements
* Windows / Linux / OSX
* Web server (Apache, ngix and others)
* PHP 5.3.3+
* MySQL

*WAMP, LAMP and MAMP are a quick way to satisfy the above, just check that the version you download includes PHP 5.3* 

## Installation
*This section is a work in progress*

* Download and unzip
* [Configure your server for Laravel 3](http://three.laravel.com/docs/install)
	* [Make the "lanager/public/" directory your site's DocumentRoot](http://www.laravel.com/docs/install#server-configuration) - protecting other Laravel system and configuration files
	* Set the value of the key option in the config/application.php file to a random, 32 character string
	* Make the storage/ directory writable (chmod 777 -R lanager/storage/)
	* Enable mod_rewrite in Apache
	* Enable .htaccess files in the "lanager/public/" directory for pretty URLs
	* Enable php_openssl in PHP
* Set up a database and a user
	* Using "lanager" for both is recommended
* [Set up and run database migrations](http://www.laravel.com/docs/database/migrations)
	* Add the PHP binary to your system's PATH ([Windows](http://www.php.net/manual/en/faq.installation.php#faq.installation.addtopath), [Linux/Unix](http://unix.stackexchange.com/questions/26047/how-to-correctly-add-a-path-to-path))
	* Open a command shell and type "php" to verify that the above step was successful
	* Change directory (cd) to the lanager directory
	* Run "php artisan migrate:install" to [prepare the database](http://www.laravel.com/docs/database/migrations#prepping-your-database)
	* Run "php artisan migrate" to create the database structure
* [Get an API key from Steam](steamcommunity.com/dev/apikey)
	* Paste it into the "api_key" variable in "lanager/bundles/steamprofile/config/config.php"
* Browse to http://localhost/ and enjoy!


## Feedback & Contributions
* Found a bug? Got a great feature idea? Post it to [issue tracker](https://github.com/ilumos/lanager/issues)!
* Want to contribute?
	* [Fork the project](https://github.com/ilumos/lanager/fork) and add the features you want to see
	* Work on new features / bug fixes in the [issue tracker](https://github.com/ilumos/lanager/issues)
	* Or if you're really hardcore, request commit access 

### Quickstart using Vagrant

Vagrant is a VM configuration tool. You can use it to very quickly retrieve,
start, and automatically configure a VirtualBox VM for development or even
production. This has been tested on OSX, but should work fine on any platform
that Vagrant supports.

1. Install [Vagrant](http://downloads.vagrantup.com) and
   [VirtualBox](https://www.virtualbox.org/wiki/Downloads).
2. Within the `lanager` directory, in a terminal or command prompt, execute
   `vagrant up`. Vagrant will automatically download a VM image and configure
   it.
3. Follow the instructions that follow the configuration process to populate the
   database. This step is not performed automatically.
4. Ensure that you've added `127.0.0.1 lanager.dev` to your hosts file. This is
   `/etc/hosts` on Unix-y platforms and `C:\WINDOWS\system32\etc\drivers\hosts`
   on Windows.
5. Visit `http://lanager.dev:8080` in your browser and develop away!

You can SSH into the machine with `vagrant ssh`. The project files are in
`~/lanager`, which is symlinked to `/vagrant`. `/vagrantcache` is a convenient
place to store any large files you don't want to lose between vagrant images.
You can shutdown the VM with `vagrant halt`, or destroy it completely with
`vagrant destroy`. Note that destroying it will remove any files on it that are
not in the project directory or cache directory.
