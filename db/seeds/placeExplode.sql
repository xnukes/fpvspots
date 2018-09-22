CREATE TABLE `places` (
  `id` int(50) UNSIGNED NOT NULL,
  `user_id` int(50) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `description` text COLLATE utf8_czech_ci NOT NULL,
  `plus_desc` varchar(255) COLLATE utf8_czech_ci NOT NULL DEFAULT '',
  `minus_desc` varchar(255) COLLATE utf8_czech_ci NOT NULL DEFAULT '',
  `map_place` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `created_on` datetime NOT NULL,
  `changed_on` datetime NOT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `zoom` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;



UPDATE places
SET

latitude = SUBSTRING_INDEX(`map_place`, ';', 1),
longitude = SUBSTRING_INDEX(SUBSTRING_INDEX(`map_place`, ';', -2), ';', -2),
zoom = SUBSTRING_INDEX(SUBSTRING_INDEX(`map_place`, ';', -1), ';', -1)
