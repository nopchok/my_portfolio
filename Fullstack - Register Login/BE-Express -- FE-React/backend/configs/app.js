require('dotenv').config()

module.exports = {
  port: process.env.PORT || 3000,
  node_env: process.env.NODE_ENV || "development",
  //isProduction: process.env.NODE_ENV === "production",
  isProduction: true,
  apiVersion: process.env.API_VERSION || 1,
};
