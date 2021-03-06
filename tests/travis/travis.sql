/* Standard user */
INSERT INTO  `users` (
`username` ,
`password` ,
`salt` ,
`email` ,
`type`
)
VALUES (
'testuser',
'6df938a7ed2725c9327ebd44bd3ae281',
'YnAZwgUGSP9WreOD',
'test@example.com',
'1'
);
/* Admin user */
INSERT INTO  `users` (
`username` ,
`password` ,
`salt` ,
`email` ,
`type`
)
VALUES (
'adminuser',
'94feaf586c0d13d9359b7192ec1d6143',
'F8udsbhmtcx4gMqW',
'admin@example.com',
'2'
);
/*S3 Storage*/
INSERT INTO `storage` (
	`id`, 
	`type`, 
	`endpoint`, 
	`access_key`, 
	`secret_key`, 
	`bucket`
) 
VALUES (
	2, 
	's3',  
	'objects.liquidweb.services', 
	'7EFHD5Q4X9LZCQJAGNXQ', 
	'5cnNywDvhOtUmKB+ZnWqSRj/flYGWr/7Xyhhe2eP', 
	'jvodev'
);