-- L2M (Layer 2 Manager): create.pgsql.sql 2011/06/05
--
-- If you are installing L2M for the first time, you need make sure
-- you have a postgresql user and database to L2M. Bellow is a example
-- in how create the database and user.
--
--  ~# su - postgres -c "createuser l2musr -S -D -R -P"
--  ~# su - postgres -c "createdb l2mdb -O l2musr"
--  ~# su - postgres -c "psql -c \"GRANT ALL PRIVILEGES ON database l2mdb TO l2musr;\""

BEGIN;

CREATE TABLE users (
  id SERIAL NOT NULL UNIQUE,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(50) NOT NULL,
  admin BOOLEAN NOT NULL DEFAULT False,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id)
);


COMMIT;
