<?php

/**
 * Modified version of:
   Class login
 *
 * handles the user login/logout/session
 * @author Panique
 * @link http://www.php-login.net
 * @link https://github.com/panique/php-login/
 * @license http://opensource.org/licenses/MIT MIT License
 */

class Login
{
    /**
     * @var object The database connection
     */
    private $db_connection = null;
    /**
     * @var string The user's name
     */
    private $user_name = "";
    /**
     * @var string The user's mail
     */
    private $user_email = "";
    /**
     * @var string The user's password hash
     */
    private $user_password_hash = "";
    /**
     * @var boolean The user's login status
     */
    private $user_is_logged_in = false;
    /**
     * @var boolean The user's login status
     */
    private $user_is_admin = false;
    /**
     * @var array Collection of error messages
     */
    public $errors = array();
    /**
     * @var array Collection of success / neutral messages
     */
    public $messages = array();

    /**
     * the function "__construct()" automatically starts whenever an object of this class 
     * is created, you know, when you do "$login = new Login();"
     */
    public function __construct()
    {
        // create/read session
        session_start();

        // check the possible login actions:
        // 1. logout (happen when user clicks logout button)
        // 2. login via session data (happens each time user opens a page on your
        //		php project AFTER he has sucessfully logged in via the login form)
        // 3. login via post data, which means simply logging in via the login form.
        //		after the user has submit his login/password successfully, his
        //    logged-in-status is written into his session data on the server.
        // 		this is the typical behaviour of common login scripts.

        // if user tried to log out
        if (isset($_GET["logout"])) {
            $this->doLogout();
        }
        // if user has an active session on the server
        elseif (!empty($_SESSION['user_name']) && ($_SESSION['user_logged_in'] == 1)) {
            $this->loginWithSessionData();
        }
        // if user just submitted a login form
        elseif (isset($_POST["login"])) {
            $this->loginWithPostData();
        }
    }

    /**
     * log in with session data
     */
    private function loginWithSessionData()
    {
        // set logged in status to true, because we just checked for this:
        // !empty($_SESSION['user_name']) && ($_SESSION['user_logged_in'] == 1)
        // when we called this method (in the constructor)
        $this->user_is_logged_in = true;
        
		// if user is an admin, set admin status to true
		if ($_SESSION['user_type'] == 1) {
			$this->user_is_admin = true;					
		}
        
    }

    /**
     * log in with post data
     */
    private function loginWithPostData()
    {

// Log POST and SESSION superglobals to file
$post = print_r($_POST,true);
$sess = print_r($_SESSION,true);
$log = 'POST: ' . $post . 'SESSION: ' . $sess;
file_put_contents('log.txt',$log);

        // if POST data contains non-empty user_name & non-empty user_password
        if (!empty($_POST['user_name']) && !empty($_POST['user_password'])) {

            // create a db connection, using the constants from config/db.php 
            // (which we loaded in index.php)
            $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

            // if no connection errors (= working database connection)
            if (!$this->db_connection->connect_errno) {

                // escape the POST stuff
                $this->user_name = $this->db_connection->real_escape_string($_POST['user_name']);
                // database query, getting all the info of the selected user
                $sql = "SELECT user_name, user_type, user_password_hash 
                		FROM users WHERE user_name = '" . $this->user_name . "';";
                $checklogin = $this->db_connection->query($sql);

                // if this user exists
                if ($checklogin->num_rows == 1) {

                    // get result row (as an object)
                    $result_row = $checklogin->fetch_object();

                    // using PHP 5.5's password_verify() function to check if the provided 
                    // passwords fits to the hash of that user's password
                    if (password_verify($_POST['user_password'], $result_row->user_password_hash)) {

                        // write user data into PHP SESSION [a file on your server]
                        $_SESSION['user_name'] = $result_row->user_name;
                        $_SESSION['user_logged_in'] = 1;
                        $_SESSION['user_type'] = $result_row->user_type;

                        // set the login status to true
                        $this->user_is_logged_in = true;
		
						// if user is an admin, set admin status to true
						if ($result_row->user_type == 1) {
	                        $this->user_is_admin = true;					
						}

                    } else {
                        $this->errors[] = "Wrong password. Try again.";
                    }
                } else {
                    $this->errors[] = "This user does not exist.";
                }
            } else {
                $this->errors[] = "Database connection problem.";
            }
        
        	$this->db_connection->close();
        
        } elseif (empty($_POST['user_name'])) {
            $this->errors[] = "Username required";
        } elseif (empty($_POST['user_password'])) {
            $this->errors[] = "Password required";
        }
    }

    /**
     * perform the logout
     */
    public function doLogout()
    {
        $_SESSION = array();
        session_destroy();
        $this->user_is_logged_in = false;
        $this->messages[] = "You have been logged out.";

    }

    /**
     * simply return the current state of the user's login
     * @return boolean user's login status
     */
    public function isUserLoggedIn()
    {
        return $this->user_is_logged_in;
    }

    /**
     * return true if user is an admin, else false
     * @return boolean user's admin status
     */
    public function isUserAdmin()
    {
        return $this->user_is_admin;
    }
}