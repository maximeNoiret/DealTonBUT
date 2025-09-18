<?php

class Layout {
  static function print_page(string $title, string $content) {
?><!DOCTYPE html>
<html>
<head>
  <title><?=$title?></title>
</head>
<body>
<?php
    echo $content;
?></body>
  </html><?php
  }
}

?>
