<?php
define("PASS_URL", "http://213.181.208.127/webfejlesztes/password.txt");
define("HOSTNAME", 'localhost');
define("USERNAME", 'root');
define("PASSWORD", '');
define("DATABASE", "adatok");
define("TABLE", "tabla");

$content = file_get_contents(PASS_URL);

function createDBandTable()
{

    $connection = new mysqli(HOSTNAME, USERNAME, PASSWORD);

    $sql = "CREATE DATABASE IF NOT EXISTS " . DATABASE;
    if ($connection->query($sql) === TRUE) {
        echo "Database created successfully\n";
    } else {
        echo "Error creating database: " . $connection->error;
    }
    mysqli_close($connection);

    $connection = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE);

    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    $sql = "DROP TABLE IF EXISTS " . TABLE;

    if (mysqli_query($connection, $sql)) {
        echo "Table removed successfully\n";
    } else {
        echo "Table is not removed successfully ";
    }


    $sql = "create table " . TABLE . "(id INT NOT NULL AUTO_INCREMENT,Username VARCHAR(50) NOT NULL,Titkos VARCHAR(10), primary key (id))";

    if (mysqli_query($connection, $sql)) {
        echo "Table created successfully\n";
    } else {
        echo "Table is not created successfully ";
    }

    $sql = "INSERT INTO " . TABLE . " (Username, Titkos) VALUES
            ('katika@gmail.com','piros'),
            ('arpi40@freemail.hu','zold'),
            ('zsanettka@hotmail.com','sarga'),
            ('hatizsak@protonmail.com','kek'),
            ('terpeszterez@citromail.hu','fekete'),
            ('nagysanyi@gmail.hu','feher')";

    if (mysqli_query($connection, $sql)) {
        echo "Data inserted successfully\n";
    } else {
        echo "Data is not inserted successfully ";
    }


    mysqli_close($connection);

}

function getColor($username){

    $connection = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE);

    $sql = "SELECT Titkos from ".TABLE." WHERE Username = '".$username."'";
    $result = $connection->query($sql);

    if ($result->num_rows > 0) {

        $row = $result->fetch_assoc();
        $titkos = $row["Titkos"];

        return "http://shrek.unideb.hu/~herakles/ZH/".$titkos.".png";

    }

    mysqli_close($connection);
}

$entetedusername = $_POST["username"];
$enteredpassword = $_POST["pass"];

login($entetedusername,$enteredpassword);

function login($user,$password){
    $found=false;

    $handle = fopen(PASS_URL, "r");
    if ($handle) {
        while (($line = fgets($handle)) !== false) {

            $data = explode("*", decode($line));
            $dbuser = $data[0];
            $dbpass = $data[1];

            if($dbuser==$user && $dbpass==$password){
                $found=true;
                echo '<center><h2>Sikeres bejelentkezés</h2></center>';
                //echo getColor($dbuser);
                echo '<center><img src="'.getColor($dbuser).'" alt="icon" /></center>';
            }

            if($dbuser==$user && $dbpass!=$password){
                $found=true;
                echo "<center><h2>Hibás jelszó</h2></center>";
                echo "<script>setTimeout(\"location.href = 'http://www.police.hu';\",3000);</script>";
            }

        }

        if($found==false){
            echo "<center><h2>Nincs ilyen felhasználó</h2></center>";
        }

        fclose($handle);
    } else {
        echo "Error open file";
    }
}

function decode($text)
{
    $keyarray = array(5, -14, 31, -9, 3);
    $length = strlen($text);
    $decoded = "";
    for ($i = 0; $i < $length; $i++) {
        if ($text[$i] != "\n")
            $decoded .= chr(ord($text[$i]) - $keyarray[$i % 5]);;
    }
    return $decoded;
}
