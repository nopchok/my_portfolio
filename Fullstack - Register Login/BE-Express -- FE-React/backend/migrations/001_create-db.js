/**
 * @param { import("knex").Knex } knex
 * @returns { Promise<void> }
 */

const createTablesSafely = (knex) => (tables) => {
  const createTables = tables.map(({ name, schema }) => {
    return knex.schema.createTable(name, schema);
  });

  return Promise.all(createTables).catch((e) => {
    const dropTables = tables.map(({ name }) => {
      return knex.schema.dropTableIfExists(name);
    });

    return Promise.all(dropTables).then(() => Promise.reject(e));
  });
};

exports.up = function (knex) {
  return createTablesSafely(knex)([
    {
      name: "users",
      schema(tbl) {
        tbl.increments("Id").unsigned().primary();
        tbl.string("email").unique().notNullable();
        tbl.string("password").notNullable();
        tbl.timestamps(true, true);
      },
    },
  ]);
};

/**
 * @param { import("knex").Knex } knex
 * @returns { Promise<void> }
 */
exports.down = function (knex) { };
