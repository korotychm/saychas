-- /** Number 2 */
select pr.*, ss.id as store_id, ss.title as store_title from product pr left join (select s.* from store s left join provider p on p.id = s.provider_id where s.id in ('000000001','000000002','000000003','000000004','000000005') ) ss on ss.provider_id=pr.provider_id where ss.id is not null order by pr.id;
SELECT `pr`.* FROM `product` AS `pr` LEFT JOIN (SELECT `s`.* FROM `store` AS `s` LEFT JOIN `provider` AS `p` ON `p`.`id` = `s`.`provider_id` WHERE `s`.`id` IN ('000000003', '000000001')) AS `ss` ON `ss`.`provider_id` = `pr`.`provider_id`
-- /** End of number 2 */

-- selects products that belong to specified providers
-- /** Number 1 */
select pr.* from product pr where pr.provider_id in (select s.provider_id from store s left join provider p on p.id = s.provider_id where s.id in ('000000003', '000000001') );
SELECT `pr`.* FROM `product` AS `pr` WHERE `pr`.`provider_id` IN (SELECT `s`.`provider_id` AS `provider_id` FROM `store` AS `s` LEFT JOIN `provider` AS `p` ON `p`.`id` = `s`.`provider_id` WHERE `s`.`id` IN ('000000003', '000000001'))
SELECT `s`.*, `p`.`id` AS `id` FROM `store` AS `s` LEFT JOIN `provider` AS `p` ON `p`.`id` = `s`.`provider_id`;
-- /** End of number 1 */

select s.provider_id from store s left join provider p on p.id = s.provider_id where s.id in ('000000003', '000000001')
SELECT `s`.`provider_id` FROM `store` AS `s` LEFT JOIN `provider` AS `p` ON `p`.`id` = `s`.`provider_id` WHERE `s`.`id` IN ('000000003', '000000001')




