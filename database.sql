-- on_users definition

CREATE TABLE "on_users" (
  "id" INTEGER NOT NULL  PRIMARY KEY AUTOINCREMENT,
  "username" TEXT(64) NOT NULL,
  "password" TEXT(256) NOT NULL,
  "email" TEXT(256) NOT NULL,
  "category_open" TEXT(10),
  "link_open" TEXT(10), 
  "themes" TEXT(10) DEFAULT 'default' NOT NULL,
  "pin" TEXT(10) NOT NULL,
  "is_admin" INTEGER DEFAULT 0 NOT NULL,
  "all_open" TEXT(10),
  "add_time" TEXT(10),
  "up_time" TEXT(10),
  CONSTRAINT "username" UNIQUE ("username")
);

-- on_categorys definition

CREATE TABLE "on_categorys" (
  "id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  "name" TEXT(32) NOT NULL,
  "priority" INTEGER NULL DEFAULT 0,
  "ispublic" TEXT(128) DEFAULT '', 
  "uid" INTEGER,
  "add_time" TEXT(10),
  "up_time" TEXT(10),
  CONSTRAINT "unique_name_uid" UNIQUE ("name", "uid")
);

-- on_links definition

CREATE TABLE "on_links" (
  "id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  "fid" INTEGER NOT NULL,
  "title" TEXT(64) NOT NULL,
  "url" TEXT(256) NOT NULL,
  "description" TEXT(256),
  "priority" INTEGER DEFAULT 0,
  "ispublic" TEXT(256),
  "favicon_image" TEXT(512),
  "uid" INTEGER,
  "favicon" TEXT(256),
  "add_time" TEXT(10),
  "up_time" TEXT(10),
  CONSTRAINT "unique_url_uid" UNIQUE ("url", "uid")
);