const express = require("express");

module.exports = async (app) => {
  require("../configs/databases");

  app.use(express.json());
  app.use(express.urlencoded({ extended: false }));

  app.use(require("../configs/responseFormat"));
};
