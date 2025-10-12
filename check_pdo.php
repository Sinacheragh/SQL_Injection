<?php
echo '<pre>';
echo "Available PDO drivers:\n";
print_r(PDO::getAvailableDrivers());
echo '</pre>';
