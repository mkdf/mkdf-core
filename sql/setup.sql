
create table user
(
    id                            int auto_increment
        primary key,
    email                         varchar(128) not null,
    full_name                     varchar(512) not null,
    password                      varchar(256) not null,
    status                        int          not null,
    date_created                  datetime     not null,
    pwd_reset_token               varchar(256)  null,
    pwd_reset_token_creation_date datetime     null,
    is_admin bool                 default false null,
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




