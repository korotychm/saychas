SELECT *
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'client_order'
    AND COLUMN_NAME = 'id'
    AND DATA_TYPE = 'int'
    AND COLUMN_DEFAULT IS NULL
    AND IS_NULLABLE = 'NO'
    AND EXTRA like '%auto_increment%';
