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
  // return knex.schema.createTable("Customer", (tbl) => {
  //   tbl.increments("Id");
  //   tbl.text("Name", 255).unique().notNullable();
  //   tbl.timestamps(true, true);
  // });
  return createTablesSafely(knex)([
    {
      name: "TransactionType",
      schema(tbl) {
        tbl.increments("Id").unsigned().primary();
        tbl.string("Name").unique().notNullable();
        tbl.timestamps(true, true);
      },
    },
    {
      name: "Customer",
      schema(tbl) {
        tbl.increments("Id").unsigned().primary();
        tbl.string("Name").unique().notNullable();
        tbl.timestamps(true, true);
      },
    },
    {
      name: "BankAccount",
      schema(tbl) {
        tbl.increments("Id").unsigned().primary();
        tbl.decimal("CurrentBalance", 20, 2).notNullable();
        tbl.integer("CustomerId").unsigned().notNullable();
        tbl.timestamps(true, true);

        tbl.foreign("CustomerId").references("Id").inTable("Customer");
      },
    },
    {
      name: "TransactionHistory",
      schema(tbl) {
        tbl.increments("Id").unsigned().primary();
        tbl.integer("TransactionTypeId").notNullable();
        tbl.integer("BankAccountId").unsigned().notNullable();
        tbl.decimal("Amount", 20, 2).notNullable();
        tbl.integer("RelateBankAccountId").unsigned().nullable();
        tbl.timestamps(true, true);

        tbl.foreign("BankAccountId").references("Id").inTable("BankAccount");
        tbl.foreign("RelateBankAccountId").references("Id").inTable("BankAccount");
        tbl.foreign("TransactionTypeId").references("Id").inTable("TransactionType");
      },
    },
  ]);
};

/**
 * @param { import("knex").Knex } knex
 * @returns { Promise<void> }
 */
exports.down = function (knex) {};
