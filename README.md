#Mac Controller
This is a simple webapp for iOS that let's you control your Mac via AppleScript. Based on Brett Terpstra original [Home Control](http://brettterpstra.com/homecontrol-local-mac-control-for-iphone/) idea but stripped down, some bug fixes and updated for 2012. It was designed to control multiple Macs on a network where the Macs were out of user reach, to preform simple tasks like volume control, restarting the mac, opening and closing applications.

##Setting up the web server

To use this, you will need to run a web server on your Mac and run it as your own user. Not the most secure but the Macs i'm running this on, are on a private network so it is not a huge concern for me.

Mac OS X comes with a built in apache web server making the installation fairly easy. 

###Running the web server as a different user account

In order for the AppleScript commands to execute, you need to change which user account is running the web server. To do this you need to edit `/etc/apache2/httpd.conf` as root, in what ever text editor you like. I'm just using nano here as it's preinstalled on OS X.

	sudo nano /etc/apache2/httpd.conf 
	
Find the following lines
	
	User _www
	Group _www
		
And change them to:

	User yourusername
	Group staff

So for example, mine looks like this:
	
	User Ben
	Group staff
	
If you haven't already, you need to enable PHP. Find the following line:

	#LoadModule php5_module libexec/apache2/libphp5.so

And remove the # at the beginning.

Now, on the command line, type `sudo apachectl graceful` to restart the server under the new user. For more information on setting up Apache on OS X 10.8, [check out this guide](http://coolestguyplanettech.com/downtown/install-and-configure-apache-mysql-php-and-phpmyadmin-osx-108-mountain-lion).

##Installing Mac Controller

Download the files and place them in a folder called `maccontroller` in `~/Sites`. By placing them in there, you can reach the app by going to `http://computername.local/~username/maccontroller`. For example, I can reach it from `http://macbookpro.local/~Ben/maccontroller/`. To change your computer name, go to `System Preferences > Sharing` and edit the text box called "Computer Name:". 

Once you are happy that everything is working, hit the share button on the bottom bar of Mobile Safari to add the app to your home screen. This will run a full-screen version directly from your home screen.

##Customising

If you wish to customise it in anyway, go and check out [Brett's original post on Home Control](http://brettterpstra.com/homecontrol-local-mac-control-for-iphone/) for more details.

