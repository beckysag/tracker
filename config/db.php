<?php


if ( $_SERVER['DOCUMENT_ROOT'] == '/nfs/ca/info/web' ) { // if OSU

	define("DB_HOST", "engr-db.engr.oregonstate.edu");
	define("DB_NAME", "cs419group3");
	define("DB_USER", "cs419group3");
	define("DB_PASS", "kFEsQrr7");
	define("DB_PORT", 3307);

} elseif ( $_SERVER['DOCUMENT_ROOT'] == '/Users/rsagalyn/Dropbox/htdocs' ) { // if local

	define("DB_HOST", "127.0.0.1");
	define("DB_NAME", "library_db");
	define("DB_USER", "root");
	define("DB_PASS", "root");
	define("DB_PORT", 3306);

}

