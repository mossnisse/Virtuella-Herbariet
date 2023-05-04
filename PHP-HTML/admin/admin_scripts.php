<?php
//include "../ini.php";
include "../herbes.php";

function flush_buffers(): void {
    ob_end_flush();
    ob_flush();
    flush();
    ob_start();
}

function replacepage(string $name, string $script, string $description, string $instcode, string $charset, string $lineendings, PDO $con2) : void {
    echo "
    <html>
    <head>
        <title>Virtuella herbariet update $name</title>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
    </head>
    <body>
    <div>
    <h2> Intruktioner för uppdatering av Specimen data $name</h2>
    <form enctype=\"multipart/form-data\" action=\"$script\" method=\"post\" accept-charset=\"utf-8\">

    <h3> 1. Ersätt fil </h3>
    <table>
        <tr> <th> </th> <th> ID </th> <th> Fil </th> <th> poster </th> <th> institution code </th> <th> collection code </th> <th> datum </th> </tr>";
    $query = "SELECT name, ID, date, inst, coll, nr_records FROM sfiles WHERE nr_records>0;";
    $result = $con2->query($query);
    if (!$result) {
        echo "$query <p>";
        echo mysql_error();
    }
    while ($row = $result->fetch())
    {
        echo "
        <tr>
            <td><input type= \"radio\" name=\"delfile_ID\" value =\"$row[ID]\"/></td>
            <td>$row[ID]</td>
            <td>$row[name]</td>
            <td>$row[nr_records]</td>
            <td>$row[inst]</td>
            <td>$row[coll]</td>
            <td>$row[date]</td>
        </tr>";
    }
    echo "
        <tr>
            <td><input type=\"radio\" name=\"delfile_ID\" value =\"-1\" checked=\"checked\" /></td>
            <td>None (Inserting new file in db)</td>
            <td>0</td>
            <td></td>
        </tr>
    </table>
    <h3>$description</h3>
    
    <h3> 3. Importera .csv filen till MySQL </h3>
        <input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"1000000000\" />
        <table>
            <tr> <td> Choose a file to upload: </td> <td> <input name=\"uploadedfile\" type=\"file\" /> </td> </tr>
            <tr> <td> InstitutionCode: </td> <td> <input type=\"text\" name =\"InstitutionCode\" value =\"$instcode\"/> </td> </tr>
            <tr> <td> CollectionCode: </td> <td> <input type=\"text\" name =\"CollectionCode\" /> </td> </tr>
            <tr> <td> Character set: </td> <td> <select name=\"char_set\" size=\"1\">
                $charset
                <option value=\"utf8\">UTF-8 Unicode</option>
                <option value=\"latin1\">latin1 - cp1252 West European</option>
                <option value=\"latin2\">latin2 - ISO 8859-2 Central European</option>
                <option value=\"ascii\">US ASCII</option>
                <option value=\"big5\">Big5 Traditional Chinese</option>
                <option value=\"dec8\">dec8 - DEC West European</option>
                <option value=\"cp850\">cp850 - DOS West European</option>
                <option value=\"hp8\">hp8 - HP West European</option>
                <option value=\"swe7\">7bit Swedish</option>
                <option value=\"ujis\">ujis - EUC-JP Japanese </option>
                <option value=\"sjis\">sjis - Shift-JIS Japanese </option>
                <option value=\"hebrew\">ISO 8859-8 Hebrew</option>
                <option value=\"tis620\">TIS620 Thai</option>
                <option value=\"euckr\">EUC-KR Korean</option>
                <option value=\"koi8u\">KOI8-U Ukrainian </option>
                <option value=\"gb2312\">GB2312 Simplified Chinese</option>
                <option value=\"greek\">ISO 8859-7 Greek</option>
                <option value=\"cp1250\">Windows Central European</option>
                <option value=\"gbk\">GBK Simplified Chinese</option>
                <option value=\"latin5\">latin5 - ISO 8859-9 Turkish</option>
                <option value=\"armscii8\">ARMSCII-8 Armenian</option>
                <option value=\"ucs2\">UCS-2 Unicode</option>
                <option value=\"cp866\">cp866 - DOS Russian</option>
                <option value=\"keybcs2\">keybcs2 - DOS Kamenicky Czech-Slovak</option>
                <option value=\"macce\">macce - Mac Central European</option>
                <option value=\"macroman\">macroman - Mac West European</option>
                <option value=\"cp852\">cp852 - DOS Central European</option>
                <option value=\"latin7\">latin7 - ISO 8859-13 Baltic</option>
                <option value=\"cp1251\">cp1251 - Windows Cyrillic</option>
                <option value=\"cp1256\">cp1256 - Windows Arabic</option>
                <option value=\"cp1257\">cp1257 - Windows Baltic</option>
                <option value=\"geostd8\">GEOSTD8 Georgian</option>
                <option value=\"cp932\">cp932 - SJIS for Windows Japanese</option>
                <option value=\"eucjpms\">eucjpms - UJIS for Windows Japanese</option>
            </select> </td> </tr>
            <tr> <td> Line endings: </td> <td> <select name=\"line_endings\" size=\"1\">
                $lineendings
                <option value=\"\r\n\">\\r\\n - DOS/Windows</option>
                <option value=\"&#x000D;\">\\r - Mac</option>
                <option value=\"\n\">\\n - Unix</option>
            </select> </td> </tr>
            
            <tr> <td>Password:</td> <td><input type=\"password\" name =\"mypassword\" />
            <input type=\"hidden\" name =\"kontroll\" value = \"OK\" /></td> </tr>
            <tr> <td><input type=\"submit\" value=\"Upload File\" /></td> </tr>
        </table>
    </form>
    <a href=\"admin.php\">admin page</a> <br />
    <a href=\"../\">start page</a>
    </div>
    </body>
    </html>";
}

