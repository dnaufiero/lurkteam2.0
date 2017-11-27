# lurkteam2.0
Website that allows you to lurk in streams, only displaying the streams that are currently live.
 

Todo:

-Separate out JS into its own file, to avoid having it in the main page
-Add bootstrap template
-Add side slide menu with settings
-Convert textarea to a popout. Single text box with a toggle box to switch to bulk entry (text area)
-Display streams as individual boxes (rounded bootstrap edges) with the profile image/offline image (pull from API?) in
  a small grid below the main grid of live streams (reponsive). Offline stream boxes will be 1/2 to 1/4 size of currently
  playing stream boxes
-Make a logo, put up top
-Copyright information on the bottom
-FAQ page
-first time page is loaded, display a short FAQ in the center of the page
-add offline "jokes" to be displayed when no streams are currently playing
-when a stream goes on cooldown, dont destroy the div, replace the contents with a the stream name, and indicate that it
  is on cooldown, not offline
-*MAYBE*: allow the person to back up their settings to a site database with a username that they enter (don't require login)
  and allow them to enter that username upon loading the site on a new computer, etc, with the notion that anyone could use their
  settings if they typed their username (maybe automatically backup settings and link to twitch account?)
-*MAYBE*: allow the user to log into Twitch from the site, and display that the user is logged in
