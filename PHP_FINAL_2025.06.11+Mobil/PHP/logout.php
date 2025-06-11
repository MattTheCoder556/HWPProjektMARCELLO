<?php
session_start();
session_unset();
session_destroy();
header("Location: https://mmm.stud.vts.su.ac.rs/index.php");
exit();