function instable(PDO $con, string $sfileName, string $instCode, string $collCode) : int {
    $query = "INSERT INTO sfiles (name, inst, coll) values (:sfileName, :instCode, :collCode);";
    $stmt = $con->prepare($query);
    $stmt->BindValue(':sfileName', $sfileName, PDO::PARAM_STR);
    $stmt->BindValue(':instCode', $instCode, PDO::PARAM_STR);
    $stmt->BindValue(':collCode', $collCode, PDO::PARAM_STR);
    $stmt->execute();
    $stmt->closeCursor();
    $res = $con->query("SELECT LAST_INSERT_ID()");
    $ro = $res->fetch();
    return $ro[0];
}

function instablenr(PDO $con, int $sfileID) : void {
    $query = "SELECT count(*) as nr from specimens WHERE sFile_ID = :sfileID Group by sFile_ID;";
    $stmtin = $con->prepare($query);
    $stmtin->BindValue(':sfileID', $sfileID, PDO::PARAM_STR);
    $stmtin->execute();
    $ro = $stmtin->fetch(PDO::FETCH_ASSOC);
    $stmtin->closeCursor();
    $nr = $ro['nr'];
    $query = "UPDATE sfiles set nr_records = :nr WHERE ID = :sfileID";
    $stmtin2 = $con->prepare($query);
    $stmtin2->BindValue(':nr', $nr, PDO::PARAM_INT);
    $stmtin2->BindValue(':sfileID', $sfileID, PDO::PARAM_INT);
    $stmtin2->execute();
    $stmtin2->closeCursor();
}

