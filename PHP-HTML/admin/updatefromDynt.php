<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include("admin_scripts.php");

echo "Intruktioner. <br>
1. Gå till https://www.dyntaxa.se/ <br>
2. logga in <br>

Taxa till xnames
3. exportera: Databas. Taxon: Plantae, Fungi, Myxomycota, Algae, Chromista, Cyanobacteria
4. Öppna i excell
5. spara som unicode text
6. ändra caracter coding to utf-8

7. Drop table if exists dyntaxa_temp;
	
8.
CREATE TABLE `dtfungitaxa` (
  `TaxonId` int(11) DEFAULT NULL,
  `ScientificName` varchar(128) DEFAULT NULL,
  `Author` varchar(64) DEFAULT NULL,
  `CommonName` varchar(64) DEFAULT NULL,
  `TaxonCategory` varchar(32) DEFAULT NULL,
  `ConceptDefinition` text,
  `CreatedDate` datetime DEFAULT NULL,
  `ModifiedDate` datetime DEFAULT NULL,
  `ValidFromeDate` datetime DEFAULT NULL,
  `ValidToDate` datetime DEFAULT NULL,
  `IsValid` varchar(8) DEFAULT NULL,
  `xnamesOK` varchar(8) DEFAULT NULL,
  KEY `name` (`ScientificName`),
  KEY `TaxonID` (`TaxonId`)
) 

9.
LOAD DATA LOCAL INFILE 'c:/dyntaxa_database.txt' INTO TABLE dtfungitaxa Fields terminated by \"\\t\" IGNORE 1 LINES
    (TaxonId,ScientificName,Author,CommonName, TaxonCategory, @dummy, @dummy,@dummy,@dummy,
	ConceptDefinition, @Dummy, CreatedDate, ModifiedDate, ValidFromeDate, ValidToDate, @Dummy, @Dummy, @Dummy, @Dummy, IsValid)
	
$query2 = "delete from dtfungitaxa where TaxonID =0;";
	
10.  radera namn och felaktiga poster  ta bort namn med incerta sedis, ta bort namn med kryss istället för x
	
11. Updatera xnames synonymer? hantera skilnader? taxon is valid.





12. updatera svenska namn";
	



  
$query2 = "LOAD DATA LOCAL INFILE 'c:/dyntaxa_database.txt' INTO TABLE dyntaxa_temp Fields terminated by \"\\t\" IGNORE 1 LINES
    (TaxonId,ScientificName,Author,CommonName, TaxonCategory, @dummy, @dummy,@dummy,@dummy,
	ConceptDefinition, @Dummy, CreatedDate, ModifiedDate, ValidFromeDate, ValidToDate, @Dummy, @Dummy, @Dummy, @Dummy, IsValid);";
	
$query2 = "delete from dtfunginames where TaxonID =0;";
	
$query3 = "update dyntaxa_temp inner join xnames 
    on xnames.Scientific_name = dyntaxa_temp.ScientificName and xnames.Taxonid = dyntaxa_temp.taxonid and xnames.Auktor = dyntaxa_temp.author and dyntaxa_temp.TaxonCategory = xnames.TaxonTyp 
    set xnamesOK = \"OK\" where xnames.Scientific_Name is not null";
	
	
