select pr.*, ss.id as store_id, ss.title as store_title from product pr left join (select s.* from store s left join provider p on p.id = s.provider_id where s.id in ('000000001','000000002','000000003','000000004','000000005') ) ss on ss.provider_id=pr.provider_id where ss.id is not null order by pr.id;

-- selects products that belong to specified providers
select pr.* from product pr where pr.provider_id in (select s.provider_id from store s left join provider p on p.id = s.provider_id where s.id in ('000000003', '000000001') );