function doreplace(PDO $con, string $query, string $sfileName, int $file_ID, string $uploadfile, string $char_set, string $line_endings, string $instCode, string $collCode): void {
    echo "<br /> query: $query <br />";
    $timer = new Timer();
   
    echo "
        <h3> 2. Inserting file in db</h3>
        inserting $sfileName ID: $file_ID in db <br />";
    ob_flush();
    flush();
    $stmt = $con->prepare($query);
    $stmt->BindValue(':uploadfile', $uploadfile, PDO::PARAM_STR);
    echo ":uploadfile', $uploadfile <br>";
    $stmt->BindValue(':char_set',  $char_set, PDO::PARAM_STR);
    echo "':char_set',  $char_set<br>";
    $stmt->BindValue(':fileID', $file_ID, PDO::PARAM_INT);
    echo "':fileID', $file_ID<br>";
    $stmt->BindValue(':instCode', $instCode, PDO::PARAM_STR);
    echo "':instCode', $instCode<br>";
    $stmt->BindValue(':collCode', $collCode, PDO::PARAM_STR);
    echo "':collCode', $collCode<br>";
    $stmt->BindValue(':line_endings', $line_endings, PDO::PARAM_STR);
    echo "':line_endings', $line_endings<p>";
    
    $stmt->execute();
    //warningFormat($con,$sfileName);
    
    echo "<p/>";
    if ($stmt->errorCode() == 0) {
        $stmt->closeCursor();
        instablenr($con, $file_ID);
        echo "<br /> query: $query <br />  $sfileName now inserted in db. Time: ". $timer->getTime()."<br />";
        echo "
            <h3> 3. Deleting file </h3>";
            ob_flush();
            flush();
            $delfile_ID = $_POST['delfile_ID'];
            delfile($con, $delfile_ID, $sfileName);
        echo "
            done. Time: " .$timer->getTime() ."
            <h3> 4. Changing name for geografical regions</h3>";
            ob_flush();
            flush();
            fixGeoNames($con, $file_ID);
        echo "
            done. Time: " .$timer->getTime() ."
            <h3> 5. Fixing relations for faster access to the db</h3>";
            ob_flush();
            flush();
            fixIdLinks($con, $file_ID, $timer);
        echo "
            done. Time: " .$timer->getTime() ."
            <h3> 6. Calculating coordinates in wgs84 </h3>";
            ob_flush();
            flush();
            CalcCoordBatchM($con, $timer, $file_ID);
        echo "
            done. Time: " .$timer->getTime() ."
            <h3> 7. Empty cache </h3>";
            ob_flush();
            flush();
            emptycache();
        echo "
        done. Time: " .$timer->getTime() ."<p>";
    } else {
        echo 'error when trying to insert file:';
        $errors = $stmt->errorInfo();
        $stmt->closeCursor();
        echo($errors[2]).'<br />';
        echo "<br /> query: $query <br />";
    }
}

function emptycache() : void {
    $dir = 'c:\\Apache24\\htdocs\\cache\\';
    $mydir = opendir($dir);
    while (false !== ($file = readdir($mydir))) {
        if ($file != "." && $file != "..") {
            chmod($dir.$file, 0777);
            if (is_dir($dir.$file)) {
                chdir('.');
                destroy($dir.$file.'/');
                rmdir($dir.$file) or DIE("couldn't delete $dir$file<br />");
            }
            else
                unlink($dir.$file) or DIE("couldn't delete $dir$file<br />");
        }
    }
    closedir($mydir);
}

function delfile(PDO $con, int $delfile_ID, string $sfileName) {
    echo "delfile ID: $delfile_ID <br>";
    if ($delfile_ID == -2) {
        $delIDQuery = "SELECT ID FROM sfiles where name = :fileName;";
        $Stm = $con->prepare($query);
        $Stm->bindValue(':filename',$sfileName, PDO::PARAM_STR);
        $Stm->execute();
        $IDresult = $Stm->fetch(PDO::FETCH_ASSOC);
        if (!$IDresult) {
            $delfile_ID = -1;
            echo "no old file with that name not deleting old records";
        }
        $delfileIDA = $Stm->fetch();
        $delfile_ID = $delfileIDA[0];
        echo "updal del id: $delfile_ID";
    } 
     if ($delfile_ID!=-1) {
        echo "
            delete file $delfile_ID from db <br />";
        $query = "DELETE FROM specimens WHERE sFile_ID = :delfile_ID";
        $Stm = $con->prepare($query);
        $Stm->bindValue(':delfile_ID',$delfile_ID, PDO::PARAM_INT);
        $Stm->execute();
        if ($Stm) {
            //$result = $Stm->fetch(PDO::FETCH_ASSOC);
            $query = "update sfiles set nr_records = 0 where ID = :delfile_ID";
            $Stm2 = $con->prepare($query);
            $Stm2->bindValue(':delfile_ID',$delfile_ID, PDO::PARAM_INT);
            $Stm2->execute();
            $Stm2->fetch(PDO::FETCH_ASSOC);
            //echo "$query2 <br />";
            if (!$Stm2) {
                echo "error updating sfiles table $query <br/>";
            }
            echo "
            $delfile_ID deleted <p />";
            return true;
        } else {
            echo "
                error when trying delete $delfile_ID. <br />
                query: $query <br />";
            return false;
        }
    } else {
        echo "no file to delete chosen <p />";
    }
}

