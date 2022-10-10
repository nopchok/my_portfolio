const model = require("../models/create.model");

const methods = {
  async newaccount(req, res) {
    try {
      const result = await model.newaccount({
        name: "aaaa",
        initial_deposit: 500,
      });
      res.success(result);
    } catch (error) {
      res.error(error);
    }
  },
  async transferamount(req, res) {
    try {
      const result = await model.transferamount({
        from: 1,
        to: 2,
        amount: 20,
        transaction_type: "withdraw",
        relate_transaction_type: "deposit",
      });
      res.success(result);
    } catch (error) {
      res.error(error);
    }
  },
};

module.exports = { ...methods };
