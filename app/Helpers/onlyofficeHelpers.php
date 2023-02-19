<?php

use App\Http\Controllers\OnlyOfficeController;
use Illuminate\Support\Facades\Log;

if (!function_exists('sendlog')) {
    function sendlog($log)
    {
        Log::info('SendLog => ' . $log);
    }
}

if (!function_exists('getHistoryDir')) {
    function getHistoryDir($storagePath)
    {
        $directory = $storagePath . "-hist";
        // if the history directory doesn't exist, make it
        if (!file_exists($directory) && !is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        return $directory;
    }
}

if (!function_exists('getVersionDir')) {
    function getVersionDir($histDir, $version)
    {
        return $histDir . DIRECTORY_SEPARATOR . $version;
    }
}

if (!function_exists('getFileVersion')) {
    function getFileVersion($histDir) {
        if (!file_exists($histDir) || !is_dir($histDir)) return 1;  // check if the history directory exists

        $cdir = scandir($histDir);
        $ver = 1;
        foreach($cdir as $key => $fileName) {
            if (!in_array($fileName,array(".", ".."))) {
                if (is_dir($histDir . DIRECTORY_SEPARATOR . $fileName)) {
                    $ver++;
                }
            }
        }
        return $ver;
    }
}

if (!function_exists('getStoragePath')) {
    function getStoragePath($fileName)
    {
        // $storagePath = trim(str_replace(array('/','\\'), DIRECTORY_SEPARATOR, $GLOBALS['STORAGE_PATH']), DIRECTORY_SEPARATOR);
        $storagePath = trim(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, public_path() . '/uploads'), DIRECTORY_SEPARATOR);
        $storagePath = DIRECTORY_SEPARATOR . $storagePath;
        if (!empty($storagePath) && !file_exists($storagePath) && !is_dir($storagePath)) {
            sendlog('in f');
            mkdir($storagePath, 0777, true);
        }
        if (realpath($storagePath) === $storagePath) {
            $directory = $storagePath;
        } else {
            $directory = $storagePath;
        }

        if ($storagePath != "") {
            $directory =  $directory  . DIRECTORY_SEPARATOR;

            // if the file directory doesn't exist, make it
            if (!file_exists($directory) && !is_dir($directory)) {
                mkdir($directory, 0777, true);
            }
        }

        if (realpath($storagePath) !== $storagePath) {
            // $directory = $directory . getCurUserHostAddres) . DIRECTORY_SEPARATOR;
            $directory = $directory;
        }

        if (!file_exists($directory) && !is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        sendlog("getStoragePath result: " . $directory . basename($fileName), "common.log");
        return realpath($storagePath) === $storagePath ? $directory . $fileName : $directory . basename($fileName);
    }
}

if (!function_exists('getForcesavePath')) {
    // get the path to the forcesaved file version
    function getForcesavePath($fileName, $create)
    {
        $storagePath = trim(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, public_path() . '/uploads'), DIRECTORY_SEPARATOR);

        // create the directory to this file version
        if (realpath($storagePath) === $storagePath) {
            $directory = $storagePath . DIRECTORY_SEPARATOR;
        } else {
            // $directory = __DIR__ . DIRECTORY_SEPARATOR . $storagePath . getCurUserHostAddres) . DIRECTORY_SEPARATOR;
            $directory = $storagePath . DIRECTORY_SEPARATOR;
        }

        if (!is_dir($directory)) return "";

        // create the directory to the history of this file version
        $directory = $directory . $fileName . "-hist" . DIRECTORY_SEPARATOR;
        if (!$create && !is_dir($directory))  return "";

        if (!file_exists($directory) && !is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        $directory = $directory . $fileName;
        if (!$create && !file_exists($directory)) return "";

        return $directory;
    }
}
if (!function_exists('getDocEditorKey')) {
    function getDocEditorKey($fileName)
    {
        $key = $fileName;// FileUri($fileName);  // get document key by adding local file url to the current user host address
        $stat = filemtime(getStoragePath($fileName));  // get creation time
        $key = $key . $stat;  // and add it to the document key
        $office_controller = new OnlyOfficeController();
        return $office_controller->GenerateRevisionId($key);  // generate the document key value
    }
    // function FileUri($file_name, $forDocumentServer = NULL)
    // {
    //     $uri = getVirtualPath($forDocumentServer) . rawurlencode($file_name);  // add encoded file name to the virtual path
    //     return $uri;
    // }
    // // get the virtual path
    // function getVirtualPath($forDocumentServer)
    // {
    //     $storagePath = trim(str_replace(array('/', '\\'), '/', $GLOBALS['STORAGE_PATH']), '/');
    //     $storagePath = $storagePath != "" ? $storagePath . '/' : "";


    //     if (realpath($storagePath) === $storagePath) {
    //         $virtPath = serverPath($forDocumentServer) . '/' . $storagePath . '/';
    //     } else {
    //         $virtPath = serverPath($forDocumentServer) . '/' . $storagePath . getCurUserHostAddress() . '/';
    //     }
    //     sendlog("getVirtualPath virtPath: " . $virtPath, "common.log");
    //     return $virtPath;
    // }
}

// function getHistory($filename, $filetype, $docKey, $fileuri) {
//     $storagePath = public_path().'/uploads';
//     $histDir = getHistoryDir(getStoragePath($filename));  // get the path to the file history
// // dd(getFileVersion($histDir));
//     if (getFileVersion($histDir) > 0) {  // check if the file was modified (the file version is greater than 0)
//         $curVer = getFileVersion($histDir);

//         $hist = [];
//         $histData = [];

//         for ($i = 1; $i <= $curVer; $i++) {  // run through all the file versions
//             $obj = [];
//             $dataObj = [];
//             $verDir = getVersionDir($histDir, $i);  // get the path to the file version

//             $key = $i == $curVer ? $docKey : file_get_contents($verDir . DIRECTORY_SEPARATOR . "key.txt");  // get document key
//             $obj["key"] = $key;
//             $obj["version"] = $i;

//             if ($i == 1) {  // check if the version number is equal to 1
//                 $createdInfo = file_get_contents($histDir . DIRECTORY_SEPARATOR . "createdInfo.json");  // get meta data of this file
//                 $json = json_decode($createdInfo, true);  // decode the meta data from the createdInfo.json file

//                 $obj["created"] = $json["created"];
//                 $obj["user"] = [
//                     "id" => $json["id"],
//                     "name" => $json["name"]
//                 ];
//             }


//             $fileExe = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

//             $prevFileName = $verDir . DIRECTORY_SEPARATOR . "prev." . $filetype;
//             $prevFileName = substr($prevFileName, strlen(getStoragePath("")));
//             $dataObj["fileType"] = $fileExe;
//             $dataObj["key"] = $key;

//             $directUrl =  $i == $curVer ? FileUri($filename, FALSE) : getHistoryDownloadUrl($filename, $i, "prev.".$fileExe, FALSE);
//             $prevFileUrl = $i == $curVer ? $fileuri : getHistoryDownloadUrl($filename, $i, "prev.".$fileExe);
//             if (realpath($storagePath) === $storagePath) {
//                 $prevFileUrl = $i == $curVer ? getDownloadUrl($filename) : getHistoryDownloadUrl($filename, $i, "prev.".$fileExe);
//                 $directUrl =  $i == $curVer ? getDownloadUrl($filename, FALSE) : getHistoryDownloadUrl($filename, $i, "prev.".$fileExe, FALSE);
//             }

//             $dataObj["url"] = $prevFileUrl;  // write file url to the data object
//             $dataObj["directUrl"] = $directUrl;  // write direct url to the data object
//             $dataObj["version"] = $i;

//             if ($i > 1) {  // check if the version number is greater than 1 (the document was modified)
//                 $changes = json_decode(file_get_contents(getVersionDir($histDir, $i - 1) . DIRECTORY_SEPARATOR . "changes.json"), true);  // get the path to the changes.json file
//                 $change = $changes["changes"][0];

//                 $obj["changes"] = $changes ? $changes["changes"] : null;  // write information about changes to the object
//                 $obj["serverVersion"] = $changes["serverVersion"];
//                 $obj["created"] = $change ? $change["created"] : null;
//                 $obj["user"] = $change ? $change["user"] : null;

//                 $prev = $histData[$i - 2];  // get the history data from the previous file version
//                 $dataObj["previous"] = [  // write information about previous file version to the data object
//                     "fileType" => $prev["fileType"],
//                     "key" => $prev["key"],
//                     "url" => $prev["url"],
//                     "directUrl" => $prev["directUrl"]
//                 ];

//                 // write the path to the diff.zip archive with differences in this file version
//                 $dataObj["changesUrl"] = getHistoryDownloadUrl($filename, $i - 1, "diff.zip");

//             }

//             // if (isJwtEnabled()) {
//                 $dataObj["token"] = jwtEncode($dataObj);
//             // }

//             array_push($hist, $obj);  // add object dictionary to the hist list
//             $histData[$i - 1] = $dataObj;  // write data object information to the history data
//         }

//         // write history information about the current file version
//         $out = [];
//         array_push($out, [
//                 "currentVersion" => $curVer,
//                 "history" => $hist
//             ],
//             $histData);
//         return $out;
//     }
// }

// get document history
function getHistory($filename, $filetype, $docKey, $fileuri) {
    $storagePath = public_path().'/uploads';
    $histDir = getHistoryDir(getStoragePath($filename));  // get the path to the file history

    if (getFileVersion($histDir) > 0) {  // check if the file was modified (the file version is greater than 0)
        $curVer = getFileVersion($histDir);

        $hist = [];
        $histData = [];

        for ($i = 1; $i <= $curVer; $i++) {  // run through all the file versions
            $obj = [];
            $dataObj = [];
            $verDir = getVersionDir($histDir, $i);  // get the path to the file version

            $key = $i == $curVer ? $docKey : file_get_contents($verDir . DIRECTORY_SEPARATOR . "key.txt");  // get document key
            $obj["key"] = $key;
            $obj["version"] = $i;

            if ($i == 1) {  // check if the version number is equal to 1
                $createdInfo = file_get_contents($histDir . DIRECTORY_SEPARATOR . "createdInfo.json");  // get meta data of this file
                $json = json_decode($createdInfo, true);  // decode the meta data from the createdInfo.json file

                $obj["created"] = $json["created"];
                $obj["user"] = [
                    "id" => $json["id"],
                    "name" => $json["name"]
                ];
            }


            $fileExe = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            $prevFileName = $verDir . DIRECTORY_SEPARATOR . "prev." . $filetype;
            $prevFileName = substr($prevFileName, strlen(getStoragePath("")));
            $dataObj["fileType"] = $fileExe;
            $dataObj["key"] = $key;

            $directUrl =  $i == $curVer ? FileUri($filename, FALSE) : getHistoryDownloadUrl($filename, $i, "prev.".$fileExe, FALSE);
            $prevFileUrl = $i == $curVer ? $fileuri : getHistoryDownloadUrl($filename, $i, "prev.".$fileExe);
            if (realpath($storagePath) === $storagePath) {
                $prevFileUrl = $i == $curVer ? getDownloadUrl($filename) : getHistoryDownloadUrl($filename, $i, "prev.".$fileExe);
                $directUrl =  $i == $curVer ? getDownloadUrl($filename, FALSE) : getHistoryDownloadUrl($filename, $i, "prev.".$fileExe, FALSE);
            }

            $dataObj["url"] = $prevFileUrl;  // write file url to the data object
            $dataObj["directUrl"] = $directUrl;  // write direct url to the data object
            $dataObj["version"] = $i;

            if ($i > 1) {  // check if the version number is greater than 1 (the document was modified)
                $changes = json_decode(file_get_contents(getVersionDir($histDir, $i - 1) . DIRECTORY_SEPARATOR . "changes.json"), true);  // get the path to the changes.json file
                $change = $changes["changes"][0];

                $obj["changes"] = $changes ? $changes["changes"] : null;  // write information about changes to the object
                $obj["serverVersion"] = $changes["serverVersion"];
                $obj["created"] = $change ? $change["created"] : null;
                $obj["user"] = $change ? $change["user"] : null;

                $prev = $histData[$i - 2];  // get the history data from the previous file version
                $dataObj["previous"] = [  // write information about previous file version to the data object
                    "fileType" => $prev["fileType"],
                    "key" => $prev["key"],
                    "url" => $prev["url"],
                    "directUrl" => $prev["directUrl"]
                ];

                // write the path to the diff.zip archive with differences in this file version
                $dataObj["changesUrl"] = getHistoryDownloadUrl($filename, $i - 1, "diff.zip");

            }

            if (true) {
                $dataObj["token"] = jwtEncode($dataObj);
            }

            array_push($hist, $obj);  // add object dictionary to the hist list
            $histData[$i - 1] = $dataObj;  // write data object information to the history data
        }

        // write history information about the current file version
        $out = [];
        array_push($out, [
                "currentVersion" => $curVer,
                "history" => $hist
            ],
            $histData);
        return $out;
    }
}

function jwtEncode($payload) {
    return \Firebase\JWT\JWT::encode($payload, config('onlyoffice.secret'), 'HS256');
}

// get url to the created file
// function getCreateUrl($fileName, $uid, $type) {
//     $ext = trim(getInternalExtension($fileName),'.');
//     return serverPath(false) . '/'
//             . "doceditor.php"
//             . "?fileExt=" . $ext
//             . "&user=" . $uid
//             . "&type=" . $type;
// }

function getHistoryDownloadUrl($fileName, $version, $file, $isServer = TRUE) {
    // $userAddress = $isServer ? "&userAddress=" . getClientIp() : "";
    return url('/').'/uploads/'. $fileName.'-hist/'.$version.'/'.$file;
    return serverPath($isServer) . '/'
        . "webeditor-ajax.php"
        . "?type=history"
        . "&fileName=" . urlencode($fileName)
        . "&ver=" . $version
        . "&file=" . urlencode($file)
        . $userAddress;
}

// get url to download a file
function getDownloadUrl($fileName, $isServer = TRUE) {
    return url('/').'/uploads/'. $fileName;
    $userAddress = $isServer ? "&userAddress=" . getClientIp() : "";
    return serverPath($isServer) . '/'
        . "webeditor-ajax.php"
        . "?type=download"
        . "&fileName=" . urlencode($fileName)
        . $userAddress;
}

function FileUri($file_name, $forDocumentServer = NULL) {
    $uri = getVirtualPath($forDocumentServer) . rawurlencode($file_name);  // add encoded file name to the virtual path
    sendlog('file uri -> '.$uri);
    return $uri;
}

// get the virtual path
function getVirtualPath($forDocumentServer) {
    $storagePath = trim(str_replace(array('/','\\'), '/', url('/').'/uploads'), '/');
    $storagePath = $storagePath != "" ? $storagePath . '/' : "";
    // $storagePath = DIRECTORY_SEPARATOR . $storagePath;

    $virtPath = $storagePath;
    // if (realpath($storagePath) === $storagePath) {
    //     $virtPath = serverPath($forDocumentServer) . '/' . $storagePath . '/';
    // } else {
    //     $virtPath = serverPath($forDocumentServer) . '/' . $storagePath ;//. getCurUserHostAddress() . '/';
    // }
    sendlog("getVirtualPath virtPath: " . $virtPath, "common.log");
    return $virtPath;
}

function serverPath($forDocumentServer = NULL) {
    return $forDocumentServer && isset($GLOBALS['EXAMPLE_URL']) && $GLOBALS['EXAMPLE_URL'] != ""
        ? $GLOBALS['EXAMPLE_URL']
        : url('/');
}

function commandRequest($method, $key, $meta = null){
    $documentCommandUrl = config('onlyoffice.doc_server_url').'/coauthoring/CommandService.ashx';

    $arr = [
        "c" => $method,
        "key" => $key
    ];

    if($meta)
        $arr["meta"] = $meta;

    $headerToken = "";
    $jwtHeader = "Authorization";

    if (true) {  // check if a secret key to generate token exists or not
        $headerToken = jwtEncode([ "payload" => $arr ]);  // encode a payload object into a header token
        $arr["token"] = jwtEncode($arr);  // encode a payload object into a body token
    }

    $data = json_encode($arr);

    $opts = array('http' => array(
        'method'  => 'POST',
        'header'=> "Content-type: application/json\r\n" .
            (empty($headerToken) ? "" : $jwtHeader.": Bearer $headerToken\r\n"),  // add a header Authorization with a header token and Authorization prefix in it
        'content' => $data
    ));

    if (substr($documentCommandUrl, 0, strlen("https")) === "https") {
        if($GLOBALS['DOC_SERV_VERIFY_PEER_OFF'] === TRUE) {
            $opts['ssl'] = array( 'verify_peer' => FALSE, 'verify_peer_name' => FALSE );
        }
    }

    $context = stream_context_create($opts);
    $response_data = file_get_contents($documentCommandUrl, FALSE, $context);

    return $response_data;
}
