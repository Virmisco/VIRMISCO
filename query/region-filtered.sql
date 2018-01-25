# region
SELECT DISTINCT `g`.`location__region` `region`
FROM `photomicrograph` `p`
JOIN `organism` `o` ON `o`.`id` = `p`.`organism_id`
JOIN `specimen_carrier` `sc` ON `sc`.`id` = `o`.`specimen_carrier_id`
JOIN `gathering` `g` ON `g`.`id` = `sc`.`gathering_id`
WHERE `g`.`location__country` = :country AND `g`.`location__province` = :province
ORDER BY `g`.`location__region`;
