create table short_links(
  `id` int auto_increment primary key,
  `url` varchar(500) not null,
  `token` varchar(15) binary not null,
   index(`token`),
   unique(`token`),
  `status` int not null default 0,/* 0-normal, 1-paused, 2-blocked, 3-expired */
  `time` bigint,
  `ip` bigint
) engine=myisam default charset=utf8;
