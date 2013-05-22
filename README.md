gw2api
======

This is a very basic and simple PHP client implementation for the [official Guild Wars 2 API](https://forum-en.guildwars2.com/forum/community/api/API-Documentation).
Somewhen around the 15th of May, 2013, ArenaNet announced the [official Guild Wars 2 API](https://forum-en.guildwars2.com/forum/community/api/API-Documentation),
offering developers to create services based on the official RESTful interface to the Guild Wars 2 world.

Getting started
---------------
To run the API on your local development on http://localhost/ just follow these
4 simple steps:

1. Copy or extract the API client to your webserver directory.
2. Copy ./config-dist.php to ./config.php and fill in the values (Just 2 values: API endpoint and version)
3. Create a ./cache directory and chmod it to 777
4. Open http://localhost/test.php in your browser

This should result in the following:

1. 4 cache files (*.json) should reside in your cache folder
2. The website in the browser should display all events in my homeworld (Riverside [DE]), their regional occurrence and their status

Now, go and develop some more, **have fun!**

Troubleshooting
---------------
There's not much troubleshooting at the moment. Your PHP installation will require
*allow_url_fopen* set to *On* and your *localhost* should be able to request resources
from the internet. If you get cache write errors, you should check whether the
read/write settings for the ./cache directory are correctly set to 777.

Important note
--------------
Though not required to run the API client, I have included a simple but solid
filesystem caching mechanism, writing the JSON response to local files, to save
bandwidth and both, yours as well ArenaNet's resources. Please use caching
(especially while developing). Read the phpdoc comments to learn about the
cache's requirements

Disclaimer
----------
I am a former Guild Wars 2 player, playing very rarely now. But I love the open
web and especially open APIs. I just put together this simple libraries to help
some other developers get into more serious API development. I am not sure
whether I find the time to take this client to next levels, but I will keep you
informed in this github project. Feel free to fork this library and make it
your own. In fact, it's all ArenaNet's work and we can have fun thanks to them!

**However, here's the official API terms of use:**
These API's are wholly owned by ArenaNet, LLC ("ArenaNet"). Any use of the API's
must comply with the [Website Terms of Use](https://www.guildwars2.com/en/legal/website-terms-of-use/)
and [Content Terms of Use](https://www.guildwars2.com/en/legal/guild-wars-2-content-terms-of-use/), however you
may use the API's to make commercial products so long as they are otherwise
compliant and do not compete with ArenaNet. ArenaNet may revoke your right to
use the API's at any time. In addition, ArenaNet may create and/or amend any
terms or conditions applicable to the API's or their use at any time and from
time to time. You understand and agree that ArenaNet is in the process of
developing a full license agreement for these API's and ArenaNet will publish
it when it is complete. Your continued use of the API's constitutes acceptance
of the full license agreement and any related terms or conditions when they are
posted.
