const fnc = {
  getCurrentBalance: async function (db, id) {
    let result = await db.select("CurrentBalance").from("BankAccount").where("Id", id);
    return result.length == 1 ? result[0].CurrentBalance : null;
  },
  count: async function (db, tbl, column, value) {
    let result = await db.raw(`select count(*) c from ${tbl} where ${column} = "${value}";`);
    return result[0].c;
  },
  getTransactionTypeId: async function (db, name) {
    let id = await db.raw(`SELECT Id FROM TransactionType WHERE Name = "${name}";`);
    if (id.length == 0) {
      id = await db
        .insert({
          Name: name,
        })
        .returning("Id")
        .into("TransactionType");
    }
    return id[0].Id;
  },
  getCustomerId: async function (db, name) {
    let id = await db.raw(`SELECT Id FROM Customer WHERE Name = "${name}";`);
    if (id.length == 0) {
      id = await db
        .insert({
          Name: name,
        })
        .returning("Id")
        .into("Customer");
    }
    return id[0].Id;
  },
  updateCurrentBalance: async function (db, id, amount) {
    amount = (amount < 0 ? "" : "+") + amount;
    await db
      .update({
        CurrentBalance: db.raw("?? " + amount, ["CurrentBalance"]),
      })
      .from("BankAccount")
      .where("Id", id);
  },
};

module.exports = { ...fnc };