$query4 = "update dyntaxa_temp inner join xnames 
    on xnames.Scientific_name = dyntaxa_temp.ScientificName and xnames.Taxonid = dyntaxa_temp.taxonid and xnames.Auktor = dyntaxa_temp.author and dyntaxa_temp.TaxonCategory = xnames.TaxonTyp 
    set dcomments =  CONCAT (dcomments + \"OK Dyntaxa 2019-11-27\") where dyntaxa_temp.ScientificName is not null;";

	
$query5 = 	"insert into xnames (Taxonid, Taxontyp, Scientific_name, Auktor, Svenskt_namn, Genus, Species, rekomenderat) 
    select TaxonId, TaxonCategory, ScientificName, Author, CommonName, SUBSTRING_INDEX(ScientificName,' ',1), SUBSTRING_INDEX(ScientificName,' ',-1), "Ja"
    from dyntaxa_temp where Taxonid ="6011464"";
	
insert into xnames (Taxonid, Taxontyp, Scientific_name, Auktor, Svenskt_namn, Genus, Species, rekomenderat) 
    select TaxonId, TaxonCategory, ScientificName, Author, CommonName, LEFT(SUBSTRING_INDEX(ScientificName,' ',1), 32), LEFT(SUBSTRING_INDEX(ScientificName,' ',-1), 32), "Ja"
    from dyntaxa_temp where xnamesOK is null;
	
select * from xnames group by Scientific_name having count(Genus)>1;


Updatera taxonNames:

7.
save as unicode text from excell

6. ändra caracter coding till utf-8

Drop table if exists dtfunginames;




CREATE TABLE `dtfunginames` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `TaxonID` int(11) DEFAULT NULL,
  `TaxonName` varchar(256) DEFAULT NULL,
  `TaxonNameId` varchar(45) DEFAULT NULL,
  `Name` varchar(128) CHARACTER SET utf8 COLLATE utf8_swedish_ci DEFAULT NULL,
  `Author` varchar(128) DEFAULT NULL,
  `NameCategory` varchar(64) DEFAULT NULL,
  `IsRecommended` varchar(6) DEFAULT NULL,
  `Description` text CHARACTER SET utf8 COLLATE utf8_swedish_ci,
  `NameUsage` varchar(45) DEFAULT NULL,
  `NameStatus` varchar(45) DEFAULT NULL,
  `ISOKForObsSystems` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Name` (`Name`),
  KEY `TaxonID` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=131072 DEFAULT CHARSET=utf8





LOAD DATA LOCAL INFILE 'c:/DTFungiNames.txt' INTO TABLE DTFungiNames Fields terminated by "\t" IGNORE 1 LINES
    (TaxonId, TaxonName, TaxonNameID, Name, Author, NameCategory, @Dummy, IsRecommended, @Dummy, Description, @Dummy, NameUsage, @Dummy, NameStatus, ISOKForObsSystems, @Dummy, @Dummy, @Dummy, @Dummy, @Dummy, @Dummy, @Dummy, @Dummy);
	
delete from dtfunginames where TaxonID =0;

/*radera namn och felaktiga poster  ta bort namn med incerta sedis, ta bort namn med kryss istället för x*/

delete from dtfunginames where name like "%incertae sedis%";
delete from dtfunginames where name like "%×%";

/* uptaerar info om rekomenderade namn ska inte finnas dubletter i dtfungi
updaterar inte ej rekomenderade namn, lägger inte till nya poster */

update xnames inner join dtfunginames 
    on xnames.Scientific_name = dtfunginames.Name and xnames.Taxonid = dtfunginames.taxonid  
    set xnames.auktor = dtfunginames.Author,
        xnames.rekomenderat = "Ja",
        xnames.mark = "rekomendrat dtfungi 2019-11-27"
    where dtfunginames.ID is not null and NameCategory = "Vetenskapligt" and isrecommended = "True";
	
update xnames inner join dtfunginames 
    on xnames.Scientific_name = dtfunginames.Name and xnames.Taxonid = dtfunginames.taxonid  
    set xnames.auktor = dtfunginames.Author,
        xnames.rekomenderat = "Nej",
        xnames.mark = "OKforObssys+nstatus korr dtfungi 2019-11-27"
    where dtfunginames.ID is not null and NameCategory = "Vetenskapligt" and xnames.mark is null and isrecommended = "False" and namestatus = "korrekt" and IsOKforObssystems = "true";

insert into xnames (Taxonid, Scientific_name, Auktor, Genus, Species, SspVarForm, HybridName, rekomenderat) 
    select dtfunginames.TaxonId, Name, Author, DGenus(Name), DSpecies(Name), DSspVarForm(Name), DHybridName(Name), "Ja"
    from xnames right join dtfunginames 
    on xnames.Scientific_name = dtfunginames.Name
    where xnames.id is null and dtfunginames.NameCategory = "Vetenskapligt" and isRecommended = "True";
	
/*updatera svenska namn i xnames*/
update xnames inner join dtfunginames
    on dtfunginames.TaxonId = xnames.TaxonId
    set xnames.svenskt_namn = dtfunginames.name
    where dtfunginames.NameCategory = "Svenskt" and isrecommended = "True" and rekomenderat ="Ja";

    
/*Updatera taxonNames till xsvenska namn*/
	
insert into xsvenska_namn (Taxonid, Svenskt_namn, rekomenderat, Description) select DTFungiNames.TaxonID, DTFungiNames.Name, DTFungiNames.IsRecommended, DTFungiNames.Description from DTFungiNames Left join xsvenska_namn 
    on DTFungiNames.name = xsvenska_namn.Svenskt_namn and DTFungiNames.TaxonID = xsvenska_namn.TaxonID 
    where NameCategory = "Svenskt" and xsvenska_namn.ID is null;
		
update xsvenska_namn inner join DTFUngiNames on xsvenska_namn.TaxonID = DTFungiNames.taxonID and xsvenska_namn.Svenskt_namn = DTFungiNames.name set xsvenska_namn.rekomenderat = DTFungiNames.isrecommended, xsvenska_namn.Description = dtfungiNames.Description;	


Updatera taxonNames till xnames
Vetenskapliga namn:





8. Updatera xgenera
 exporter rak taxonlista
 urval av taxa
 kategori Släkte, familj, orgning, klass
 Taxonkategori: Släkte, familj, Ordning, klass, stam, rike
 namn: Vetenskapligt
 Auktor, Taxonkategori, TaxonId
 Öpna i excell
 5. spara som unicode text
 byt till Utf8
 
 
 
 Drop table if exists dtFungiGenera;
 
 delimiter $$

CREATE TABLE `dtfungigenera` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `TaxonID` int(11) DEFAULT NULL,
  `Taxonkategori` varchar(45) DEFAULT NULL,
  `Auktor` varchar(64) DEFAULT NULL,
  `Klass` varchar(64) DEFAULT NULL,
  `Ordning` varchar(64) DEFAULT NULL,
  `Familj` varchar(64) DEFAULT NULL,
  `Släkte` varchar(128) DEFAULT NULL,
  `Vetenskapligt` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `taxonID` (`TaxonID`),
  KEY `sn` (`Vetenskapligt`),
  KEY `släkte` (`Släkte`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8$$


LOAD DATA LOCAL INFILE 'c:/DTFungiGenera.txt' INTO TABLE DTFungiGenera Fields terminated by "\t" IGNORE 1 LINES
    (TaxonId, Taxonkategori, Auktor, Klass, Ordning, Familj, Släkte, Vetenskapligt);
	
	
update dtfungigenera set Familj = null where Familj like "%genera incertae sedis";

update dtfungigenera set Ordning = null where Ordning like "%families incertae sedis";

update dtfungigenera set Klass = null where Klass like "%ordines incertae sedis";
	
select * from xgenera inner join dtfungiGenera on xgenera.Genus = dtfungigenera.släkte where not xgenera.family = dtfungigenera.familj;
 
select * from xgenera right join dtfungiGenera on xgenera.Genus = dtfungigenera.släkte where xgenera.ID is null and not Släkte = '';


insert into xgenera (Genus, Family, `Order`, Class, TaxonLevel, Dyntaxa2019)
    select DtFungiGenera.Släkte, Familj, Ordning, Klass, Taxonkategori, "ny"
    from xgenera right join dtfungiGenera on xgenera.Genus = dtfungigenera.släkte where xgenera.ID is null and not Släkte = '';

?>