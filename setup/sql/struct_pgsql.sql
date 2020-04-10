DROP TABLE IF EXISTS "config";
CREATE TABLE "config" (
  "name" varchar(50) PRIMARY KEY,
  "value" text NOT NULL,
  "description" varchar(255),
  "protected" boolean NOT NULL DEFAULT false,
  "visible" boolean NOT NULL DEFAULT false
);

DROP TABLE IF EXISTS "asn1id";
CREATE TABLE "asn1id" (
  "lfd" serial PRIMARY KEY,
  "oid" varchar(255) NOT NULL,
  "name" varchar(255) NOT NULL,
  "standardized" boolean NOT NULL DEFAULT false,
  "well_known" boolean NOT NULL DEFAULT false
);

DROP TABLE IF EXISTS "iri";
CREATE TABLE "iri" (
  "lfd" serial PRIMARY KEY,
  "oid" varchar(255) NOT NULL,
  "name" varchar(255) NOT NULL,
  "longarc" boolean NOT NULL DEFAULT false,
  "well_known" boolean NOT NULL DEFAULT false
);

DROP TABLE IF EXISTS "objects";
CREATE TABLE "objects" (
  "id" varchar(255) PRIMARY KEY,
  "parent" varchar(255) DEFAULT NULL,
  "title" varchar(255) NOT NULL,
  "description" text NOT NULL,
  "ra_email" varchar(100) NULL,
  "confidential" boolean NOT NULL,
  "created" timestamp,
  "updated" timestamp,
  "comment" varchar(255) NULL
);

DROP TABLE IF EXISTS "ra";
CREATE TABLE "ra" (
  "ra_id" serial PRIMARY KEY,
  "email" varchar(100) NOT NULL,
  "ra_name" varchar(100) NOT NULL,
  "personal_name" varchar(100) NOT NULL,
  "organization" varchar(100) NOT NULL,
  "office" varchar(100) NOT NULL,
  "street" varchar(100) NOT NULL,
  "zip_town" varchar(100) NOT NULL,
  "country" varchar(100) NOT NULL,
  "phone" varchar(100) NOT NULL,
  "mobile" varchar(100) NOT NULL,
  "fax" varchar(100) NOT NULL,
  "privacy" boolean NOT NULL DEFAULT false,
  "salt" varchar(100) NOT NULL,
  "authkey" varchar(100) NOT NULL,
  "registered" timestamp,
  "updated" timestamp,
  "last_login" timestamp
);

DROP TABLE IF EXISTS "log";
CREATE TABLE "log" (
  "id" serial PRIMARY KEY,
  "unix_ts" bigint NOT NULL,
  "addr" varchar(45) NOT NULL,
  "event" text NOT NULL
);

DROP TABLE IF EXISTS "log_user";
CREATE TABLE "log_user" (
  "id" serial PRIMARY KEY,
  "log_id" integer NOT NULL,
  "username" varchar(255) NOT NULL
);

DROP TABLE IF EXISTS "log_object";
CREATE TABLE "log_object" (
  "id" serial PRIMARY KEY,
  "log_id" integer NOT NULL,
  "object" varchar(255) NOT NULL
);

DROP INDEX IF EXISTS "index_asn1id_uq_oid_name";
DROP INDEX IF EXISTS "index_iri_uq_oid_name";
DROP INDEX IF EXISTS "index_objects_fk_parent";
DROP INDEX IF EXISTS "index_ra_uq_email";

CREATE UNIQUE INDEX "index_asn1id_uq_oid_name" ON "asn1id"("oid","name");
CREATE UNIQUE INDEX "index_iri_uq_oid_name"    ON "iri"("oid","name");
CREATE        INDEX "index_objects_fk_parent"  ON "objects"("parent");
CREATE UNIQUE INDEX "index_ra_uq_email"        ON "ra"("email");

INSERT INTO "config" ("name", "description", "value", "protected", "visible") VALUES ('database_version', 'Version of the database tables', '202', true, false);