const model = require("../models/get.model");

const methods = {
  async currentbalance(req, res) {
    try {
      const result = await model.CurrentBalance({
        id: 1,
      });
      res.success(result);
    } catch (error) {
      res.error(error);
    }
  },
  async transactionhistory(req, res) {
    try {
      const result = await model.TransactionHistory({
        id: 1,
      });
      res.success(result);
    } catch (error) {
      res.error(error);
    }
  },
};

module.exports = { ...methods };
