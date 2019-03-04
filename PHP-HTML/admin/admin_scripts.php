<?php
include("../herbes.php");

function flush_buffers(){
    ob_end_flush();
    ob_flush();
    flush();
    ob_start();
}

function replacepage($name, $script, $description, $instcode, $charset, $lineendings, $con2) {
    echo "
    <html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">
    <head>
        <title> Virtuella herbariet update $name </title>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
    </head>
    <body>
    <div>
    <h2> Intruktioner för uppdatering av Specimen data $name</h2>

    <form enctype=\"multipart/form-data\" action=\"$script\" method=\"post\" accept-charset=\"utf-8\">

    <h3> 1. Ersätt fil </h3>";
        //error_reporting(E_ALL);
        //ini_set('display_errors', '1');
    //include("../herbes.php");
        //include("admin_scripts.php");
    //$con2 = conDatabase($MySQLHost, $MySQLDB, $MySQLSUser, $MySQLSPass);
    echo "
    <table>
        <tr> <th> </th> <th> ID </th> <th> Fil </th> <th> poster </th> <th> institution code </th> <th> collection code </th> <th> datum </th> </tr>";
    $query = "SELECT name, ID, date, inst, coll, nr_records FROM sfiles WHERE nr_records>0;";
    $result = $con2->query($query);
    if (!$result) {
        echo "$query <p>";
        echo mysql_error();
    }
    while($row = $result->fetch())
    {
        echo "
        <tr>
            <td> <input type= \"radio\" name=\"delfile_ID\" value =\"$row[ID]\"/> </td>
            <td> $row[ID] </td>
            <td> $row[name] </td>
            <td> $row[nr_records] </td>
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
    
    echo "
    <h3> $description <br />
    
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
                <option value=\"\\r\\n\">\\r\\n - DOS/Windows</option>
                <option value=\"\\r\">\\r - Mac</option>
                <option value=\"\\n\">\\n - Unix</option>
            </select> </td> </tr>
            
            <tr> <td> Password: </td> <td> <input type=\"password\" name =\"mypassword\" />
            <input type=\"hidden\" name =\"kontroll\" value = \"OK\" /> </td> </tr>
            
            <tr> <td> <input type=\"submit\" value=\"Upload File\" /> </td> </tr>
        </table>
    </form>
    <a href=\"admin.php\">admin page</a> <br />
    <a href=\"../\">start page</a>
    </div>
    </body>
    </html>";
}

function instable($con, $sfileName, $instCode, $collCode) {
    $query = "INSERT INTO sfiles (name, inst, coll) values ('$sfileName', '$instCode', '$collCode');";
    $result = $con->query($query);
    if (!$result) {
        echo "Query: $query </P>";
        echo mysql_error();
        return false;
    } else {
        $res = $con->query("SELECT LAST_INSERT_ID()");
        $ro = $res->fetch();
        return $ro[0];
    }
}

function instablenr($con, $sfileID) {
    $query = "SELECT count(*) from specimens WHERE sFile_ID = $sfileID Group by sFile_ID";
    $res = $con->query($query);
    //echo "Query: $query </p>";
    if (!$res) {
        echo "instablenr error Query: $query </p>";
        echo mysql_error();
        return false;
    } else {    
        $ro = $res->fetch();
        $nr = $ro[0];
        $query = "UPDATE sfiles set nr_records = $nr WHERE ID = $sfileID";
        $res2 = $con->query($query);
        //echo "Query: $query </p>";
         if (!$res2) {
            echo "error Query: $query </p>";
            echo mysql_error();
            return false;
        }
    }
}

function doreplace($con, $query, $sfileName, $file_ID) {
    $timer = new Timer();
    echo "
        <h3> 2. Inserting file in db</h3>
        inserting $sfileName in db <br />";
    ob_flush();
    flush();
    $stmt = $con->prepare($query);
    $stmt->execute();
    //$result = $con->query($query);
    //$stmt = $con->query('SHOW WARNINGS');
    //echo "Warnings<br/>";
    //var_dump($stmt->fetchAll());
    warningFormat($con,$sfileName);
    echo "<p/>";
    if($stmt->errorCode() == 0) {
        instablenr($con, $file_ID);
        echo "<br /> query: $query <br />  $sfileName now inserted in db. Time: ". $timer->getTime()."<br />";
       
        echo "
            <h3> 3. Deleting file </h3>";
            ob_flush();
            flush();
            delfile ($con);
        echo "
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
        echo($errors[2]).'<br />';
        echo "<br /> query: $query <br />";
    }
}

function emptycache() {
    $dir = 'C:\\inetpub\\wwwroot\\cache\\';
    $mydir = opendir($dir);
    while(false !== ($file = readdir($mydir))) {
        if($file != "." && $file != "..") {
            chmod($dir.$file, 0777);
            if(is_dir($dir.$file)) {
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

function delfile ($con) {
    $delfile_ID = $_POST['delfile_ID'];
    if ($delfile_ID!=-1) {
        echo "
            delete file $delfile_ID from db <br />";
        $query = "DELETE FROM specimens WHERE sFile_ID = '$delfile_ID'";
        $query2 = "update sfiles set nr_records = 0 where ID = $delfile_ID";
        //echo "$query <p>";
        $result = $con->query($query);
        if ($result) {
            $result2 = $con->query($query2);
            echo "$query2 <br />";
            if (!$result2) {
                echo "error updating sfiles table $query2 <br/>";
            }
            echo "
            $delfile_ID deleted <p />";
            return true;
        } else {
            echo "
                error when trying delete $delfile_ID. <br />
                error: ".mysql_error($con)."<br />
                query: $query <br />";
            return false;
        }
       

    } else {
        echo "no file to delete chosen <p />";
    }
}

function calcModified($con, $delfileDate) {
    $query = "UPDATE specimens SET lastModified = $delfileDate dff WHERE sfile = $file;"; // ej färdig
    $result = $con->query($query);
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

function fixGeoNames($con, $file_ID) {
    $con->query("Call fix_geonames_f($file_ID);");
    //mysql_query("Call UKProv();", $con);
    //mysql_query("Call fixProv();", $con);
}

function fixIdLinks($con, $file_ID, $timer) {
     // fixa lokaldatabasen  update specimen_locality join specimens set specimen_locality.specimen_ID = specimens.ID where specimens.AccessionNo = specimen_locality.AccessionNo and specimens.InstitutionCode = specimen_locality.InstitutionCode;
    
    if ($file_ID == "-1") {
        $query1 = "UPDATE specimens SET Genus_ID = Null;";
        $query2 = "UPDATE specimens join xgenera using (Genus) SET Genus_ID = xgenera.ID;";
        $query3 = "UPDATE specimens SET Taxon_ID = Null";
        $query4 = "UPDATE specimens join xnames using (Genus, Species, sspVarForm, HybridName) SET Taxon_ID = xnames.ID, Dyntaxa_ID = xnames.taxonid;";
        $query5 = "UPDATE specimens SET Geo_ID = Null;";
        //$query6 = "UPDATE specimens join district using (district, province, country) SET Geo_ID = district.ID;"; // Collate binnary
        $query6 = "UPDATE specimens join district on specimens.district=district.district and specimens.province = district.province and specimens.country = district.country  SET Geo_ID = district.ID";
        $query7 = "UPDATE specimens SET Sign_ID = Null;";
        $query8 = "UPDATE specimens join signaturer ON specimens.collector = signaturer.Signatur SET Sign_ID = signaturer.ID;";
        //$query9 = "UPDATE specimens SET Dyntaxa_ID = Null;";
        //$query10 = "UPDATE specimens join xnames using (Genus, Species, sspVarForm, HybridName) SET Dyntaxa_ID = xnames.taxonid;";
        $query11 ="UPDATE specimen_locality join specimens set specimen_locality.specimen_ID = specimens.ID WHERE specimens.AccessionNo = specimen_locality.AccessionNo and specimens.InstitutionCode = specimen_locality.InstitutionCode;";
    } else {
        $query1 = "UPDATE specimens SET Genus_ID = Null WHERE sFile_ID = '$file_ID';";
        $query2 = "UPDATE specimens join xgenera using (Genus) SET Genus_ID = xgenera.ID WHERE sFile_ID = '$file_ID';";
        $query3 = "UPDATE specimens SET Taxon_ID = Null WHERE sFile = '$file_ID';";
        $query4 = "UPDATE specimens join xnames using (Genus, Species, sspVarForm, HybridName) SET Taxon_ID = xnames.ID, Dyntaxa_ID = xnames.taxonid WHERE sFile_ID = '$file_ID';";
        $query5 = "UPDATE specimens SET Geo_ID = Null WHERE sFile = '$file_ID';";
        //$query6 = "UPDATE specimens join district using (district, province, country) SET Geo_ID = district.ID WHERE sFile_ID = '$file_ID';"; // Collate binnary
        $query6 = "UPDATE specimens join district on specimens.district=district.district and specimens.province = district.province and specimens.country = district.country SET Geo_ID = district.ID WHERE sFile_ID ='$file_ID'";
        $query7 = "UPDATE specimens SET Sign_ID = Null WHERE sFile_ID = '$file_ID';";
        $query8 = "UPDATE specimens join signaturer ON specimens.collector = signaturer.Signatur SET Sign_ID = signaturer.ID  WHERE sFile_ID = '$file_ID';";
        //$query9 = "UPDATE specimens SET Dyntaxa_ID = Null WHERE sFile_ID = '$file_ID';";
        //$query10 = "UPDATE specimens join xnames using (Genus, Species, sspVarForm, HybridName) SET Dyntaxa_ID = xnames.taxonid WHERE sFile_ID = '$file_ID';";
        $query11 ="UPDATE specimen_locality join specimens set specimen_locality.specimen_ID = specimens.ID WHERE specimens.AccessionNo = specimen_locality.AccessionNo and specimens.InstitutionCode = specimen_locality.InstitutionCode and sFile_ID = '$file_ID';";
        
        //fix specimen_locality link with ID
        // use something like update specimen_locality join specimens using (InstitutionCode, AccessionNo) set specimen_locality.specimen_ID = specimens.ID where sFile_ID = "627";
    }

    echo "
        creating Genus_ID.. <br />";
    $result = $con->query($query2);
    ob_flush();
    flush();
    if (!$result) {
        echo "
        error when creating Genus_ID:".mysql_error($con)." <br />
        query: $query2 <br />";
        
    }
    echo "Time: ".$timer->getTime()."<br />";
    echo "
        creating Taxon_ID.. <br />";
    $result = $con->query($query4);
    ob_flush();
    flush();
    if (!$result) {
        echo "
        error when creating Taxon_ID:".mysql_error($con)." <br />
        query: $query4 <br />";
        
    }
   echo "Time: ".$timer->getTime()."<br />";
    echo "
        creating Geo_ID.. <br />";
    $result = $con->query($query6);
    ob_flush();
    flush();
    if (!$result) {
        echo "
        error when creating Geo_ID:".mysql_error($con)." <br />
        query: $query6 <br />";
    }
   echo "Time: ".$timer->getTime()."<br />";
    echo "
        creating Sign_ID.. <br />";
    $result = $con->query($query8);
    ob_flush();
    flush();
    if (!$result) {
        echo "
        error when creating Sign_ID:".mysql_error($con)." <br />
        query: $query8 <br />";
    }
    echo "Time: ".$timer->getTime()."<br />";
  echo "
        creating specimen ID in the locality db.. <br />";
    $result = $con->query($query11);
    ob_flush();
    flush();
    if (!$result) {
        echo "
        error when creating specimen_ID:".mysql_error($con)." <br />
        query: $query11 <br />";
    }
    echo "Time: ".$timer->getTime()."<br />";
    echo "
    done fixing id links <br />";
}



function filetable($con2) {
    echo "
    <table>
        <tr> <th> </th> <th> ID </th> <th> Fil </th> <th> poster </th> <th> institution code </th> <th> collection code </th> <th> datum </th> </tr>";
    $query = "SELECT sfiles.name, sfiles.ID, sfiles.date, sfiles.inst, sfiles.coll, Count(*) as records FROM specimens join sfiles on specimens.sFile_ID = sfiles.ID GROUP BY sFile_ID;";
    $result = $con2->query($query);
    if (!$result) {
        echo mysql_error();
    }
    while($row = $result->fetch())
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

function upploadfile($backpage) {
   if ($_POST['kontroll'] != "OK") {
        echo "lyckas inte ladda upp filen, antagligen är det för att filen du försöker ladda upp är för stor <br />
            <a href=\"$backpage\">back to admin page</a> <p />";
        return false;
    } elseif ($_POST['mypassword'] != "baconas") {
       echo "wrong password <a href=\"$backpage\">försök igen?</a> <br />";
       return false;
    }
    elseif ($_FILES["uploadedfile"]["error"] > 0) {
        echo "error: " . $_FILES["uploadedfile"]["error"] . "<br />
        försök igen <a href=\"$backpage\">försök igen?</a> <p />";
        return false;
    } else
    {
        $uploaddir = 'C:/inetpub/wwwroot/uploads/';
        $uploadfile = $uploaddir . basename($_FILES['uploadedfile']['name']);
        $file = basename($_FILES['uploadedfile']['name']);
        //echo "file: $file <br />";
        $delfile_ID = $_POST['delfile_ID'];
        //echo "delfile: $delfile <br />";
        $collCode = $_POST['CollectionCode'];
        //echo "collCode: $collCode <br />";
        $instCode = $_POST['InstitutionCode'];
        //echo "instCode: $instCode <br />";
        $char_set = $_POST['char_set'];
        $line_endings = $_POST['line_endings'];
        if (substr($file,-4)!=".csv" and substr($file,-4)!=".txt") {
            echo "
                it should be a .csv or .txt file <br />
                <a href=\"$backpage\"> back to admin page </a> <p />";
            return false;
        }
        elseif (move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $uploadfile)) {
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

function warningFormat($con, $sfileName) {
    $stmt = $con->query('SHOW WARNINGS');
    $errors = $stmt->fetchAll();
    echo "Warnings <br/>
        <Table>
        <tr><th>Level</th><th>Message</th></tr>";
    foreach ($errors as $w) {
        echo "<tr><td>$w[Level]</td><td>$w[Message]</td></tr>";
    }
    echo "</Table>";
    $myfile = fopen("C:/inetpub/wwwroot/uploadlogs/$sfileName.txt", "w") or die("Unable to open file!");
    foreach ($errors as $w) {
        fwrite($myfile, "$w[Level]: $w[Message]\r\n");
    }
    fclose($myfile);    
}

/*
function insertFileUME($con, $uploadfile, $sfileName) {
    //echo "   Inserting file $uploadfile. <p />";
    
   
    $collCode = $_POST['CollectionCode'];
        //echo "collCode: $collCode <br />";
    $instCode = $_POST['InstitutionCode'];
        //echo "instCode: $instCode <br />";
    $char_set = $_POST['char_set'];
    $line_endings = $_POST['line_endings'];
    $File_id = instable($con, $sfileName, $instCode, $collCode);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    
    $query = "LOAD DATA INFILE '$uploadfile' INTO TABLE specimens CHARACTER SET $char_set FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '$line_endings'
            (`AccessionNo`, `Day`, `Month`, `Year`, `Genus`, `Species`, `SspVarForm`, `HybridName`, `collector`,
            `Collectornumber`, `Comments`, `Continent`, `Country`, `Province`, `District`, `Locality`, `Cultivated`,
            `Altitude_meter`, `Original_name`, `Original_text`, `Notes`, `Exsiccata`, `Exs_no`, `RUBIN`, `RiketsN`,
            `RiketsO`, `Lat_deg`, `Lat_min`, `Lat_sec`, `Lat_dir`, `Long_deg`, `Long_min`, `Long_sec`, `long_dir`, `LastModified`)
            SET `sFile_ID` = '$File_id', institutionCode = '$instCode', collectionCode = '$collCode'";
    echo " $query <p />";
    try {
        $result = $con->query($query);
         echo "
            $sfileName now inserted in db <br />";
        return $File_id ;
    } catch (PDOException $e) {
        echo 'error when trying to insert: ' . $e->getMessage();
        echo "<br /> query: $query <br />";
        return false;
    }
}

function insertFileLINREG($con, $uploadfile, $sfileName) {
    //echo "   Inserting file $uploadfile. <p />";

    $collCode = $_POST['CollectionCode'];
        //echo "collCode: $collCode <br />";
    $instCode = $_POST['InstitutionCode'];
        //echo "instCode: $instCode <br />";
    $char_set = $_POST['char_set'];
    $line_endings = $_POST['line_endings'];
    $File_id = instable($con, $sfileName, $instCode, $collCode);
    
    
    $query = "LOAD DATA INFILE '$uploadfile' INTO TABLE specimens CHARACTER SET $char_set FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '$line_endings'
            (`AccessionNo`, `Day`, `Month`, `Year`, `Genus`, `Species`, `SspVarForm`, `HybridName`, `collector`,
            `Collectornumber`, `Comments`, `Continent`, `Country`, `Province`, `District`, `Locality`, `Cultivated`,
            `Altitude_meter`, `Original_name`, `Original_text`, `Notes`, `Exsiccata`, `Exs_no`, `RUBIN`, `RiketsN`,
            `RiketsO`, `Lat_deg`, `Lat_min`, `Lat_sec`, `Lat_dir`, `Long_deg`, `Long_min`, `Long_sec`, `long_dir`, `LastModified`,
            `linereg`, `Type_status`, `Basionym`, `TAuctor`)
            SET `sFile_ID` = '$File_id', institutionCode = '$instCode', collectionCode = '$collCode'";
    //echo " $query <p />";
    $result = $con->query($query);
    if ($result) {
        echo "
            $sfileName now inserted in db <br />";
        return $File_id ;
    } else {
        echo "
            error when trying to insert $sfileName into the db. <br />
            error: ".mysql_error($con)."<br />
            query: $query <br />";
        return false;
    }
}

function insertFileOHN($con, $uploadfile, $sfileName) {
    //echo "   Inserting file $uploadfile. <p />";

    $collCode = $_POST['CollectionCode'];
        //echo "collCode: $collCode <br />";
    $instCode = $_POST['InstitutionCode'];
        //echo "instCode: $instCode <br />";
    $char_set = $_POST['char_set'];
    $line_endings = $_POST['line_endings'];
    $separated = $_POST['separated'];
    $enclosed = $_POST['enclosed'];
    $File_id = instable($con, $sfileName, $instCode, $collCode);
    
    
    $query = "LOAD DATA INFILE '$uploadfile' INTO TABLE specimens CHARACTER SET $char_set FIELDS TERMINATED BY '$separated' ENCLOSED BY '$enclosed' LINES TERMINATED BY '$line_endings'
            (`AccessionNo`, @scientific_name, @datum, @North, @East, @Presicion, @Koordsys, Continent,
            Country, `Province`, `District`, @OriginalLokal, `Collector`, `Original_name`, `Original_text`, @Que, @Notes)
            SET `sFile_ID` = $File_id, institutionCode = '$instCode', collectionCode = '$collCode',
                Year = PYear(@datum),
                Month = PMonth(@datum),
                Day = PDay(@datum),
                Genus = Genera(CONVERT(@scientific_name USING utf8)),
                Species = Species2(CONVERT(@scientific_name USING utf8)),
                SspVarForm = Ssp(CONVERT(@scientific_name USING utf8)),
                HybridName = OHNHybrid(CONVERT(@scientific_name USING utf8)),
                RiketsN = PRT90N(@North, @Koordsys, @Presicion),
                RiketsO = PRT90E(@East, @Koordsys, @Presicion),
                CSource = OHNCSource(@Koordsys, @Presicion),
                `Lat` = OHNLat(@North, @East, @Koordsys),
                `Long` = OHNLong(@East,  @North, @Koordsys),
                linereg = OHNLinreg(@Presicion, CONVERT(@Locality USING utf8)),
                Locality = OHNLocality(@Presicion, CONVERT(@Locality USING utf8)),
                Notes = OHNNotes(CONVERT(@Notes USING utf8))";
                
                //Country = PCountry(CONVERT(@SWCountry USING utf8)),
                //Continent = PContinent(CONVERT(@SWCountry USING utf8),Province),
                
    //echo " $query <p />";
    $result = $con->query($query);
    if ($result) {
        echo "
            $sfileName now inserted in db <br />";
        return $File_id ;
    } else {
        echo "
            error when trying to insert $sfileName into the db. <br />
            error: ".mysql_error($con)."<br />
            query: $query <br />";
        return false;
    }
}

function insertFileUPS($con, $uploadfile, $sfileName) {
    //echo "   Inserting file $uploadfile. <p />";
    
   
    $collCode = $_POST['CollectionCode'];
        //echo "collCode: $collCode <br />";
    $instCode = $_POST['InstitutionCode'];
        //echo "instCode: $instCode <br />";
    $char_set = $_POST['char_set'];
    $line_endings = $_POST['line_endings'];
    $File_id = instable($con, $sfileName, $instCode, $collCode);
    
    // 
    
   $query = "LOAD DATA INFILE '$uploadfile' INTO TABLE specimens CHARACTER SET $char_set FIELDS TERMINATED BY ',' ENCLOSED BY '\\\"' LINES TERMINATED BY '$line_endings'
        (`AccessionNo`, `Day`, `Month`, `Year`, `Genus`, @Species, @irang, @iepi, collector, Collectornumber, notes, continent, country, province, district, original_text,
        habitat, `Altitude_meter`,`Original_name`, `Exsiccata`, `Exs_no`, @Lat, @Long, @dumy, Type_status, Basionym) 
        SET `sFile_ID` = '$File_id', institutionCode = 'UPS', collectionCode = '',
            SspVarForm = concat(@irang, ' ', @iepi),
            CSource = CSource(@Lat, @Long),
            `Long` = ToNum(@Long),
            `Lat` = ToNum(@Lat),
            HybridName = UPSHybrid(@Species),
            Species = UPSSpecies(@Species)
            " ;
    //echo " $query <p />";
    //
    $result = $con->query($query);
    if ($result) {
        echo "
            $sfileName now inserted in db <br />";
        return $File_id ;
    } else {
        echo "
            error when trying to insert $sfileName into the db. <br />
            error: ".mysql_error($con)."<br />
            query: $query <br />";
        return false;
    }
}

function insertFileS($con, $uploadfile, $sfileName) {
    //echo "   Inserting file $uploadfile. <p />";

    $collCode = $_POST['CollectionCode'];
        //echo "collCode: $collCode <br />";
    $instCode = $_POST['InstitutionCode'];
        //echo "instCode: $instCode <br />";
    $char_set = $_POST['char_set'];
    $line_endings = $_POST['line_endings'];
    $File_id = instable($con, $sfileName, $instCode, $collCode);
    
    
   $query = "LOAD DATA INFILE '$uploadfile' INTO TABLE specimens CHARACTER SET $char_set FIELDS TERMINATED BY ',' ENCLOSED BY '\\\"' LINES TERMINATED BY '$line_endings' IGNORE 1 LINES 
    (`AccessionNo`, `Genus`, `Species`,  SspVarForm, Basionym, Type_status, collector, Collectornumber,
    `Exsiccata`, `Exs_no`, `Year`, `Month`, `Day`, @continent, country, province, district, `Original_text`, habitat,
    `Lat_deg`, `Lat_min`, `Lat_sec`, `Lat_dir`, `Long_deg`, `Long_min`, `Long_sec`, `long_dir`, `RUBIN`, RiketsN, RiketsO, @AltMin, @AltMax, comments)
    SET `sFile_ID` = '$File_id',
    institutionCode = '$instCode',
    CollectionCode = '$collCode',
    Altitude_meter = SAlt(@AltMin, @AltMax),
    continent = SContinent(@continent, @country)";
    echo " $query <p />";
    //
    $result = $con->query($query);
    if ($result) {
        echo "
            $sfileName now inserted in db <br />";
        return $File_id ;
    } else {
        echo "
            error when trying to insert $sfileName into the db. <br />
            error: ".mysql_error($con)."<br />
            query: $query <br />";
        return false;
    }
}

function insertFileKryptoS($con, $uploadfile, $sfileName) {
    //echo "   Inserting file $uploadfile. <p />";

    $collCode = $_POST['CollectionCode'];
        //echo "collCode: $collCode <br />";
    $instCode = $_POST['InstitutionCode'];
        //echo "instCode: $instCode <br />";
    $char_set = $_POST['char_set'];
    $line_endings = $_POST['line_endings'];
    $File_id = instable($con, $sfileName, $instCode, $collCode);
    
    
   $query = "LOAD DATA INFILE '$uploadfile' INTO TABLE specimens CHARACTER SET $char_set FIELDS TERMINATED BY ',' ENCLOSED BY '\\\"' LINES TERMINATED BY '$line_endings' IGNORE 1 LINES 
    (`AccessionNo`, @Taxon, Basionym, Type_status, collector, Collectornumber,
    `Exsiccata`, `Exs_no`, `Year`, `Month`, `Day`, @continent, country, province, district, `Original_text`, habitat,
    `Lat_deg`, `Lat_min`, `Lat_sec`, `Lat_dir`, `Long_deg`, `Long_min`, `Long_sec`, `long_dir`, comments)
    SET `sFile_ID` = '$File_id',
    institutionCode = '$instCode',
    CollectionCode = '$collCode',
    Genus = Genera(@Taxon),
    Species = Species2(@Taxon),
    SspVarForm = Ssp(@Taxon),
    continent = SContinent(@continent, @country)";
    echo " $query <p />";
    //
    $result = $con->query($query);
    if ($result) {
        echo "
            $sfileName now inserted in db <br />";
        return $File_id ;
    } else {
        echo "
            error when trying to insert $sfileName into the db. <br />
            error: ".mysql_error($con)."<br />
            query: $query <br />";
        return false;
    }
}

function insertFileIMG($con, $uploadfile, $sfileName) {
    //echo "   Inserting file $uploadfile. <p />";

    $collCode = $_POST['CollectionCode'];
        //echo "collCode: $collCode <br />";
    $instCode = $_POST['InstitutionCode'];
        //echo "instCode: $instCode <br />";
    $char_set = $_POST['char_set'];
    $line_endings = $_POST['line_endings'];
    $File_id = instable($con, $sfileName, $instCode, $collCode);
    
    
    $query = "LOAD DATA INFILE '$uploadfile' INTO TABLE specimens CHARACTER SET $char_set FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '$line_endings'
            (`AccessionNo`, `Day`, `Month`, `Year`, `Genus`, `Species`, `SspVarForm`, `HybridName`, `collector`,
            `Collectornumber`, `Comments`, `Continent`, `Country`, `Province`, `District`, `Locality`, `Cultivated`,
            `Altitude_meter`, `Original_name`, `Original_text`, `Notes`, `Exsiccata`, `Exs_no`, `RUBIN`, `RiketsN`,
            `RiketsO`, `Lat_deg`, `Lat_min`, `Lat_sec`, `Lat_dir`, `Long_deg`, `Long_min`, `Long_sec`, `long_dir`, `LastModified`,
            `linereg`, `Type_status`, `Basionym`, `TAuctor`, `image1`, `image2`, `image3`)
            SET `sFile_ID` = '$File_id', institutionCode = '$instCode', collectionCode = '$collCode'";
    //echo " $query <p />";
    $result = $con->query($query);
    if ($result) {
        echo "
            $sfileName now inserted in db <br />";
        return $File_id ;
    } else {
        echo "
            error when trying to insert $sfileName into the db. <br />
            error: ".mysql_error($con)."<br />
            query: $query <br />";
        return false;
    }
}*/

?>