# genus
# subgenus
# specificEpithet
# infraspecificEpithet
SELECT DISTINCT
	`genus`,
	`subgenus`,
	`specific_epithet` `specificEpithet`,
	`infraspecific_epithet` `infraspecificEpithet`
FROM `scientific_name`
WHERE `genus` = :genus;
