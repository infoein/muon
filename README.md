# Muon file manager (version 3.4)

Muon is a powerful and lightweight open source PHP/JavaScript-based file manager for your web space released under the MIT license. 

Home page and demo: [Muon on GitHub Pages](https://infoein.github.io/muon/)


## Features
- *You don't need to install any boring app to manage your web space*: Muon works as a web-app and its simple, fast loading and responsive interface is optimized for modern browsers for desktops, tablets and smartphones;
- *A stand-alone 100kB file*: Muon's code is compressed in a single <100kB page and it's ready to work out-of-the-box. No external resources are used (not even fonts or pictures);
- *No database required*: Muon allows a single user access, so a database would be a useless waste of resources. All settings are stored at the beginning of the file, and (of course) hidden to visitors;
- Fully open source - released under the MIT License.



## Functionalities
- Browse folders, display sizes, permission and tree structure;
- Move, copy, delete, rename your files and create new ones;
- Zip/unzip archives (requires PHP-Zip extension on your server);
- Upload multiple files, according to server's limitations;
- *Built-in text editor* - This simple tool is one of the most helpful to manage your site's pages (HTML, PHP), scripts (JS) and stylesheets (CSS).



## Installation notes
The only file required to use Muon is ***muon.php***. You need first to edit its settings as explained below, then upload it to your web space; it will start working immediately asking you to log-in. You can place it anywhere and eventually rename it, but you will be able to manage only that folder and its subdirectories, unless you specify a different root path (check *mu_root_dir* option).

To change settings, define PHP constants at the beginning of the file this way:
``define( "parameter", "value" );``

### Login settings
- ``mu_username`` and ``mu_password`` - These two parameters are **required**. As suggested by the names, they represent the credentials to run Muon file manager. Password must be md5-encrypted (there are several tools to do it online), so, let’s take an example, you will type *34819d7beeabb9260a5c854bc85b3e44* instead of *mypassword*;
- ``mu_cookie`` - To handle login and user validation, Muon needs to use cookies. Cookies have a name, so you have to choose one to identify this session. If you place multiple *muon.php* files on your web space you should set a different cookie name for each one. Default value: *cookie*;

### Visualisation settings
- ``mu_title`` - The title to be displayed on the browser tab. Default value: *Muon*;
- ``mu_title_html`` - The title to be displayed in the top left corner of Muon's interface. Default value: *Muon*;
- ``mu_browse_title`` - Alternative title to be displayed while you’re browsing a folder. Double percentage sign *%%* will be replaced with the name of the folder;
- ``mu_editor_title`` - Alternative title to be displayed while you’re editing a file. Double percentage sign *%%* will be replaced with the name of the file;
- ``mu_home_link`` - The link to YOUR website. This is useful because Muon’s interface will display a button to go back to your site. If your home page is in the root folder of the web space you may set this option to */*.

### Further settings
- ``mu_guest_session`` and ``mu_guest_can_read`` - These two are boolean options, which means they can be **true** or **false** (whitout quotation marks). The first one means that the user is a guest and can't edit files and folders but just browse them. The second one means that guests can open editor and read (but not save, of course) files. Default: both **false**;
- ``mu_root_dir`` - By default, Muon works in the folder where you place it and in all its subfolders. If you want to make it work somewhere else, please type the relative path to the desired root directory. Please, note that all its subfolders will be accessible as well.



## Source code
Muon's source code is available on GitHub. All files located there are not required, but they're useful if you want to make your own modifications to the file manager. A PHP/HTML script - *MUON_ASSEMBLER.php* - is also provided to help you merging the whole code in a single file.



## License
Muon is a open-source software release by InfoEin under the MIT License. Check *LICENSE* for additional information.

**Muon is a proud [European](http://europa.eu/) software.**

