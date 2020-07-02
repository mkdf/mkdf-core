
create table if not exists user
(
    id                            int auto_increment
        primary key,
    email                         varchar(128) charset utf8 not null,
    full_name                     varchar(512) charset utf8 not null,
    password                      varchar(256) charset utf8 not null,
    status                        int                       not null,
    date_created                  datetime                  not null,
    pwd_reset_token               varchar(256) charset utf8 null,
    pwd_reset_token_creation_date datetime                  null,
    is_admin                      tinyint(1) default 0      null,
    constraint email
        unique (email)
);

# ROLES
create table if not exists role
(
    id          int           not null,
    description varchar(1028) null,
    constraint roles_id_uindex
        unique (id)
);

alter table role
    add primary key (id);

#Add three system/default roles
INSERT INTO role (id, description) VALUES (0, 'Dataset owner');
INSERT INTO role (id, description) VALUES (-1, 'Logged in');
INSERT INTO role (id, description) VALUES (-2, 'Anonymous');




