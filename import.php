<?php
session_start();
include('dbconfig.php');

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (isset($_POST['save_excel_data'])) {
    $fileName = $_FILES['import_file']['name'];
    $file_ext = pathinfo($fileName, PATHINFO_EXTENSION);

    $allowed_ext = ['xls', 'csv', 'xlsx'];

    if (in_array($file_ext, $allowed_ext)) {
        $inputFileNamePath = $_FILES['import_file']['tmp_name'];
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileNamePath);
        $data = $spreadsheet->getActiveSheet()->toArray();

        $count = "0";
        foreach ($data as $row) {
            if ($count > 0) {
                $title = $row['0'];
                $description_short = $row['1'];
                $description_long = $row['2'];
                $year = $row['3'];
                $country_id = $row['4'];
                $rating = $row['5'];
                $genre_id = $row['6'];

                $actorsData = $row['7'];
                $actorsList = explode(",", $actorsData);
                $actorsArray = json_encode($actorsList);

                $director = $row['8'];
                $featured = $row['9'];
                $kids_restriction = $row['10'];
                $url = $row['11'];
                $trailer_url = $row['12'];
                $duration = $row['13'];

                $moviesQuery = "INSERT INTO movie (title,description_short,description_long,year,country_id,rating,genre_id,actors,director,featured,kids_restriction,url,trailer_url,duration) 
                                VALUES ('$title','$description_short','$description_long','$year','$country_id','$rating','$genre_id','$actorsArray','$director','$featured','$kids_restriction','$url','$trailer_url','$duration')";
                $result = mysqli_query($conn, $moviesQuery);

                $movie_id = mysqli_insert_id($conn);


                $thumbnailData = $row['14'];
                $imageData = file_get_contents($thumbnailData);
                file_put_contents('C:/xampp/htdocs/Netflix/assets/global/movie_thumb/' . $movie_id . '.jpg', $imageData);

                $posterData = $row['15'];
                $imageData = file_get_contents($posterData);
                file_put_contents('C:/xampp/htdocs/Netflix/assets/global/movie_poster/' . $movie_id . '.jpg', $imageData);


                $msg = true;
            } else {
                $count = "1";
            }
        }

        if (isset($msg)) {
            $_SESSION['message'] = "Successfully Imported";
            header('Location: index.php');
            exit(0);
        } else {
            $_SESSION['message'] = "Not Imported";
            header('Location: index.php');
            exit(0);
        }
    } else {
        $_SESSION['message'] = "Invalid File";
        header('Location: index.php');
        exit(0);
    }
}
?>