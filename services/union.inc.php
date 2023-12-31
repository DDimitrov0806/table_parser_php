<?php

if (isset($_POST['submit'])) {
    require_once "../file.php";

    $fileName1 = $_POST['joinOption1'];
    $fileName2 = $_POST['joinOption2'];

    require_once "db-connect.inc.php";
    require_once "file.inc.php";

    $file1 = getFileByFilename($conn, $fileName1);

    if ($file1 === false) {
        header("location: ../parser.php?error=fileNotFound");
        exit();
    }

    $file2 = getFileByFilename($conn, $fileName2);

    if ($file2 === false) {
        header("location: ../parser.php?error=fileNotFound");
        exit();
    }

    if($file1->getFileHeader() !== $file2->getFileHeader()) {
        header("location: ../parser.php?error=fileHeadersNotEqual");
        exit();
    }

    $fileData1 = $file1->getFileData();
    $fileData2 = $file2->getFileData();

    $fileName = $file1->getFileName() . "_" . $file2->getFileName();

    $unionData = array_merge($fileData1, $fileData2);

    insertFile($conn, $unionData, $fileName, $file1->getFileHeader());
    header("location: ../parser.php");
}
