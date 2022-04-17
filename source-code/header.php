<?php set_time_limit(0);

/*********************************************
*  MUON FILE MANAGER                         *
*  Website: http://infoein.github.io/muon    *
*  Released under the MIT License            *
**********************************************/

/* Login */
define( "mu_username",        "admin" );//required
define( "mu_password",        "5f4dcc3b5aa765d61d8327deb882cf99" ); //required, md5-encrypted
define( "mu_cookie",          "my_site" ); //default: "cookie"

/* Visualisation */
define( "mu_title",           "Muon" ); //default: "Muon"
define( "mu_title_html",      "Muon" ); //default: "Muon"
define( "mu_browse_title",    "~ %% | ".mu_title );
define( "mu_editor_title",    "%% | ".mu_title );
define( "mu_home_link",       "https://www.example.com/" ); //default: "/"

/* Other settings */
define( "mu_guest_session",   false ); //default: false
define( "mu_guest_can_read",  false ); //default: false
define( "mu_root_dir" , "./" ); //relative path, by default "./"


/*************************************************/
/*End of Muon settings*/ ?>