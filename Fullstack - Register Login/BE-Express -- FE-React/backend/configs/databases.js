const knex = require("knex");
const knex_config = require("../knexfile.js");
const app_config = require("./app.js");

const db = knex(knex_config[app_config.node_env]);

module.exports = db;
