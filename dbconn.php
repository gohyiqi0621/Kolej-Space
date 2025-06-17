<?php
  $host = 'localhost';
  $username = 'root'; // Default XAMPP MySQL username
  $password = ''; // Default XAMPP MySQL password (empty)
  $database = 'KolejSpace';

  $conn = mysqli_connect($host, $username, $password, $database);

  if (!$conn) {
      die("Connection failed: " . mysqli_connect_error());
  }
  ?>