function calcModified(PDO $con, $delfileDate): bool {
    $query = "UPDATE specimens SET lastModified = :delfileDate dff WHERE sfile = :file;"; // ej färdig
    $Stm = $con->prepare($query);
    $Stm->bindValue(':delfileDate',$file_ID, PDO::PARAM_STR);
    $Stm->bindValue(':file',$file_ID, PDO::PARAM_STR);
    $Stm->execute();
    $result = $Stm->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        echo "
            last Modified now fixed for $file <br />";
        return true;
    } else {
        echo "
            error when trying to calculate Last Modified for $file. <br />
            error: ".mysql_error($con)."<br />
            query: $query <br />";
        return false;
    }
}

function fixGeoNames(PDO $con, int $file_ID): void {
    $Stm = $con->prepare("Call fix_geonames_f(:file_ID);");
    $Stm->bindValue(':file_ID',$file_ID, PDO::PARAM_INT);
    $Stm->execute();
}

function fixIdLinks(PDO $con, int $file_ID, $timer) {
    if ($file_ID == "-1") {
        $UGenusQuery = "UPDATE specimens join xgenera using (Genus) SET Genus_ID = xgenera.ID;";
        $UnameQuery = "UPDATE specimens join xnames using (Genus, Species, sspVarForm, HybridName) SET Taxon_ID = xnames.ID, Dyntaxa_ID = xnames.taxonid;";
        $UDistrictQuery = "UPDATE specimens join district on specimens.district=district.district and specimens.province = district.province and specimens.country = district.country  SET Geo_ID = district.ID";
        $USignaturQuery = "UPDATE specimens join signaturer ON specimens.collector = signaturer.Signatur SET Sign_ID = signaturer.ID;";
        $USpecimenLocQuery ="UPDATE specimen_locality join specimens ON specimens.AccessionNo = specimen_locality.AccessionNo and specimens.InstitutionCode = specimen_locality.InstitutionCode SET specimen_locality.specimen_ID = specimens.ID";        
    } else {
        $UGenusQuery = "UPDATE specimens join xgenera using (Genus) SET Genus_ID = xgenera.ID WHERE sFile_ID = :file_ID;";
        $UnameQuery = "UPDATE specimens join xnames using (Genus, Species, sspVarForm, HybridName) SET Taxon_ID = xnames.ID, Dyntaxa_ID = xnames.taxonid WHERE sFile_ID = :file_ID;";
        $UDistrictQuery = "UPDATE specimens join district on specimens.district=district.district and specimens.province = district.province and specimens.country = district.country SET Geo_ID = district.ID WHERE sFile_ID = :file_ID";
        $USignaturQuery  = "UPDATE specimens join signaturer ON specimens.collector = signaturer.Signatur SET Sign_ID = signaturer.ID  WHERE sFile_ID = :file_ID;";
        $USpecimenLocQuery = "UPDATE specimen_locality join specimens ON specimens.AccessionNo = specimen_locality.AccessionNo and specimens.InstitutionCode = specimen_locality.InstitutionCode SET specimen_locality.specimen_ID = specimens.ID WHERE sFile_ID = :file_ID;";        
    }
     
    $UGenusStm = $con->prepare($UGenusQuery);
    $UnameStm = $con->prepare($UnameQuery);
    $UDistrictStm = $con->prepare($UDistrictQuery);
    $USignaturStm = $con->prepare($USignaturQuery);
    $USpecimenLocStm = $con->prepare($USpecimenLocQuery);
    
    if ($file_ID != "-1") {
        $UGenusStm->bindValue(':file_ID',$file_ID, PDO::PARAM_INT);
        $UnameStm->bindValue(':file_ID',$file_ID, PDO::PARAM_INT);
        $UDistrictStm->bindValue(':file_ID',$file_ID, PDO::PARAM_INT);
        $USignaturStm->bindValue(':file_ID',$file_ID, PDO::PARAM_INT);
        $USpecimenLocStm->bindValue(':file_ID',$file_ID, PDO::PARAM_INT);
    }
        
    echo "
        creating Genus_ID.. <br />";
    $UGenusStm->execute();
    echo "Time: ".$timer->getTime()."<br />";
    ob_flush();
    flush();
    
    echo "
        creating Taxon_ID.. <br />";
    $UnameStm->execute();
    echo "Time: ".$timer->getTime()."<br />";
    ob_flush();
    flush();
    
    echo "
        creating Geo_ID.. <br />";
    $UDistrictStm->execute();
    echo "Time: ".$timer->getTime()."<br />";
    ob_flush();
    flush();
    echo "
        creating Sign_ID.. <br />";
    $USignaturStm->execute();
    echo "Time: ".$timer->getTime()."<br />";
    ob_flush();
    flush();
    
    echo "
        creating specimen ID in the locality db.. <br />";
    $USpecimenLocStm->execute();
    echo "Time: ".$timer->getTime()."<br />";
    ob_flush();
    flush();
    
    echo "
    done fixing id links <br />";
}

