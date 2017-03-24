fancyBox
===========

A Plugin for moziloCMS 2.0

The fancyBox plugin offers a nice and elegant way to add zooming functionality for images, inline, linked and html content.

## Installation
#### With moziloCMS installer
To add (or update) a plugin in moziloCMS, go to the backend tab *Plugins* and click the item *Manage Plugins*. Here you can choose the plugin archive file (note that it has to be a ZIP file with exactly the same name the plugin has) and click *Install*. Now the fancyBox plugin is listed below and can be activated.

#### Manually
Installing a plugin manually requires FTP Access.
- Upload unpacked plugin folder into moziloCMS plugin directory: ```/<moziloroot>/plugins/```
- Set default permissions (chmod 777 for folders and 666 for files)
- Go to the backend tab *Plugins* and activate the now listed new fancyBox plugin

## Syntax
    {fancyBox|image|<gallery>|<file>|<remote>}
Shows images or galleries in fancyBox.

1. Parameter ```<gallery>```: Name of an existing gallery
2. Parameter ```<file>```: Name of an existing file. If parameter ```<gallery>``` is set, the file has to exist in this gallery. Otherwise the given file will be taken from the moziloCMS filelist and has to be set with category, e.g. ```@=category:filename.jpg=@```
3. Parameter ```<remote>```: Optional. Text to display instead of thumbnail image.

    {fancyBox|inline|<text>|<content>|<title>}
Shows inline content like text, html or moziloCMS syntax in fancyBox.

1. Parameter ```<text>```: Text to link (moziloCMS syntax is possible).
2. Parameter ```<content>```: Content to display in fancyBox.
3. Parameter ```<title>```: Optional. Title for fancyBox.

    {fancyBox|link|<text>|<url>|<title>}
Shows linked content in fancyBox.

1. Parameter ```<text>```: Text to link (moziloCMS syntax is possible).
2. Parameter ```<url>```: Link url (with http://).
3. Parameter ```<title>```: Optional. Title for fancyBox.

## License
This Plugin is distributed under *Creative Commons Attribution-NonCommercial 3.0* (see LICENSE) and is free to use only for non-comercial purposes.

## Documentation
A detailed documentation and demo can be found here:
http://t.devmount.de/Develop/moziloCMS/Plugins/fancyBox.html
