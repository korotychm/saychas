with recursive cte (id, name, parent_id) as (
  select     id,
             title,
             parent_id
  from       category
  where      parent_id = '000000003'
  union all
  select     p.id,
             p.title,
             p.parent_id
  from       category p
  inner join cte
          on p.parent_id = cte.id
)
select * from cte;
