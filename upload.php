<?php
/**
 * Created by PhpStorm.
 * User: kong
 * Date: 1/2/17
 * Time: 9:22 AM
 */

//include the S3 class
if (!class_exists('S3')) require_once('S3.php');

//AWS access info
if (!defined('awsAccessKey')) define('awsAccessKey', 'AKIAJGKTM67FZFIX3CEA');
if (!defined('awsSecretKey')) define('awsSecretKey', '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');
$s3 = new S3(awsAccessKey, awsSecretKey);
//print_r($s3->listBuckets());

$orderId = $_POST['order'];
$folder = $_POST['upload'];
$files = $_FILES['file'];
//print_r($folder);
//print_r($files);


$i = -1;
$bucketName = 'tradebanner';
if (is_null($orderId)) $orderId = 'null';
$validExt = array("jpeg", "jpg", "png", "psd", "tiff", "pdf", "aiff");
$maxFile = 1024 * 1024 * 1024;
$msg = '';

foreach( $folder as $name ) {
    $i++;
    $name = array_unique($name);
    if (isset($files['name'][$i]) && $files['name'][$i] != '') {

        $fileName = $files['name'][$i];
        $baseName = basename($fileName);
        $ext = explode('.', $baseName);
        $file_ext = end($ext);
        $size = $files['size'][$i];
        $uploadFile = $files['tmp_name'][$i];

        // $msg .= ' size ' . $size . ' max size ' . $maxFile;

        // Load foreach options
        if (is_array($name)) {
            foreach ($name as $option) {
                $option = trim($option);
                $msg .=  '<p>'.$option . '/' . $fileName . " &#8594; ";

                // Check File exist
                if ( is_null($fileName) || $fileName == '' ) {
                    $msg .= ' <span class="error">File is not exist</span></p>';
                    continue;
                }
                
                // Make full path            
                $path = $option . '/' . $orderId . '/' . $baseName;
             
                // Upload
                if ($size < $maxFile && in_array($file_ext, $validExt)) {
                    $check = $s3->putObjectFile($uploadFile, $bucketName, $path, $s3::ACL_PUBLIC_READ);
                    $msg .= ($check) ? '<span class="success">&#10003; Success</span></p>' : '<span class="error">&#215; Failed</span></p>';
                } else $msg .= '<span class="warning">&#33; Exceed the maximum upload size for file or File extension is not valid</span></p>';
            }
        }       

    }   
}

echo $msg;

//array(5) {
//    ["name"]=> array(1) {
//        [0]=> string(14) "11-390x300.png" }
//    ["type"]=> array(1) {
//        [0]=> string(9) "image/png" }
//    ["tmp_name"]=> array(1) {
//        [0]=> string(14) "/tmp/phpXI2zL9" }
//    ["error"]=> array(1) {
//        [0]=> int(0) }
//    ["size"]=> array(1) {
//        [0]=> int(278996) }
//}
?>