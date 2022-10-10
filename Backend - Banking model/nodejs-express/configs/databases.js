const knex = require("knex");
const knex_config = require("../knexfile.js");
const app_config = require("./app.js");

const db = knex(knex_config[app_config.node_env]);

module.exports = db;

/*
module.exports = {
  find,
  findById,
  insert,
  update,
  remove,
};

function find() {
  return db("cars");
}

function findById(id) {
  return db("cars").where({ id: Number(id) });
}

function insert(post) {
  return db("cars")
    .insert(post)
    .then((id) => ({ id: id[0] }));
}

function update(id, post) {
  return db("cars")
    .where({ id: Number(id) })
    .update(post);
}

function remove(id) {
  return db("cars")
    .where({ id: Number(id) })
    .del();
}
*/