function filetable(PDO $con2): void {
    echo "
    <table>
        <tr> <th> </th> <th> ID </th> <th> Fil </th> <th> poster </th> <th> institution code </th> <th> collection code </th> <th> datum </th> </tr>";
    $query = "SELECT sfiles.name, sfiles.ID, sfiles.date, sfiles.inst, sfiles.coll, sfiles.nr_records as records FROM specimens join sfiles on specimens.sFile_ID = sfiles.ID GROUP BY sFile_ID;";
    $result = $con2->query($query);
    if (!$result) {
        echo mysql_error();
    }
    while ($row = $result->fetch())
    {
        echo "
        <tr>
            <td> <input type= \"radio\" name=\"delfile_ID\" value =\"$row[ID]\"/> </td>
            <td> $row[ID] </td>
            <td> $row[name] </td>
            <td> $row[records] </td>
            <td> $row[inst] </td>
            <td> $row[coll] </td>
            <td> $row[date] </td>
        </tr>";
    }
    echo "
        <tr>
            <td> <input type=\"radio\" name=\"delfile_ID\" value =\"-1\" checked=\"checked\" /> </td>
            <td> None (Inserting new file in db)</td>
            <td> 0 </td>
            <td> </td>
        </tr>
    </table>";
}

function upploadfile(string $backpage) {
    global $APass;
    if ($_POST['kontroll'] != "OK") {
        echo "lyckas inte ladda upp filen, antagligen är det för att filen du försöker ladda upp är för stor <br />
            <a href=\"$backpage\">back to admin page</a> <p />";
        return false;
    } elseif ($_POST['mypassword'] != $APass) {
       echo "wrong password <a href=\"$backpage\">försök igen?</a> <br />";
       return false;
    }
    elseif ($_FILES["uploadedfile"]["error"] > 0) {
        echo "error: " . $_FILES["uploadedfile"]["error"] . "<br />
        försök igen <a href=\"$backpage\">försök igen?</a> <p />";
        return false;
    } else
    {
        $uploaddir = 'C:/uploads/';
        $uploadfile = $uploaddir . basename($_FILES['uploadedfile']['name']);
        $file = basename($_FILES['uploadedfile']['name']);
        if (substr($file,-4)!=".csv" && substr($file,-4)!=".txt") {
            echo "
                it should be a .csv or .txt file <br />
                <a href=\"$backpage\"> back to admin page </a> <p />";
            return false;
        }
        elseif (move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $uploadfile)) {
            $temp_file = $_FILES['uploadedfile']['tmp_name'];
            echo "tempfile: $temp_file </br>";
            //echo phpinfo();
            echo "
                    file $file of ". round(($_FILES["uploadedfile"]["size"] / 1024)) . " Kb is uploaded <br />";
                    $fd[0]=$file;
                    $fd[1]=$uploadfile;
                    //echo "<p/>file: $file uploadfile: $uploadfile";
                    return $fd;
        } else {
            echo "
                Possible file upload attack! <br />";
            return false;
        }
    }
}

function warningFormat(PDO $con, string $sfileName) {
    $stmt = $con->query('SHOW WARNINGS');
    //echo "stmt" + $stmt;
    if ($stmt) { 
        $errors = $stmt->fetchAll();
        echo "Warnings <br/>
            <Table>
            <tr><th>Level</th><th>Message</th></tr>";
        foreach ($errors as $w) {
            echo "<tr><td>$w[Level]</td><td>$w[Message]</td></tr>";
        }
        echo "</Table>";
        $myfile = fopen("C:/Apache24/htdocs/uploadlogs/$sfileName.txt", "w") or die("Unable to open file!");
        foreach ($errors as $w) {
            fwrite($myfile, "$w[Level]: $w[Message]\r\n");
        }
        fclose($myfile);
    } else {
        echo "no warnings?";
    }
}
?>