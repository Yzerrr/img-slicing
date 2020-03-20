<?php
/**
 * Created by PhpStorm.
 * User: Joey y
 * Date: 18-10-2017
 * Time: 21:38
 */

// Define the root of the site
define('SITE_ROOT', realpath(dirname(__FILE__)));

// Declare the array for the width
$widths = array();

if (isset($_FILES['image'])) {

    $maxWidthInput = $_POST['maxWidth'];

    // Count how many images were uploaded
    $imageCount = count($_FILES['image']['name']);

    // Loop through the uploaded images
    for ($i = 0; $i < $imageCount; $i++) {
        // Declare the errors array
        $errors = array();
        // lowercase the filename
        $tmp = explode('.', $_FILES['image']['name'][$i]);
        $file = end($tmp);
        $file_ext = strtolower($file);
        // Check if there are any errors
        if (empty($errors) == true) {
            // Upload the image
            move_uploaded_file($_FILES['image']['tmp_name'][$i], SITE_ROOT . "\\uploads\\" . $_FILES['image']['name'][$i]);
            // Get the image width and height
            list($width, $height) = getimagesize(SITE_ROOT . "\\uploads\\" . $_FILES['image']['name'][$i]);
            // Add the width to the array
            $widths[] = array(
                'image_name' => "\\..\\uploads\\" . $_FILES['image']['name'][$i],
                'width' => $width
            );
        } else {
            print_r($errors);
        }
    }

    // Declare the max width of the table from the form Input
    $maxWidth = $maxWidthInput;

    // Declare the html variable
    $html = "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"" . $maxWidthInput . "\">\n";
    $html .= "\t<tbody>\n";

    $index = 0;

    $chunkWidthArray = array();

    // Loop through the widths array
    foreach ($widths as $key => $width) {
        // Check the width and subtract it from $maxWidth
        $maxWidth = $maxWidth - (int)$width['width'];
        // Check if the maxWidth is 0 or below
        if ($maxWidth >= 0) {
            // Add the image name to the array and chunk it
            $chunkWidthArray[$index][] = $width;
        } else {
            $maxWidth = $maxWidthInput;
            // Set the maxWidth to $maxWidthInput again.
            $maxWidth = $maxWidth - $width['width'];
            // Increment the index  - add enter
            $index++;
            // Add the image name to the array and chunk it
            $chunkWidthArray[$index][] = $width;
        }
    }

    // Loop through the chunk array
    foreach ($chunkWidthArray as $chunk) {
        // Loop over the chunks .... OUTPUT
        for ($i = 0; $i < count($chunk); $i++) {
            $html .= "\t\t<tr>\n";
            if ($chunk[$i]['width'] >= $maxWidthInput) {
                $html .= "\t\t\t<td width=\"" . $chunk[$i]['width'] . "\">" . $chunk[$i]['image_name'] . "</td>\n";    /*. $imageArray['image_name']  For the image path*/
            } else {
                $html .= "\t\t\t<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"" . $maxWidthInput . "\">\n";
                $html .= "\t\t\t\t<tr>\n";
                for ($j = $i; $j < count($chunk); $j++) {
                    $html .= "\t\t\t\t\t<td width=\"" . $chunk[$j]['width'] . "\">" . $chunk[$j]['image_name'] . "</td>\n";
                    $i++;
                }
                $html .= "\t\t\t\t</tr>\n";
                $html .= "\t\t\t</table>\n";
            }
            $html .= "\t\t</tr>\n";
        }
    }



    $html .= "\t</tbody>\n";
    $html .= "</table>";


    // Replace the < with &lt;
    $replaceFirst = str_replace('<', '&lt;', $html);
    // Replace the > with &gt;
    $replaceSecond = str_replace('>', '&gt;', $replaceFirst);
    // Add the <code> and <pre> to create a result
    $result = "<code><pre>" . $replaceSecond . "</pre></code>";

    // echo the result
    echo '<div id="copyBtn">' . $result . '</div>';
}


?>


<!DOCTYPE html>
<html>
<head>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!-- custom style-->
    <link rel="stylesheet" href="styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>


</head>
<title>Parse Images UI</title>
<body>
<div class="container">
    <div class="row">
        <div class="col-sm-offset-4 col-sm-4">
            <button class=" copy btn btn-primary" onclick="copyToClipboard('#copyBtn')">Copy the Table</button>
        </div>
    </div>
</div>
</body>

</html>
<script type="text/javascript">
    function copyToClipboard(element) {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($(element).text()).select();
        document.execCommand("copy");
        $temp.remove();
    }
</script>

