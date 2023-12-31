<?php

session_start();

function insertFile($conn, $fileData, $fileName, $fileHeader)
{
    if(getFileByFilename($conn,$fileName) !== false) {
        header("location: ../parser.php?error=fileExists");
        exit();
    }

    $sql = "INSERT INTO files(userId,fileData,fileName,fileHeaders) VALUES (?,?,?,?);";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../parser.php?error=stmtFailed");
        exit();
    }

    $fileData = json_encode($fileData);
    $fileHeader = json_encode($fileHeader);
    $fileName = str_replace(' ', '', $fileName);

    mysqli_stmt_bind_param($stmt, "isss", $_SESSION['userId'], $fileData, $fileName, $fileHeader);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function getFileByFilename($conn, $fileName)
{
    $sql = "SELECT * FROM files WHERE fileName = ? AND userId = ?;";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../parser.php?error=stmtFailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "si", $fileName,$_SESSION['userId']);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        return new FileInfo(json_decode($row['fileData'],true), $row['fileName'], json_decode($row['fileHeaders'],true));
    } else {
        return false;
    }
}

function getFilesForUser($conn) {
    $sql = "SELECT * FROM files WHERE userId = ?;";
    $stmt = mysqli_stmt_init($conn);
    
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: upload.php?error=stmtFailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "i", $_SESSION['userId']);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $data = array();

    while($row = mysqli_fetch_assoc($result)) {
        array_push($data, new FileInfo(json_decode($row['fileData'],true),$row['fileName'],json_decode($row['fileHeaders'],true)));
    }

    return $data;
}