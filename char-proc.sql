DELIMITER //

DROP PROCEDURE IF EXISTS get_products_by_characteristics//

-- +---------------------+--------------+------+-----+---------+-------+
-- | Field               | Type         | Null | Key | Default | Extra |
-- +---------------------+--------------+------+-----+---------+-------+
-- | id                  | varchar(12)  | NO   | PRI | NULL    |       |
-- | provider_id         | varchar(6)   | NO   |     | NULL    |       |
-- | category_id         | varchar(9)   | NO   |     | NULL    |       |
-- | title               | text         | NO   |     | NULL    |       |
-- | description         | text         | NO   |     | NULL    |       |
-- | vendor_code         | varchar(100) | NO   |     | NULL    |       |
-- | param_value_list    | text         | NO   |     | NULL    |       |
-- | param_variable_list | text         | NO   |     | NULL    |       |
-- | brand_id            | varchar(8)   | NO   |     | NULL    |       |
-- | color               | varchar(10)  | YES  |     | NULL    |       |
-- | size                | varchar(10)  | YES  |     | NULL    |       |
-- +---------------------+--------------+------+-----+---------+-------+

--  +-------------------+-------------+------+-----+---------+-------+
-- | Field             | Type        | Null | Key | Default | Extra |
-- +-------------------+-------------+------+-----+---------+-------+
-- | product_id        | varchar(20) | NO   | PRI | NULL    |       |
-- | characteristic_id | varchar(20) | NO   | PRI | NULL    |       |
-- | type              | int(11)     | NO   |     | NULL    |       |
-- | sort_order        | int(11)     | NO   |     | 0       |       |
-- | value             | varchar(20) | NO   | PRI | NULL    |       |
-- +-------------------+-------------+------+-----+---------+-------+

-- set @b = '{"000000001-000000006":["156","704"],"000000003-000000006":["000009","000010"],"000000014-000000006":["000000011","000000044"],"000000029-000000006":["6.3;6.6"]}';
-- SELECT JSON_EXTRACT(@b, '$.000000001-000000006[0]') c1;

-- SET @a = '{"new_settings": [{"setting_name": "test", "setting_value": "test_value"}]}';
-- SELECT JSON_EXTRACT(@a, '$.new_settings[0].setting_name') c1
-- SELECT JSON_EXTRACT(@a, '$.new_settings[0].setting_name') c1 , JSON_EXTRACT(@a, '$.new_settings[0].setting_value') c2;

CREATE PROCEDURE get_products_by_characteristics
(
)
BEGIN
	select * from product;
END//

DELIMITER ;
