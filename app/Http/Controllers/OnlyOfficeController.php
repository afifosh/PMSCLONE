<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OnlyOfficeController extends Controller
{
    public function updateFile(Request $request, File $file)
    {
        $result["error"] = 0;

        // get the body of the post request and check if it is correct
        $data = $this->readBody();
        Log::info('data => ' . json_encode($data));

        if (!empty($data->error)) {
            return $data;
        }

        $_trackerStatus = array(
            0 => 'NotFound',
            1 => 'Editing',
            2 => 'MustSave',
            3 => 'Corrupted',
            4 => 'Closed',
            6 => 'MustForceSave',
            7 => 'CorruptedForceSave'
        );
        $status = $_trackerStatus[$data->status];  // get status from the request body

        // $userAddress = $_GET["userAddress"];
        // $fileName = basename($_GET["fileName"]);
        $fileName = $file->file;

        sendlog("   CommandRequest status: " . $data->status, "webedior-ajax.log");
        sendlog('all => ' . json_encode($data));
        switch ($status) {
            case "Editing":  // status == 1
                if ($data->actions && $data->actions[0]->type == 0) {   // finished edit
                    $user = $data->actions[0]->userid;  // the user who finished editing
                    if (array_search($user, $data->users) === FALSE) {
                        sendlog("   CommandRequest forcesave: init", "webedior-ajax.log");
                        $commandRequest = commandRequest("forcesave", $data->key);  // create a command request with the forcasave method
                        sendlog("   CommandRequest forcesave: " . serialize($commandRequest), "webedior-ajax.log");
                    }
                }
                break;
            case "MustSave":  // status == 2
            case "Corrupted":  // status == 3
                $result = $this->processSave($data, $fileName);
                break;
            case "MustForceSave":  // status == 6
            case "CorruptedForceSave":  // status == 7
                $result = $this->processForceSave($data, $fileName);
                break;
        }

        sendlog("Track RESULT: " . json_encode($result), "webedior-ajax.log");
        echo json_encode($result);
        die();
    }

    // read request body
    protected function readBody()
    {
        $result["error"] = 0;

        // get the body of the post request and check if it is correct
        if (($body_stream = file_get_contents('php://input')) === FALSE) {
            $result["error"] = "Bad Request";
            return $result;
        }

        $data = json_decode($body_stream, false);

        // check if the response is correct
        if ($data === NULL) {
            $result["error"] = "Bad Response";
            return $result;
        }

        sendlog("   InputStream data: " . serialize($data), "webedior-ajax.log");

        // check if the document token is enabled
        if (false) {
            sendlog("   jwt enabled, checking tokens", "webedior-ajax.log");

            $inHeader = true; //false;
            // $data = "";
            $jwtHeader = "Authorization";

            // if (!empty($data["token"])) {  // if the document token is in the data
            //     $data = $this->jwtDecode($data["token"]);  // decode it
            //     sendlog("   jwt in body", "webedior-ajax.log");
            // } elseif (!empty(apache_request_headers()[$jwtHeader])) {  // if the Authorization header exists
            //     $data = $this->jwtDecode(substr(apache_request_headers()[$jwtHeader], strlen("Bearer ")));  // decode its part after Authorization prefix
            //     $inHeader = true;
            //     sendlog("   jwt in header", "webedior-ajax.log");
            // } else {  // otherwise, an error occurs
            //     sendlog("   jwt token wasn't found in body or headers", "webedior-ajax.log");
            //     $result["error"] = "Expected JWT";
            //     return $result;
            // }

            // if ($data === "") {  // invalid signature error
            //     sendlog("   token was found but signature is invalid", "webedior-ajax.log");
            //     $result["error"] = "Invalid JWT signature";
            //     return $result;
            // }

            if ($inHeader) $data = $data->payload;
        }

        return $data;
    }

    // file force saving process
    function processForceSave($data, $fileName)
    {
        $downloadUri = $data->url;
        if ($downloadUri === null) {
            $result["error"] = 1;
            return $result;
        }

        $curExt = strtolower('.' . pathinfo($fileName, PATHINFO_EXTENSION));  // get current file extension
        $downloadExt = strtolower('.' . $data->filetype);  // get the extension of the downloaded file

        // TODO [Delete in version 7.0 or higher]
        if (!$downloadExt) $downloadExt = strtolower('.' . pathinfo($downloadUri, PATHINFO_EXTENSION));    // Support for versions below 7.0

        $newFileName = false;

        // convert downloaded file to the file with the current extension if these extensions aren't equal
        if ($downloadExt != $curExt) {
            $key = GenerateRevisionId($downloadUri);

            try {
                sendlog("   Convert " . $downloadUri . " from " . $downloadExt . " to " . $curExt, "webedior-ajax.log");
                $convertedUri;  // convert file and give url to a new file
                $percent = GetConvertedUri($downloadUri, $downloadExt, $curExt, $key, FALSE, $convertedUri);
                if (!empty($convertedUri)) {
                    $downloadUri = $convertedUri;
                } else {
                    sendlog("   Convert after save convertedUri is empty", "webedior-ajax.log");
                    $baseNameWithoutExt = substr($fileName, 0, strlen($fileName) - strlen($curExt));
                    $newFileName = true;
                }
            } catch (Exception $e) {
                sendlog("   Convert after save " . $e->getMessage(), "webedior-ajax.log");
                $newFileName = true;
            }
        }

        $saved = 1;

        if (!(($new_data = file_get_contents($downloadUri)) === FALSE)) {
            $baseNameWithoutExt = substr($fileName, 0, strlen($fileName) - strlen($curExt));
            $isSubmitForm = $data->forcesavetype == 3;  // SubmitForm

            if ($isSubmitForm) {
                if ($newFileName) {
                    $fileName = GetCorrectName($baseNameWithoutExt . "-form" . $downloadExt);  // get the correct file name if it already exists
                } else {
                    $fileName = GetCorrectName($baseNameWithoutExt . "-form" . $curExt);
                }
                $forcesavePath = getStoragePath($fileName);
            } else {
                if ($newFileName) {
                    $fileName = GetCorrectName($baseNameWithoutExt . $downloadExt);
                }
                // create forcesave path if it doesn't exist
                $forcesavePath = getForcesavePath($fileName, false);
                if ($forcesavePath == "") {
                    $forcesavePath = getForcesavePath($fileName, true);
                }
            }

            file_put_contents($forcesavePath, $new_data, LOCK_EX);

            if ($isSubmitForm) {
                $uid = $data->actions[0]->userid;  // get the user id
                createMeta($fileName, $uid, "Filling Form");  // create meta data for the forcesaved file
            }

            $saved = 0;
        }

        $result["error"] = $saved;

        return $result;
    }

    protected function jwtDecode($token)
    {
        try {
            $payload = \Firebase\JWT\JWT::decode($token, config('onlyoffice.secret'), array("HS256"));
        } catch (\UnexpectedValueException $e) {
            $payload = "";
        }

        return $payload;
    }

    // file saving process
    // protected function processSave($data, $fileName)
    // {
    //     $downloadUri = $data->url;
    //     if ($downloadUri === null) {
    //         $result["error"] = 1;
    //         return $result;
    //     }

    //     $curExt = strtolower('.' . pathinfo($fileName, PATHINFO_EXTENSION));  // get current file extension
    //     $downloadExt = strtolower('.' . $data->filetype);  // get the extension of the downloaded file

    //     // TODO [Delete in version 7.0 or higher]
    //     if (!$downloadExt) $downloadExt = strtolower('.' . pathinfo($downloadUri, PATHINFO_EXTENSION)); // Support for versions below 7.0

    //     $newFileName = $fileName;

    //     // convert downloaded file to the file with the current extension if these extensions aren't equal
    //     if ($downloadExt != $curExt) {
    //         $key = $this->GenerateRevisionId($downloadUri);

    //         try {
    //             sendlog("   Convert " . $downloadUri . " from " . $downloadExt . " to " . $curExt, "webedior-ajax.log");
    //             $convertedUri = '';  // convert file and give url to a new file
    //             $percent = GetConvertedUri($downloadUri, $downloadExt, $curExt, $key, FALSE, $convertedUri);
    //             if (!empty($convertedUri)) {
    //                 $downloadUri = $convertedUri;
    //             } else {
    //                 sendlog("   Convert after save convertedUri is empty", "webedior-ajax.log");
    //                 $baseNameWithoutExt = substr($fileName, 0, strlen($fileName) - strlen($curExt));
    //                 $newFileName = GetCorrectName($baseNameWithoutExt . $downloadExt);  // get the correct file name if it already exists
    //             }
    //         } catch (Exception $e) {
    //             sendlog("   Convert after save " . $e->getMessage(), "webedior-ajax.log");
    //             $baseNameWithoutExt = substr($fileName, 0, strlen($fileName) - strlen($curExt));
    //             $newFileName = GetCorrectName($baseNameWithoutExt . $downloadExt);
    //         }
    //     }

    //     $saved = 1;
    //     sendlog(json_encode($data));
    //     if (!(($new_data = file_get_contents($downloadUri)) === FALSE)) {
    //         $storagePath = getStoragePath($newFileName);  // get the file path
    //         $histDir = getHistoryDir($storagePath);  // get the path to the history direction
    //         $verDir = getVersionDir($histDir, getFileVersion($histDir));  // get the path to the file version

    //         mkdir($verDir, 0777, true);  // if the path doesn't exist, create it

    //         rename(getStoragePath($fileName), $verDir . DIRECTORY_SEPARATOR . "prev" . $curExt);  // get the path to the previous file version and rename the storage path with it
    //         file_put_contents($storagePath, $new_data, LOCK_EX);  // save file to the storage directory

    //         if (isset($data->changesurl))
    //             if ($changesData = @file_get_contents(@$data->changesurl)) {
    //                 file_put_contents($verDir . DIRECTORY_SEPARATOR . "diff.zip", $changesData, LOCK_EX);  // save file changes to the diff.zip archive
    //             }

    //         $histData = empty($data->changeshistory) ? null : $data->changeshistory;
    //         if (empty($histData)) {
    //             $histData = json_encode($data->history, JSON_PRETTY_PRINT);
    //         }
    //         if (!empty($histData)) {
    //             file_put_contents($verDir . DIRECTORY_SEPARATOR . "changes.json", $histData, LOCK_EX);  // write the history changes to the changes.json file
    //         }
    //         file_put_contents($verDir . DIRECTORY_SEPARATOR . "key.txt", $data->key, LOCK_EX);  // write the key value to the key.txt file

    //         $forcesavePath = getForcesavePath($newFileName, false);  // get the path to the forcesaved file version
    //         if ($forcesavePath != "") {  // if the forcesaved file version exists
    //             unlink($forcesavePath);  // remove it
    //         }

    //         $saved = 0;
    //     }

    //     $result["error"] = $saved;

    //     return $result;
    // }
    function processSave($data, $fileName)
    {
        $downloadUri = $data->url;
        if ($downloadUri === null) {
            $result["error"] = 1;
            return $result;
        }

        $curExt = strtolower('.' . pathinfo($fileName, PATHINFO_EXTENSION));  // get current file extension
        $downloadExt = strtolower('.' . $data->filetype);  // get the extension of the downloaded file

        // TODO [Delete in version 7.0 or higher]
        if (!$downloadExt) $downloadExt = strtolower('.' . pathinfo($downloadUri, PATHINFO_EXTENSION)); // Support for versions below 7.0

        $newFileName = $fileName;

        // convert downloaded file to the file with the current extension if these extensions aren't equal
        if ($downloadExt != $curExt) {
            $key = GenerateRevisionId($downloadUri);

            try {
                sendlog("   Convert " . $downloadUri . " from " . $downloadExt . " to " . $curExt, "webedior-ajax.log");
                $convertedUri;  // convert file and give url to a new file
                $percent = GetConvertedUri($downloadUri, $downloadExt, $curExt, $key, FALSE, $convertedUri);
                if (!empty($convertedUri)) {
                    $downloadUri = $convertedUri;
                } else {
                    sendlog("   Convert after save convertedUri is empty", "webedior-ajax.log");
                    $baseNameWithoutExt = substr($fileName, 0, strlen($fileName) - strlen($curExt));
                    $newFileName = GetCorrectName($baseNameWithoutExt . $downloadExt, $userAddress);  // get the correct file name if it already exists
                }
            } catch (Exception $e) {
                sendlog("   Convert after save " . $e->getMessage(), "webedior-ajax.log");
                $baseNameWithoutExt = substr($fileName, 0, strlen($fileName) - strlen($curExt));
                $newFileName = GetCorrectName($baseNameWithoutExt . $downloadExt, $userAddress);
            }
        }

        $saved = 1;

        if (!(($new_data = file_get_contents($downloadUri)) === FALSE)) {
            $storagePath = getStoragePath($newFileName);  // get the file path
            $histDir = getHistoryDir($storagePath);  // get the path to the history direction
            $verDir = getVersionDir($histDir, getFileVersion($histDir));  // get the path to the file version

            mkdir($verDir, 0777, true);  // if the path doesn't exist, create it

            rename(getStoragePath($fileName), $verDir . DIRECTORY_SEPARATOR . "prev" . $curExt);  // get the path to the previous file version and rename the storage path with it
            file_put_contents($storagePath, $new_data, LOCK_EX);  // save file to the storage directory

            if ($changesData = file_get_contents($data->changesurl)) {
                file_put_contents($verDir . DIRECTORY_SEPARATOR . "diff.zip", $changesData, LOCK_EX);  // save file changes to the diff.zip archive
            }

            $histData = empty($data->changeshistory) ? null : $data->changeshistory;
            if (empty($histData)) {
                $histData = json_encode($data->history, JSON_PRETTY_PRINT);
            }
            if (!empty($histData)) {
                file_put_contents($verDir . DIRECTORY_SEPARATOR . "changes.json", $histData, LOCK_EX);  // write the history changes to the changes.json file
            }
            file_put_contents($verDir . DIRECTORY_SEPARATOR . "key.txt", $data->key, LOCK_EX);  // write the key value to the key.txt file

            $forcesavePath = getForcesavePath($newFileName, false);  // get the path to the forcesaved file version
            if ($forcesavePath != "") {  // if the forcesaved file version exists
                unlink($forcesavePath);  // remove it
            }

            $saved = 0;
        }

        $result["error"] = $saved;

        return $result;
    }

    public function GenerateRevisionId($expected_key)
    {
        if (strlen($expected_key) > 20) $expected_key = crc32($expected_key);  // if the expected key length is greater than 20, calculate the crc32 for it
        $key = preg_replace("[^0-9-.a-zA-Z_=]", "_", $expected_key);
        $key = substr($key, 0, min(array(strlen($key), 20)));  // the resulting key length is 20 or less
        return $key;
    }

    public function restoreVersion(Request $request)
    {
        $storagePath = getStoragePath($request->file);  // get the file path
        $histDir = getHistoryDir($storagePath);
        $curVersionDir = getVersionDir($histDir, $request->version);
        $this->copyr($curVersionDir, getVersionDir($histDir,getFileVersion($histDir)));

        $filetype = 'docx';
        $docKey = getDocEditorKey($request->file);
        $fileuri = FileUri($request->file, true);
        return getHistory($request->file, $filetype, $docKey, $fileuri);
    }

    protected function copyr($source, $dest)
    {
        // recursive function to copy
        // all subdirectories and contents:
        if(is_dir($source)) {
            $dir_handle=opendir($source);
            mkdir($dest, 0777, true);
            while($file=readdir($dir_handle)){
                if($file!="." && $file!=".."){
                    copy($source."/".$file, $dest."/".$file);
                }
            }
            closedir($dir_handle);
        } else {
            // can also handle simple copy commands
            copy($source, $dest);
        }
    }
}
