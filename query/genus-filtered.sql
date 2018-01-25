# genus
SELECT DISTINCT `sn`.`genus`
FROM `scientific_name` `sn`
	LEFT JOIN `organism` `o` USING (`specimen_carrier_id`, `sequence_number`)
WHERE `o`.`higher_taxa` LIKE concat('% ', :taxon) or `o`.`higher_taxa` LIKE concat('% ', :taxon, ' %');
